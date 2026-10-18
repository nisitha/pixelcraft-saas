<?php

namespace Tests\Feature;

use App\Filament\App\Resources\Invoices\Pages\ViewInvoice;
use App\Filament\App\Resources\Quotations\Pages\ViewQuotation;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\User;
use App\Services\PdfService;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Tests\TestCase;

class ZohoSplitViewAndEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;

    protected User $user;

    protected Contact $contact;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->company = Company::where('slug', 'lanka-solar-solutions')->first();
        $this->user = User::where('email', 'owner@lankasolar.lk')->first();
        $this->contact = Contact::where('company_id', $this->company->id)->first();

        // Update contact with Sri Lankan mobile format starting with 07
        $this->contact->update([
            'phone' => '077 123 4567',
            'email' => 'customer@example.lk',
            'ceb_account_no' => 'CEB-9988776655',
        ]);

        $this->actingAs($this->user);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($this->company);
    }

    public function test_sidebar_css_and_high_contrast_rules_exist_in_panel_provider(): void
    {
        $panel = Filament::getPanel('app');
        $this->assertNotNull($panel);

        // Verify custom CSS file or injected styles
        $providerContent = file_get_contents(app_path('Providers/Filament/AppPanelProvider.php'));
        $this->assertStringContainsString('.fi-sidebar-item-label', $providerContent);
        $this->assertStringContainsString('#f8fafc', $providerContent);
        $this->assertStringContainsString('#3b82f6', $providerContent);
    }

    public function test_quotations_and_invoices_have_is_sent_and_sent_at_attributes(): void
    {
        $quotation = Quotation::create([
            'company_id' => $this->company->id,
            'contact_id' => $this->contact->id,
            'quote_number' => 'QUO-SENT-TEST',
            'quote_date' => now()->toDateString(),
            'valid_until' => now()->addDays(30)->toDateString(),
            'subtotal' => 100000.00,
            'discount' => 0.00,
            'tax' => 0.00,
            'grand_total' => 100000.00,
            'status' => 'draft',
            'is_sent' => false,
        ]);

        $this->assertFalse((bool) $quotation->is_sent);
        $this->assertNull($quotation->sent_at);

        $quotation->update([
            'is_sent' => true,
            'sent_at' => now(),
            'status' => 'sent',
        ]);

        $this->assertTrue((bool) $quotation->fresh()->is_sent);
        $this->assertNotNull($quotation->fresh()->sent_at);
        $this->assertEquals('sent', $quotation->fresh()->status);
    }

    public function test_zoho_split_view_quotation_mount_and_workflow_actions(): void
    {
        $product = Product::where('company_id', $this->company->id)->first();

        $quotation = Quotation::create([
            'company_id' => $this->company->id,
            'contact_id' => $this->contact->id,
            'quote_number' => 'QUO-SPLIT-01',
            'quote_date' => now()->toDateString(),
            'valid_until' => now()->addDays(30)->toDateString(),
            'system_kw' => 10.00,
            'phase_type' => 'three',
            'subtotal' => 200000.00,
            'discount' => 10000.00,
            'tax' => 5000.00,
            'grand_total' => 195000.00,
            'status' => 'draft',
            'is_sent' => false,
        ]);

        QuotationItem::create([
            'quotation_id' => $quotation->id,
            'product_id' => $product->id,
            'description' => 'Solar Panels Batch',
            'quantity' => 10,
            'unit_price' => 20000.00,
            'subtotal' => 200000.00,
        ]);

        Livewire::test(ViewQuotation::class, ['record' => $quotation->id])
            ->assertSuccessful()
            ->assertSee('QUO-SPLIT-01')
            ->assertSee("WHAT'S NEXT?", false)
            ->call('markAsSent');

        $this->assertTrue((bool) $quotation->fresh()->is_sent);
        $this->assertEquals('sent', $quotation->fresh()->status);

        // Test unlocking
        Livewire::test(ViewQuotation::class, ['record' => $quotation->id])
            ->call('unlockForEditing');

        $this->assertFalse((bool) $quotation->fresh()->is_sent);
    }

    public function test_whatsapp_share_url_formats_sri_lankan_phone(): void
    {
        $quotation = Quotation::create([
            'company_id' => $this->company->id,
            'contact_id' => $this->contact->id,
            'quote_number' => 'QUO-WA-01',
            'quote_date' => now()->toDateString(),
            'subtotal' => 50000.00,
            'grand_total' => 50000.00,
            'status' => 'draft',
        ]);

        $component = Livewire::test(ViewQuotation::class, ['record' => $quotation->id]);
        $waUrl = $component->get('whatsAppUrl');

        // 077 123 4567 should be formatted as 94771234567
        $this->assertStringContainsString('wa.me/94771234567', $waUrl);
        $this->assertStringContainsString('QUO-WA-01', $waUrl);
        $this->assertStringContainsString('50,000.00', urldecode($waUrl));
    }

    public function test_signed_public_document_portal(): void
    {
        $quotation = Quotation::where('quote_number', 'QUO-2026-0001')->first();
        $this->assertNotNull($quotation);

        $validSignedUrl = URL::signedRoute('public.document.show', [
            'type' => 'quotation',
            'id' => $quotation->id,
        ]);

        // Accessing with valid signature should succeed (publicly without authentication)
        auth()->logout();

        $response = $this->get($validSignedUrl);
        $response->assertSuccessful();
        $response->assertSee($quotation->quote_number);
        $response->assertSee('Verified Official Document');

        // Accessing with invalid signature must be rejected (403)
        $tamperedUrl = $validSignedUrl.'&tampered=1';
        $tamperedResponse = $this->get($tamperedUrl);
        $tamperedResponse->assertStatus(403);

        // Test public PDF download route
        $downloadUrl = URL::signedRoute('public.document.download', [
            'type' => 'quotation',
            'id' => $quotation->id,
        ]);

        $downloadResponse = $this->get($downloadUrl);
        $downloadResponse->assertSuccessful();
        $downloadResponse->assertHeader('content-type', 'application/pdf');
    }

    public function test_zoho_split_view_invoice_payment_recording(): void
    {
        $invoice = Invoice::create([
            'company_id' => $this->company->id,
            'contact_id' => $this->contact->id,
            'invoice_number' => 'INV-PAY-01',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(14)->toDateString(),
            'type' => 'milestone',
            'subtotal' => 100000.00,
            'discount' => 0.00,
            'tax' => 0.00,
            'grand_total' => 100000.00,
            'paid_amount' => 0.00,
            'due_amount' => 100000.00,
            'status' => 'unpaid',
            'is_sent' => false,
        ]);

        Livewire::test(ViewInvoice::class, ['record' => $invoice->id])
            ->assertSuccessful()
            ->call('openPaymentModal')
            ->assertSet('showPaymentModal', true)
            ->set('paymentAmount', 60000.00)
            ->set('paymentMethod', 'bank_transfer')
            ->set('paymentReference', 'BOC-TX-9921')
            ->call('submitPayment')
            ->assertSet('showPaymentModal', false);

        $fresh = $invoice->fresh();
        $this->assertEquals(60000.00, (float) $fresh->paid_amount);
        $this->assertEquals(40000.00, (float) $fresh->due_amount);
        $this->assertEquals('partial', $fresh->status);

        // Record remaining payment
        Livewire::test(ViewInvoice::class, ['record' => $invoice->id])
            ->call('openPaymentModal')
            ->set('paymentAmount', 40000.00)
            ->call('submitPayment');

        $fullyPaid = $invoice->fresh();
        $this->assertEquals(100000.00, (float) $fullyPaid->paid_amount);
        $this->assertEquals(0.00, (float) $fullyPaid->due_amount);
        $this->assertEquals('paid', $fullyPaid->status);
    }

    public function test_advanced_company_branding_applied_to_pdf_generation(): void
    {
        // Update company branding settings
        $this->company->update([
            'settings' => array_merge($this->company->settings ?? [], [
                'primary_color' => '#1d4ed8',
                'table_header_color' => '#1e1b4b',
                'text_color' => '#ffffff',
                'show_solar_specs' => true,
                'show_ceb_account' => true,
                'show_milestones' => true,
                'bank_details' => "DFCC Bank - Giriulla Branch\nAcc No: 101001490231",
            ]),
        ]);

        $quotation = Quotation::where('quote_number', 'QUO-2026-0001')->first();
        $pdfService = app(PdfService::class);
        $pdf = $pdfService->generateQuotationPdf($quotation);
        $output = $pdf->output();

        $this->assertNotEmpty($output);
        $this->assertStringStartsWith('%PDF-', $output);

        $invoice = Invoice::where('invoice_number', 'INV-2026-0001')->first();
        $invoicePdf = $pdfService->generateInvoicePdf($invoice);
        $invoiceOutput = $invoicePdf->output();

        $this->assertNotEmpty($invoiceOutput);
        $this->assertStringStartsWith('%PDF-', $invoiceOutput);
    }
}

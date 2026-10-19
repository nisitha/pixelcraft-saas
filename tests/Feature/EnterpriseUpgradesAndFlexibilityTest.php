<?php

namespace Tests\Feature;

use App\Filament\App\Pages\PosTerminal;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\User;
use App\Services\PdfService;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EnterpriseUpgradesAndFlexibilityTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        $this->company = Company::create([
            'name' => 'Vertex Enterprise (Pvt) Ltd',
            'slug' => 'vertex-enterprise',
            'is_active' => true,
            'settings' => [
                'inventory_enabled' => true,
                'allow_out_of_stock_invoicing' => true,
                'enable_pos_module' => true,
                'enable_solar_specs' => true,
                'primary_color' => '#047857',
            ],
        ]);

        $this->company->users()->attach($this->user, ['role' => 'admin']);
        Filament::setTenant($this->company);
    }

    public function test_company_implements_has_avatar_and_returns_public_url(): void
    {
        Storage::fake('public');

        $this->assertNull($this->company->getFilamentAvatarUrl());

        $this->company->update(['logo_path' => 'company-logos/test-logo.png']);
        Storage::disk('public')->put('company-logos/test-logo.png', 'fake image content');

        $avatarUrl = $this->company->getFilamentAvatarUrl();
        $this->assertNotNull($avatarUrl);
        $this->assertStringContainsString('company-logos/test-logo.png', $avatarUrl);
    }

    public function test_product_track_inventory_attribute_and_stock_deduction_bypass(): void
    {
        $contact = Contact::create([
            'company_id' => $this->company->id,
            'name' => 'Solar Customer',
        ]);

        $trackingProduct = Product::create([
            'company_id' => $this->company->id,
            'name' => 'Inverter 5kW',
            'type' => 'product',
            'stock_qty' => 10.00,
            'track_inventory' => true,
            'unit_price' => 250000.00,
        ]);

        $nonTrackingProduct = Product::create([
            'company_id' => $this->company->id,
            'name' => 'Consulting & Engineering Labor',
            'type' => 'product',
            'stock_qty' => 50.00,
            'track_inventory' => false,
            'unit_price' => 45000.00,
        ]);

        $invoice = Invoice::create([
            'company_id' => $this->company->id,
            'contact_id' => $contact->id,
            'invoice_number' => 'INV-TEST-001',
            'invoice_date' => now()->toDateString(),
            'status' => 'unpaid',
            'type' => 'standard',
            'grand_total' => 295000.00,
            'due_amount' => 295000.00,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'product_id' => $trackingProduct->id,
            'description' => 'Inverter 5kW',
            'quantity' => 2.00,
            'unit_price' => 250000.00,
            'subtotal' => 500000.00,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'product_id' => $nonTrackingProduct->id,
            'description' => 'Engineering Labor',
            'quantity' => 5.00,
            'unit_price' => 45000.00,
            'subtotal' => 225000.00,
        ]);

        $trackingProduct->refresh();
        $nonTrackingProduct->refresh();

        $this->assertEquals(8.00, (float) $trackingProduct->stock_qty);
        $this->assertEquals(50.00, (float) $nonTrackingProduct->stock_qty);
    }

    public function test_inventory_disabled_company_does_not_deduct_stock(): void
    {
        $serviceCompany = Company::create([
            'name' => 'Vertex IT Consulting',
            'slug' => 'vertex-consulting',
            'is_active' => true,
            'settings' => [
                'inventory_enabled' => false,
            ],
        ]);

        $contact = Contact::create([
            'company_id' => $serviceCompany->id,
            'name' => 'Corporate Client',
        ]);

        $product = Product::create([
            'company_id' => $serviceCompany->id,
            'name' => 'Software Support',
            'type' => 'product',
            'stock_qty' => 100.00,
            'track_inventory' => true,
            'unit_price' => 20000.00,
        ]);

        $invoice = Invoice::create([
            'company_id' => $serviceCompany->id,
            'contact_id' => $contact->id,
            'invoice_number' => 'INV-SRV-001',
            'invoice_date' => now()->toDateString(),
            'status' => 'unpaid',
            'type' => 'standard',
            'grand_total' => 20000.00,
            'due_amount' => 20000.00,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'product_id' => $product->id,
            'description' => 'Software Support Hours',
            'quantity' => 10.00,
            'unit_price' => 20000.00,
            'subtotal' => 200000.00,
        ]);

        $product->refresh();
        $this->assertEquals(100.00, (float) $product->stock_qty);
    }

    public function test_free_text_line_items_without_product_id(): void
    {
        $contact = Contact::create([
            'company_id' => $this->company->id,
            'name' => 'Custom Project Client',
        ]);

        $quotation = Quotation::create([
            'company_id' => $this->company->id,
            'contact_id' => $contact->id,
            'quote_number' => 'QUO-AD-HOC-1',
            'quote_date' => now()->toDateString(),
            'status' => 'draft',
            'grand_total' => 85000.00,
        ]);

        $quoteItem = QuotationItem::create([
            'quotation_id' => $quotation->id,
            'product_id' => null,
            'description' => 'Custom CNC Fabricated Roof Mounting Brackets',
            'quantity' => 4.00,
            'unit_price' => 21250.00,
            'subtotal' => 85000.00,
        ]);

        $this->assertNull($quoteItem->product_id);
        $this->assertEquals('Custom CNC Fabricated Roof Mounting Brackets', $quoteItem->description);
    }

    public function test_generate_delivery_note_pdf_succeeds_without_prices(): void
    {
        $contact = Contact::create([
            'company_id' => $this->company->id,
            'name' => 'Kamal Silva',
            'address' => 'No. 12, Kandy Road, Kadawatha',
            'phone' => '0777123456',
        ]);

        $product = Product::create([
            'company_id' => $this->company->id,
            'name' => 'Trina Solar 550W Module',
            'sku' => 'TS-550',
            'unit_price' => 48000.00,
            'warranty_months' => 300,
        ]);

        $invoice = Invoice::create([
            'company_id' => $this->company->id,
            'contact_id' => $contact->id,
            'invoice_number' => 'INV-DN-001',
            'invoice_date' => now()->toDateString(),
            'status' => 'unpaid',
            'type' => 'standard',
            'grand_total' => 480000.00,
            'due_amount' => 480000.00,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'product_id' => $product->id,
            'description' => 'Trina Solar 550W Bifacial PV Panel',
            'quantity' => 10.00,
            'unit_price' => 48000.00,
            'subtotal' => 480000.00,
        ]);

        $pdfService = app(PdfService::class);
        $pdf = $pdfService->generateDeliveryNotePdf($invoice, 'WP-CAD-1234', 'Sunil Perera');

        $output = $pdf->output();
        $this->assertNotEmpty($output);
        $this->assertStringStartsWith('%PDF', $output);
    }

    public function test_generate_payment_receipt_pdf_succeeds(): void
    {
        $contact = Contact::create([
            'company_id' => $this->company->id,
            'name' => 'Nimal Jayawardena',
            'phone' => '0712345678',
        ]);

        $invoice = Invoice::create([
            'company_id' => $this->company->id,
            'contact_id' => $contact->id,
            'invoice_number' => 'INV-PAY-001',
            'invoice_date' => now()->toDateString(),
            'status' => 'partial',
            'type' => 'milestone',
            'grand_total' => 1000000.00,
            'paid_amount' => 600000.00,
            'due_amount' => 400000.00,
        ]);

        $payment = Payment::create([
            'company_id' => $this->company->id,
            'invoice_id' => $invoice->id,
            'amount' => 600000.00,
            'payment_date' => now()->toDateString(),
            'method' => 'bank_transfer',
            'reference_no' => 'BOC-TXN-998822',
            'milestone_label' => '60% Advance Payment on Project Signing',
            'notes' => 'Direct wire transfer received in BOC account.',
        ]);

        $pdfService = app(PdfService::class);
        $pdf = $pdfService->generatePaymentReceiptPdf($payment);

        $output = $pdf->output();
        $this->assertNotEmpty($output);
        $this->assertStringStartsWith('%PDF', $output);
    }

    public function test_quotation_and_invoice_can_be_duplicated(): void
    {
        $contact = Contact::create([
            'company_id' => $this->company->id,
            'name' => 'Original Customer',
        ]);

        $quotation = Quotation::create([
            'company_id' => $this->company->id,
            'contact_id' => $contact->id,
            'quote_number' => 'QUO-ORIGINAL-01',
            'quote_date' => now()->toDateString(),
            'status' => 'sent',
            'system_kw' => 10.00,
            'phase_type' => 'three',
            'grand_total' => 1500000.00,
        ]);

        QuotationItem::create([
            'quotation_id' => $quotation->id,
            'description' => 'Solar Equipment Package',
            'quantity' => 1,
            'unit_price' => 1500000.00,
            'subtotal' => 1500000.00,
        ]);

        $newNumber = 'QUO-'.date('Y').'-'.str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $duplicateQuote = Quotation::create([
            'company_id' => $quotation->company_id,
            'contact_id' => $quotation->contact_id,
            'quote_number' => $newNumber,
            'quote_date' => now()->toDateString(),
            'valid_until' => now()->addDays(30)->toDateString(),
            'status' => 'draft',
            'system_kw' => $quotation->system_kw,
            'phase_type' => $quotation->phase_type,
            'grand_total' => $quotation->grand_total,
        ]);

        foreach ($quotation->items as $item) {
            $duplicateQuote->items()->create([
                'product_id' => $item->product_id,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'subtotal' => $item->subtotal,
            ]);
        }

        $this->assertEquals('draft', $duplicateQuote->status);
        $this->assertEquals(1, $duplicateQuote->items()->count());
        $this->assertEquals(1500000.00, (float) $duplicateQuote->grand_total);
    }

    public function test_pos_terminal_respects_allow_out_of_stock_invoicing_toggle(): void
    {
        $product = Product::create([
            'company_id' => $this->company->id,
            'name' => 'Zero Stock Product',
            'type' => 'product',
            'stock_qty' => 0.00,
            'track_inventory' => true,
            'unit_price' => 5000.00,
        ]);

        // When allow_out_of_stock_invoicing is true (default)
        $pos = new PosTerminal;
        $pos->mount();
        $pos->addToCart($product->id);
        $this->assertArrayHasKey($product->id, $pos->cart);

        // When allow_out_of_stock_invoicing is disabled
        $this->company->update([
            'settings' => array_merge($this->company->settings ?? [], [
                'allow_out_of_stock_invoicing' => false,
            ]),
        ]);

        $pos2 = new PosTerminal;
        $pos2->mount();
        $pos2->addToCart($product->id);
        $this->assertArrayNotHasKey($product->id, $pos2->cart);
    }
}

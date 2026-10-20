<?php

namespace Tests\Feature;

use App\Filament\App\Resources\Invoices\Pages\CreateInvoice;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InvoicePaymentSettlementDirectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_creating_invoice_with_fully_paid_settlement_creates_payment_record_and_marks_paid(): void
    {
        $company = Company::where('slug', 'lanka-solar-solutions')->firstOrFail();
        $owner = User::where('email', 'owner@lankasolar.lk')->firstOrFail();
        $contact = Contact::where('company_id', $company->id)->firstOrFail();
        $product = Product::where('company_id', $company->id)->firstOrFail();

        $this->actingAs($owner);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($company);

        $invNum = 'INV-TEST-FULL-001';

        $component = Livewire::test(CreateInvoice::class)
            ->set('data.contact_id', $contact->id)
            ->set('data.invoice_number', $invNum)
            ->set('data.items', [
                [
                    'product_id' => $product->id,
                    'description' => $product->name,
                    'warranty_text' => '10 Years',
                    'quantity' => 2,
                    'cost_price' => 10000.00,
                    'unit_price' => 25000.00,
                    'discount_rate' => 0.00,
                    'tax' => 0.00,
                    'subtotal' => 50000.00,
                    'hide_on_customer_pdf' => false,
                ],
            ])
            ->call('recalculateZohoTotals')
            ->call('setSettlementStatus', 'fully_paid')
            ->set('data.payment_method', 'bank_transfer')
            ->set('data.payment_reference', 'HNB-TRX-FULL-889')
            ->call('create');

        $component->assertHasNoErrors();

        $invoice = Invoice::where('invoice_number', $invNum)->firstOrFail();
        $this->assertEquals(50000.00, (float) $invoice->grand_total);
        $this->assertEquals(50000.00, (float) $invoice->paid_amount);
        $this->assertEquals(0.00, (float) $invoice->due_amount);
        $this->assertSame('paid', $invoice->status);

        $payment = Payment::where('invoice_id', $invoice->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals(50000.00, (float) $payment->amount);
        $this->assertSame('bank_transfer', $payment->method);
        $this->assertSame('HNB-TRX-FULL-889', $payment->reference_no);
        $this->assertSame('Full Settlement', $payment->milestone_label);
    }

    public function test_creating_invoice_with_partial_advance_settlement_creates_payment_record_and_marks_partial(): void
    {
        $company = Company::where('slug', 'lanka-solar-solutions')->firstOrFail();
        $owner = User::where('email', 'owner@lankasolar.lk')->firstOrFail();
        $contact = Contact::where('company_id', $company->id)->firstOrFail();
        $product = Product::where('company_id', $company->id)->firstOrFail();

        $this->actingAs($owner);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($company);

        $invNum = 'INV-TEST-PARTIAL-002';

        $component = Livewire::test(CreateInvoice::class)
            ->set('data.contact_id', $contact->id)
            ->set('data.invoice_number', $invNum)
            ->set('data.items', [
                [
                    'product_id' => $product->id,
                    'description' => $product->name,
                    'warranty_text' => '10 Years',
                    'quantity' => 1,
                    'cost_price' => 50000.00,
                    'unit_price' => 100000.00,
                    'discount_rate' => 0.00,
                    'tax' => 0.00,
                    'subtotal' => 100000.00,
                    'hide_on_customer_pdf' => false,
                ],
            ])
            ->call('recalculateZohoTotals')
            ->call('setSettlementStatus', 'partial')
            ->set('data.paid_amount', 60000.00)
            ->set('data.payment_method', 'cash')
            ->set('data.payment_reference', 'CASH-ADVANCE-01')
            ->set('data.payment_milestone_label', '60% Advance Confirmation')
            ->call('recalculateZohoTotals')
            ->call('create');

        $component->assertHasNoErrors();

        $invoice = Invoice::where('invoice_number', $invNum)->firstOrFail();
        $this->assertEquals(100000.00, (float) $invoice->grand_total);
        $this->assertEquals(60000.00, (float) $invoice->paid_amount);
        $this->assertEquals(40000.00, (float) $invoice->due_amount);
        $this->assertSame('partial', $invoice->status);

        $payment = Payment::where('invoice_id', $invoice->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals(60000.00, (float) $payment->amount);
        $this->assertSame('cash', $payment->method);
        $this->assertSame('CASH-ADVANCE-01', $payment->reference_no);
        $this->assertSame('60% Advance Confirmation', $payment->milestone_label);
    }

    public function test_creating_invoice_with_unpaid_settlement_creates_no_payment_and_marks_unpaid(): void
    {
        $company = Company::where('slug', 'lanka-solar-solutions')->firstOrFail();
        $owner = User::where('email', 'owner@lankasolar.lk')->firstOrFail();
        $contact = Contact::where('company_id', $company->id)->firstOrFail();
        $product = Product::where('company_id', $company->id)->firstOrFail();

        $this->actingAs($owner);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($company);

        $invNum = 'INV-TEST-UNPAID-003';

        $component = Livewire::test(CreateInvoice::class)
            ->set('data.contact_id', $contact->id)
            ->set('data.invoice_number', $invNum)
            ->set('data.items', [
                [
                    'product_id' => $product->id,
                    'description' => $product->name,
                    'warranty_text' => '5 Years',
                    'quantity' => 1,
                    'cost_price' => 10000.00,
                    'unit_price' => 20000.00,
                    'discount_rate' => 0.00,
                    'tax' => 0.00,
                    'subtotal' => 20000.00,
                    'hide_on_customer_pdf' => false,
                ],
            ])
            ->call('recalculateZohoTotals')
            ->call('setSettlementStatus', 'unpaid')
            ->call('create');

        $component->assertHasNoErrors();

        $invoice = Invoice::where('invoice_number', $invNum)->firstOrFail();
        $this->assertEquals(20000.00, (float) $invoice->grand_total);
        $this->assertEquals(0.00, (float) $invoice->paid_amount);
        $this->assertEquals(20000.00, (float) $invoice->due_amount);
        $this->assertSame('unpaid', $invoice->status);

        $paymentCount = Payment::where('invoice_id', $invoice->id)->count();
        $this->assertSame(0, $paymentCount);
    }
}

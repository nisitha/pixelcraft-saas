<?php

namespace Tests\Feature;

use App\Filament\App\Pages\FinancialReports;
use App\Filament\App\Pages\PosTerminal;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Models\User;
use App\Policies\ExpensePolicy;
use App\Policies\InvoicePolicy;
use App\Policies\PaymentPolicy;
use App\Policies\ProductPolicy;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class Phase3PosInventoryAccountingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_product_serial_creation_and_warranty_lookup(): void
    {
        $company = Company::where('slug', 'lanka-solar-solutions')->first();
        $product = Product::where('company_id', $company->id)->first();
        $customer = Contact::where('company_id', $company->id)->first();

        $serial = ProductSerial::create([
            'company_id' => $company->id,
            'product_id' => $product->id,
            'contact_id' => $customer->id,
            'serial_number' => 'SN-HUA-2026-9988',
            'warranty_months' => 60,
            'warranty_expires_at' => now()->addMonths(60)->toDateString(),
            'status' => 'sold',
        ]);

        $this->assertNotNull($serial);
        $this->assertTrue($serial->isWarrantyActive());
        $this->assertEquals('SN-HUA-2026-9988', $serial->serial_number);

        // Lookup by serial number
        $found = ProductSerial::where('company_id', $company->id)
            ->where('serial_number', 'SN-HUA-2026-9988')
            ->first();
        $this->assertNotNull($found);
        $this->assertEquals($customer->id, $found->contact_id);

        // Lookup by customer phone
        $foundByPhone = ProductSerial::where('company_id', $company->id)
            ->whereHas('contact', fn ($q) => $q->where('phone', $customer->phone))
            ->first();
        $this->assertNotNull($foundByPhone);
    }

    public function test_inventory_automatic_deduction_and_restoration(): void
    {
        $company = Company::where('slug', 'lanka-solar-solutions')->first();
        $customer = Contact::where('company_id', $company->id)->first();

        $product = Product::create([
            'company_id' => $company->id,
            'name' => '100Ah Lithium Solar Battery',
            'sku' => 'BAT-LITH-100AH',
            'type' => 'product',
            'unit_price' => 120000.00,
            'cost_price' => 95000.00,
            'stock_qty' => 50.00,
            'alert_qty' => 5.00,
        ]);

        // 1. Create Invoice with 5 items -> stock should decrement to 45
        $invoice = Invoice::create([
            'company_id' => $company->id,
            'contact_id' => $customer->id,
            'invoice_number' => 'INV-STOCK-TEST-01',
            'invoice_date' => now()->toDateString(),
            'grand_total' => 600000.00,
            'status' => 'unpaid',
        ]);

        $invoice->items()->create([
            'product_id' => $product->id,
            'description' => '100Ah Lithium Solar Battery',
            'quantity' => 5,
            'unit_price' => 120000.00,
            'subtotal' => 600000.00,
        ]);

        $product->refresh();
        $this->assertEquals(45.00, (float) $product->stock_qty);

        // 2. Cancel Invoice -> stock should increment back to 50
        $invoice->update(['status' => 'cancelled']);

        $product->refresh();
        $this->assertEquals(50.00, (float) $product->stock_qty);

        // Check AuditLog recorded cancellation
        $log = AuditLog::where('model_id', $invoice->id)
            ->where('action', 'cancelled')
            ->first();
        $this->assertNotNull($log);
    }

    public function test_pos_terminal_checkout_and_sales_receipt(): void
    {
        $company = Company::where('slug', 'lanka-solar-solutions')->first();
        $user = User::where('email', 'owner@lankasolar.lk')->first();
        $this->actingAs($user);

        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($company);

        $product = Product::where('company_id', $company->id)->where('type', 'product')->first();
        $initialStock = (float) $product->stock_qty;

        $component = Livewire::test(PosTerminal::class)
            ->assertSuccessful()
            ->call('addToCart', $product->id)
            ->set('discount', 500.00)
            ->set('paymentMethod', 'cash')
            ->call('completeSale');

        $component->assertHasNoErrors();

        // Verify stock deducted
        $product->refresh();
        $this->assertEquals($initialStock - 1, (float) $product->stock_qty);

        // Verify Invoice created with type 'pos' and status 'paid'
        $invoice = Invoice::where('company_id', $company->id)
            ->where('type', 'pos')
            ->latest('id')
            ->first();

        $this->assertNotNull($invoice);
        $this->assertEquals('paid', $invoice->status);
        $this->assertEquals(0.00, (float) $invoice->due_amount);
        $this->assertGreaterThan(0, (float) $invoice->paid_amount);

        // Verify Payment record created
        $payment = Payment::where('invoice_id', $invoice->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals('cash', $payment->method);
    }

    public function test_accounting_financial_reports_pnl_accuracy(): void
    {
        $company = Company::where('slug', 'lanka-solar-solutions')->first();
        $user = User::where('email', 'owner@lankasolar.lk')->first();
        $this->actingAs($user);

        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($company);

        $component = Livewire::test(FinancialReports::class)
            ->assertSuccessful();

        $invoicedSales = $component->instance()->getInvoicedSales();
        $collected = $component->instance()->getPaymentsCollected();
        $cogs = $component->instance()->getCostOfGoodsSold();
        $expenses = $component->instance()->getOperatingExpenses();
        $netProfit = $component->instance()->getNetProfit();

        $this->assertGreaterThan(0, $invoicedSales);
        $this->assertGreaterThan(0, $collected);
        $this->assertGreaterThan(0, $cogs);
        $this->assertGreaterThan(0, $expenses);
        $this->assertEquals(round($invoicedSales - $cogs - $expenses, 2), $netProfit);

        // Verify receivables summary
        $receivables = $component->instance()->getReceivablesSummary();
        $this->assertNotEmpty($receivables);
        $this->assertGreaterThan(0, $component->instance()->getTotalReceivables());

        // Verify expense breakdown
        $breakdown = $component->instance()->getExpenseBreakdown();
        $this->assertNotEmpty($breakdown);
    }

    public function test_cashier_role_restrictions_and_security(): void
    {
        $company = Company::where('slug', 'lanka-solar-solutions')->first();

        // Create a cashier user
        $cashier = User::create([
            'name' => 'Kumara Cashier',
            'email' => 'cashier@lankasolar.lk',
            'password' => Hash::make('Password@123'),
            'is_super_admin' => false,
            'is_active' => true,
        ]);

        $company->users()->attach($cashier->id, ['role' => 'cashier']);

        $this->actingAs($cashier);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($company);

        $this->assertTrue($cashier->isTenantCashier());
        $this->assertFalse($cashier->isTenantOwnerOrAdmin());

        // 1. Cashier CANNOT view Financial Reports page
        $this->assertFalse(FinancialReports::canAccess());

        // 2. Cashier CANNOT view Expenses (ExpensePolicy)
        $expensePolicy = new ExpensePolicy;
        $this->assertFalse($expensePolicy->viewAny($cashier));
        $this->assertFalse($expensePolicy->create($cashier));

        // 3. Cashier CANNOT delete Invoices or Payments
        $invoice = Invoice::where('company_id', $company->id)->first();
        $invoicePolicy = new InvoicePolicy;
        $this->assertTrue($invoicePolicy->view($cashier, $invoice));
        $this->assertTrue($invoicePolicy->create($cashier));
        $this->assertFalse($invoicePolicy->delete($cashier, $invoice));

        $payment = Payment::where('company_id', $company->id)->first();
        $paymentPolicy = new PaymentPolicy;
        $this->assertFalse($paymentPolicy->delete($cashier, $payment));

        // 4. Cashier has view-only access to Products (cannot create/edit/delete)
        $product = Product::where('company_id', $company->id)->first();
        $productPolicy = new ProductPolicy;
        $this->assertTrue($productPolicy->viewAny($cashier));
        $this->assertTrue($productPolicy->view($cashier, $product));
        $this->assertFalse($productPolicy->create($cashier));
        $this->assertFalse($productPolicy->update($cashier, $product));
        $this->assertFalse($productPolicy->delete($cashier, $product));

        // 5. Owner has full delete permissions
        $owner = User::where('email', 'owner@lankasolar.lk')->first();
        $this->assertTrue($invoicePolicy->delete($owner, $invoice));
        $this->assertTrue($expensePolicy->deleteAny($owner));
        $this->assertTrue($productPolicy->delete($owner, $product));
    }
}

<?php

namespace Tests\Feature;

use App\Filament\App\Resources\Invoices\Pages\CreateInvoice;
use App\Filament\App\Resources\Invoices\Pages\EditInvoice;
use App\Filament\App\Resources\Quotations\Pages\CreateQuotation;
use App\Filament\App\Resources\Quotations\Pages\EditQuotation;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ZohoDocumentEditorTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;

    protected User $user;

    protected Contact $contact;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->company = Company::where('slug', 'vertex-homes')->first()
            ?? Company::where('slug', 'lanka-solar-solutions')->first();

        $this->user = User::where('email', 'operations@vertexhomeslk.com')->first()
            ?? User::where('email', 'owner@lankasolar.lk')->first();

        $this->contact = Contact::where('company_id', $this->company->id)->first();
        $this->product = Product::where('company_id', $this->company->id)->first();

        $this->actingAs($this->user);
        Filament::setCurrentPanel(Filament::getPanel('app'));
        Filament::setTenant($this->company);
    }

    public function test_create_quotation_mounts_zoho_editor_and_creates_record(): void
    {
        Livewire::test(CreateQuotation::class)
            ->assertSuccessful()
            ->assertSee('Customer Name*')
            ->assertSee('Quote#*')
            ->assertSee('Item Table')
            ->assertSee('Solar System Engineering Specifications')
            ->set('data.contact_id', $this->contact->id)
            ->set('data.quote_number', 'QUO-2026-ZOHO-TEST')
            ->set('data.system_kw', 10.00)
            ->set('data.phase_type', 'three')
            ->set('data.roof_type', 'Tile Roof')
            ->set('data.items', [
                [
                    'id' => null,
                    'product_id' => $this->product->id,
                    'description' => $this->product->name,
                    'quantity' => 2,
                    'unit_price' => (float) $this->product->unit_price,
                    'discount_rate' => 10.0,
                    'tax' => 5000.00,
                    'subtotal' => 0.00,
                ],
            ])
            ->call('recalculateZohoTotals')
            ->call('create');

        $quotation = Quotation::where('quote_number', 'QUO-2026-ZOHO-TEST')->first();
        $this->assertNotNull($quotation);
        $this->assertEquals($this->contact->id, $quotation->contact_id);
        $this->assertEquals(10.00, (float) $quotation->system_kw);
        $this->assertEquals('three', $quotation->phase_type);
        $this->assertEquals(1, $quotation->items()->count());

        $item = $quotation->items->first();
        $this->assertEquals(2, (float) $item->quantity);
        $this->assertEquals(5000.00, (float) $item->tax);
    }

    public function test_edit_quotation_mounts_zoho_editor_with_existing_items_and_updates(): void
    {
        $quotation = Quotation::where('company_id', $this->company->id)->first();
        $this->assertNotNull($quotation);

        Livewire::test(EditQuotation::class, ['record' => $quotation->id])
            ->assertSuccessful()
            ->assertSee('Financial Summary')
            ->assertSee('Total ( LKR )')
            ->call('addItemRow')
            ->call('save');

        $this->assertDatabaseHas('quotations', [
            'id' => $quotation->id,
            'quote_number' => $quotation->quote_number,
        ]);
    }

    public function test_solar_preset_loads_items_and_recalculates_totals(): void
    {
        $component = Livewire::test(CreateQuotation::class)
            ->assertSuccessful()
            ->call('applySolarPreset', '5kw');

        $items = $component->get('data.items');
        $this->assertNotEmpty($items);
        $this->assertGreaterThanOrEqual(4, count($items));

        $grandTotal = (float) $component->get('data.grand_total');
        $this->assertGreaterThan(0, $grandTotal);
        $this->assertEquals(5.00, (float) $component->get('data.system_kw'));
    }

    public function test_create_invoice_mounts_zoho_editor_and_saves_invoice(): void
    {
        Livewire::test(CreateInvoice::class)
            ->assertSuccessful()
            ->assertSee('Invoice#*')
            ->assertSee('Due Date:')
            ->set('data.contact_id', $this->contact->id)
            ->set('data.invoice_number', 'INV-2026-ZOHO-TEST')
            ->set('data.paid_amount', 100000.00)
            ->set('data.items', [
                [
                    'id' => null,
                    'product_id' => $this->product->id,
                    'description' => $this->product->name,
                    'quantity' => 1,
                    'unit_price' => 250000.00,
                    'discount_rate' => 0.0,
                    'tax' => 0.00,
                    'subtotal' => 250000.00,
                ],
            ])
            ->call('recalculateZohoTotals')
            ->call('create');

        $invoice = Invoice::where('invoice_number', 'INV-2026-ZOHO-TEST')->first();
        $this->assertNotNull($invoice);
        $this->assertEquals($this->contact->id, $invoice->contact_id);
        $this->assertEquals(250000.00, (float) $invoice->grand_total);
        $this->assertEquals(100000.00, (float) $invoice->paid_amount);
        $this->assertEquals(150000.00, (float) $invoice->due_amount);
        $this->assertEquals('partial', $invoice->status);
        $this->assertEquals(1, $invoice->items()->count());
    }

    public function test_edit_invoice_mounts_and_recalculates_balance(): void
    {
        $invoice = Invoice::where('company_id', $this->company->id)->first() ?? Invoice::create([
            'company_id' => $this->company->id,
            'contact_id' => $this->contact->id,
            'invoice_number' => 'INV-2026-SEED-01',
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(14)->toDateString(),
            'subtotal' => 100000.00,
            'discount' => 0.00,
            'tax' => 0.00,
            'grand_total' => 100000.00,
            'paid_amount' => 0.00,
            'due_amount' => 100000.00,
            'status' => 'unpaid',
        ]);

        if ($invoice->items()->count() === 0) {
            $invoice->items()->create([
                'product_id' => $this->product->id,
                'description' => $this->product->name,
                'quantity' => 1,
                'unit_price' => 100000.00,
                'tax' => 0.00,
                'subtotal' => 100000.00,
            ]);
        }
        $this->assertNotNull($invoice);

        Livewire::test(EditInvoice::class, ['record' => $invoice->id])
            ->assertSuccessful()
            ->assertSee('Balance Due (Rs.)')
            ->set('data.paid_amount', (float) $invoice->grand_total)
            ->call('recalculateZohoTotals')
            ->call('save');

        $this->assertEquals('paid', $invoice->fresh()->status);
        $this->assertEquals(0.00, (float) $invoice->fresh()->due_amount);
    }
}

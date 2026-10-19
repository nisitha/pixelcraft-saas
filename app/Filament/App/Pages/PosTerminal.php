<?php

namespace App\Filament\App\Pages;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductSerial;
use App\Services\PdfService;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosTerminal extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedComputerDesktop;

    protected static ?string $navigationLabel = 'POS Terminal';

    protected static ?string $title = 'Point of Sale (POS) Terminal';

    protected static ?string $slug = 'pos';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.app.pages.pos-terminal';

    public static function shouldRegisterNavigation(): bool
    {
        $tenant = Filament::getTenant();

        if (! $tenant) {
            return true;
        }

        return (bool) ($tenant->settings['enable_pos_module'] ?? true);
    }

    public string $search = '';

    public ?int $selectedCategoryId = null;

    public ?int $customerId = null;

    /**
     * @var array<int, array{id: int, name: string, sku: string, unit_price: float, quantity: int|float, subtotal: float, stock_qty: float}>
     */
    public array $cart = [];

    public float $discount = 0.00;

    public string $paymentMethod = 'cash';

    public float $amountTendered = 0.00;

    public string $notes = '';

    public function mount(): void
    {
        $tenant = Filament::getTenant();

        if ($tenant) {
            $walkIn = Contact::firstOrCreate(
                [
                    'company_id' => $tenant->id,
                    'name' => 'Walk-in Customer',
                ],
                [
                    'type' => 'customer',
                    'phone' => '0770000000',
                    'email' => 'walkin@customer.lk',
                ]
            );

            $this->customerId = $walkIn->id;
        }
    }

    public function getCategories(): Collection
    {
        $tenantId = Filament::getTenant()?->id;

        return Category::where('company_id', $tenantId)->orderBy('name')->get();
    }

    public function getCustomers(): Collection
    {
        $tenantId = Filament::getTenant()?->id;

        return Contact::where('company_id', $tenantId)->where('type', 'customer')->orderBy('name')->get();
    }

    public function getProducts(): Collection
    {
        $tenantId = Filament::getTenant()?->id;

        $query = Product::where('company_id', $tenantId);

        if ($this->selectedCategoryId) {
            $query->where('category_id', $this->selectedCategoryId);
        }

        if (filled($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('sku', 'like', '%'.$this->search.'%');
            });
        }

        return $query->orderBy('name')->limit(30)->get();
    }

    public function addToCart(int $productId): void
    {
        $product = Product::find($productId);

        if (! $product) {
            return;
        }

        $tenant = Filament::getTenant();
        $inventoryEnabled = (bool) ($tenant?->settings['inventory_enabled'] ?? true);
        $allowOutOfStock = (bool) ($tenant?->settings['allow_out_of_stock_invoicing'] ?? true);

        if ($inventoryEnabled && ! $allowOutOfStock && $product->type === 'product' && ($product->track_inventory ?? true)) {
            $currentQtyInCart = isset($this->cart[$productId]) ? $this->cart[$productId]['quantity'] : 0;
            if ($product->stock_qty <= $currentQtyInCart) {
                Notification::make()
                    ->title('Out of Stock')
                    ->body("Product {$product->name} is out of stock and out-of-stock invoicing is disabled.")
                    ->danger()
                    ->send();

                return;
            }
        }

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
            $this->cart[$productId]['subtotal'] = round($this->cart[$productId]['quantity'] * $this->cart[$productId]['unit_price'], 2);
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku ?? '',
                'unit_price' => (float) $product->unit_price,
                'quantity' => 1,
                'subtotal' => (float) $product->unit_price,
                'stock_qty' => (float) $product->stock_qty,
            ];
        }

        $this->amountTendered = $this->getGrandTotal();

        Notification::make()
            ->title('Added to Cart')
            ->success()
            ->duration(1200)
            ->send();
    }

    public function updateQuantity(int $productId, int|float $qty): void
    {
        if ($qty <= 0) {
            $this->removeFromCart($productId);

            return;
        }

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity'] = $qty;
            $this->cart[$productId]['subtotal'] = round($qty * $this->cart[$productId]['unit_price'], 2);
            $this->amountTendered = $this->getGrandTotal();
        }
    }

    public function incrementQuantity(int $productId): void
    {
        if (isset($this->cart[$productId])) {
            $this->updateQuantity($productId, $this->cart[$productId]['quantity'] + 1);
        }
    }

    public function decrementQuantity(int $productId): void
    {
        if (isset($this->cart[$productId])) {
            $this->updateQuantity($productId, $this->cart[$productId]['quantity'] - 1);
        }
    }

    public function removeFromCart(int $productId): void
    {
        unset($this->cart[$productId]);
        $this->amountTendered = $this->getGrandTotal();
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->discount = 0.00;
        $this->amountTendered = 0.00;
    }

    public function getSubtotal(): float
    {
        return round(array_sum(array_column($this->cart, 'subtotal')), 2);
    }

    public function getGrandTotal(): float
    {
        return max(0, round($this->getSubtotal() - (float) $this->discount, 2));
    }

    public function getChangeDue(): float
    {
        return max(0, round((float) $this->amountTendered - $this->getGrandTotal(), 2));
    }

    public function completeSale()
    {
        if (empty($this->cart)) {
            Notification::make()
                ->title('Cart is empty')
                ->warning()
                ->body('Please select at least one product before checking out.')
                ->send();

            return null;
        }

        $tenant = Filament::getTenant();

        $invoice = DB::transaction(function () use ($tenant) {
            $grandTotal = $this->getGrandTotal();
            $subtotal = $this->getSubtotal();

            $invoice = Invoice::create([
                'company_id' => $tenant->id,
                'contact_id' => $this->customerId,
                'invoice_number' => 'POS-'.date('Ymd').'-'.str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->toDateString(),
                'type' => 'pos',
                'subtotal' => $subtotal,
                'discount' => $this->discount,
                'tax' => 0.00,
                'grand_total' => $grandTotal,
                'paid_amount' => $grandTotal,
                'due_amount' => 0.00,
                'status' => 'paid',
            ]);

            foreach ($this->cart as $item) {
                $invoice->items()->create([
                    'product_id' => $item['id'],
                    'description' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax' => 0.00,
                    'subtotal' => $item['subtotal'],
                ]);

                // Auto-link any in-stock serial numbers if product tracks serials
                $product = Product::find($item['id']);
                if ($product && $product->track_serial) {
                    $availableSerials = ProductSerial::where('company_id', $tenant->id)
                        ->where('product_id', $product->id)
                        ->where('status', 'in_stock')
                        ->limit((int) $item['quantity'])
                        ->get();

                    foreach ($availableSerials as $serial) {
                        $serial->update([
                            'status' => 'sold',
                            'invoice_id' => $invoice->id,
                            'contact_id' => $this->customerId,
                            'warranty_expires_at' => now()->addMonths($product->warranty_months ?: 12),
                        ]);
                    }
                }
            }

            Payment::create([
                'company_id' => $tenant->id,
                'invoice_id' => $invoice->id,
                'payment_date' => now()->toDateString(),
                'amount' => $grandTotal,
                'method' => $this->paymentMethod,
                'reference_no' => 'POS-REC-'.strtoupper(Str::random(6)),
                'milestone_label' => 'POS Quick Counter Sale',
                'notes' => 'Tendered: Rs. '.number_format((float) $this->amountTendered, 2).' | Change: Rs. '.number_format($this->getChangeDue(), 2),
            ]);

            return $invoice;
        });

        $amountTendered = $this->amountTendered;
        $changeDue = $this->getChangeDue();

        $this->clearCart();

        Notification::make()
            ->title('POS Sale #'.$invoice->invoice_number.' Completed!')
            ->success()
            ->body('Payment recorded via '.strtoupper($this->paymentMethod).'. Generating receipt...')
            ->send();

        $safeNumber = str_replace(['/', '\\'], '-', $invoice->invoice_number);
        $filename = "Receipt-{$safeNumber}.pdf";

        return response()->streamDownload(
            fn () => print (app(PdfService::class)->generatePosReceiptPdf($invoice, $amountTendered, $changeDue)->output()),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }
}

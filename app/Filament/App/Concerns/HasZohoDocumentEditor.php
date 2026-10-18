<?php

namespace App\Filament\App\Concerns;

use App\Models\Product;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

trait HasZohoDocumentEditor
{
    public string $documentType = 'quotation'; // 'quotation' or 'invoice'

    public bool $showSolarSpecs = false;

    public bool $showTotalSummary = true;

    public bool $showBulkModal = false;

    public array $selectedBulkProducts = [];

    public string $bulkSearch = '';

    public ?string $bulkCategory = null;

    /**
     * Initialize document state for Zoho editor.
     */
    public function initZohoDocument(): void
    {
        $tenant = Filament::getTenant();
        $companyId = $tenant?->id;

        if (is_null($this->data)) {
            $this->data = [];
        }

        if ($this->documentType === 'quotation') {
            $this->initQuotationData($companyId);
        } else {
            $this->initInvoiceData($companyId);
        }

        // Ensure at least one item row exists
        if (empty($this->data['items'])) {
            $this->addItemRow();
        }

        $this->recalculateZohoTotals();
    }

    protected function initQuotationData(?int $companyId): void
    {
        if ($this->record instanceof Model && $this->record->exists) {
            $this->data = array_merge([
                'contact_id' => $this->record->contact_id,
                'quote_number' => $this->record->quote_number,
                'quote_date' => $this->record->quote_date?->format('Y-m-d') ?? now()->toDateString(),
                'valid_until' => $this->record->valid_until?->format('Y-m-d') ?? now()->addDays(30)->toDateString(),
                'status' => $this->record->status ?? 'draft',
                'system_kw' => (float) ($this->record->system_kw ?? 5.00),
                'phase_type' => $this->record->phase_type ?? 'single',
                'roof_type' => $this->record->roof_type ?? 'Tile Roof',
                'inverter_brand' => $this->record->inverter_brand ?? '',
                'panel_brand' => $this->record->panel_brand ?? '',
                'terms' => $this->record->terms ?? $this->getDefaultQuotationTerms(),
                'customer_notes' => 'Thank you for considering Vertex Home Solutions. We look forward to powering your solar journey.',
                'subtotal' => (float) ($this->record->subtotal ?? 0),
                'discount' => (float) ($this->record->discount ?? 0),
                'tax' => (float) ($this->record->tax ?? 0),
                'grand_total' => (float) ($this->record->grand_total ?? 0),
            ], $this->data ?? []);

            $this->data['items'] = $this->record->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'description' => $item->description,
                    'quantity' => (float) $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'discount_rate' => 0.00,
                    'tax' => (float) ($item->tax ?? 0),
                    'subtotal' => (float) $item->subtotal,
                ];
            })->toArray();
        } else {
            $defaultQuoteNo = 'QUO-'.date('Y').'-'.str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $this->data = array_merge([
                'contact_id' => null,
                'quote_number' => $defaultQuoteNo,
                'quote_date' => now()->toDateString(),
                'valid_until' => now()->addDays(30)->toDateString(),
                'status' => 'draft',
                'system_kw' => 5.00,
                'phase_type' => 'single',
                'roof_type' => 'Tile Roof',
                'inverter_brand' => 'GoodWe GW5000D-NS Single Phase',
                'panel_brand' => 'METEOR 610W-630W N-type TopCon Bifacial',
                'terms' => $this->getDefaultQuotationTerms(),
                'customer_notes' => 'Thank you for considering Vertex Home Solutions. We look forward to powering your solar journey.',
                'subtotal' => 0.00,
                'discount' => 0.00,
                'tax' => 0.00,
                'grand_total' => 0.00,
                'items' => [],
            ], $this->data ?? []);
        }
    }

    protected function initInvoiceData(?int $companyId): void
    {
        if ($this->record instanceof Model && $this->record->exists) {
            $this->data = array_merge([
                'contact_id' => $this->record->contact_id,
                'quotation_id' => $this->record->quotation_id,
                'invoice_number' => $this->record->invoice_number,
                'invoice_date' => $this->record->invoice_date?->format('Y-m-d') ?? now()->toDateString(),
                'due_date' => $this->record->due_date?->format('Y-m-d') ?? now()->addDays(14)->toDateString(),
                'status' => $this->record->status ?? 'unpaid',
                'type' => $this->record->type ?? 'standard',
                'terms' => 'Due on Receipt',
                'customer_notes' => 'Thanks for your business. Please remit payments using the verified bank accounts below.',
                'subtotal' => (float) ($this->record->subtotal ?? 0),
                'discount' => (float) ($this->record->discount ?? 0),
                'tax' => (float) ($this->record->tax ?? 0),
                'grand_total' => (float) ($this->record->grand_total ?? 0),
                'paid_amount' => (float) ($this->record->paid_amount ?? 0),
                'due_amount' => (float) ($this->record->due_amount ?? 0),
            ], $this->data ?? []);

            $this->data['items'] = $this->record->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'description' => $item->description,
                    'quantity' => (float) $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'discount_rate' => 0.00,
                    'tax' => (float) ($item->tax ?? 0),
                    'subtotal' => (float) $item->subtotal,
                ];
            })->toArray();
        } else {
            $defaultInvNo = 'INV-'.date('Y').'-'.str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $this->data = array_merge([
                'contact_id' => null,
                'quotation_id' => null,
                'invoice_number' => $defaultInvNo,
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->addDays(14)->toDateString(),
                'status' => 'unpaid',
                'type' => 'standard',
                'terms' => 'Due on Receipt',
                'customer_notes' => 'Thanks for your business. Please remit payments using the verified bank accounts below.',
                'subtotal' => 0.00,
                'discount' => 0.00,
                'tax' => 0.00,
                'grand_total' => 0.00,
                'paid_amount' => 0.00,
                'due_amount' => 0.00,
                'items' => [],
            ], $this->data ?? []);
        }
    }

    protected function getDefaultQuotationTerms(): string
    {
        return "1. Valid for 07 days from the date of issue.\n2. 60% advance upon confirmation, 20% on delivery, 20% after commissioning.\n3. Goods once sold will be covered strictly under manufacturer warranty terms.\n4. No additional or hidden charges will be applied beyond this agreed quotation.";
    }

    public function toggleSolarSpecs(): void
    {
        $this->showSolarSpecs = ! $this->showSolarSpecs;
    }

    public function toggleTotalSummary(): void
    {
        $this->showTotalSummary = ! $this->showTotalSummary;
    }

    public function addItemRow(): void
    {
        if (! isset($this->data['items']) || ! is_array($this->data['items'])) {
            $this->data['items'] = [];
        }

        $this->data['items'][] = [
            'id' => null,
            'product_id' => null,
            'description' => '',
            'quantity' => 1,
            'unit_price' => 0.00,
            'discount_rate' => 0.00,
            'tax' => 0.00,
            'subtotal' => 0.00,
        ];

        $this->recalculateZohoTotals();
    }

    public function removeItemRow(int $index): void
    {
        if (isset($this->data['items'][$index])) {
            unset($this->data['items'][$index]);
            $this->data['items'] = array_values($this->data['items']);
        }

        if (empty($this->data['items'])) {
            $this->addItemRow();
        } else {
            $this->recalculateZohoTotals();
        }
    }

    public function updateItemProduct(int $index, $productId): void
    {
        if (! isset($this->data['items'][$index])) {
            return;
        }

        $productId = (int) $productId;
        $this->data['items'][$index]['product_id'] = $productId ?: null;

        if ($productId) {
            $product = Product::find($productId);
            if ($product) {
                $this->data['items'][$index]['description'] = $product->name;
                $this->data['items'][$index]['unit_price'] = (float) $product->unit_price;
                $qty = (float) ($this->data['items'][$index]['quantity'] ?: 1);
                $this->data['items'][$index]['subtotal'] = round($qty * (float) $product->unit_price, 2);
            }
        }

        $this->recalculateZohoTotals();
    }

    public function recalculateZohoTotals(): void
    {
        $items = $this->data['items'] ?? [];
        $subtotal = 0.00;
        $totalTax = 0.00;

        foreach ($items as $idx => $item) {
            $qty = (float) ($item['quantity'] ?? 0);
            $rate = (float) ($item['unit_price'] ?? 0);
            $discRate = (float) ($item['discount_rate'] ?? 0);
            $rowTax = (float) ($item['tax'] ?? 0);

            $gross = $qty * $rate;
            $discAmount = $gross * ($discRate / 100);
            $rowTotal = max(0, round($gross - $discAmount + $rowTax, 2));

            $this->data['items'][$idx]['subtotal'] = $rowTotal;
            $subtotal += $gross - $discAmount;
            $totalTax += $rowTax;
        }

        $docDiscount = (float) ($this->data['discount'] ?? 0);
        $grandTotal = max(0, round($subtotal - $docDiscount + $totalTax, 2));

        $this->data['subtotal'] = round($subtotal, 2);
        $this->data['tax'] = round($totalTax, 2);
        $this->data['grand_total'] = $grandTotal;

        if ($this->documentType === 'invoice') {
            $paid = (float) ($this->data['paid_amount'] ?? 0);
            $due = max(0, round($grandTotal - $paid, 2));
            $this->data['due_amount'] = $due;

            if ($paid >= $grandTotal && $grandTotal > 0) {
                $this->data['status'] = 'paid';
            } elseif ($paid > 0 && $due > 0) {
                $this->data['status'] = 'partial';
            } elseif ($paid == 0) {
                $this->data['status'] = 'unpaid';
            }
        }
    }

    public function updatedData($value, $key): void
    {
        if (str_contains($key, 'quantity') ||
            str_contains($key, 'unit_price') ||
            str_contains($key, 'discount') ||
            str_contains($key, 'tax') ||
            str_contains($key, 'paid_amount')) {
            $this->recalculateZohoTotals();
        }
    }

    public function openBulkModal(): void
    {
        $this->showBulkModal = true;
        $this->selectedBulkProducts = [];
        $this->bulkSearch = '';
        $this->bulkCategory = null;
    }

    public function closeBulkModal(): void
    {
        $this->showBulkModal = false;
    }

    public function toggleBulkProduct(int $productId): void
    {
        if (in_array($productId, $this->selectedBulkProducts)) {
            $this->selectedBulkProducts = array_values(array_diff($this->selectedBulkProducts, [$productId]));
        } else {
            $this->selectedBulkProducts[] = $productId;
        }
    }

    public function addSelectedBulkProducts(): void
    {
        if (empty($this->selectedBulkProducts)) {
            $this->closeBulkModal();

            return;
        }

        $products = Product::whereIn('id', $this->selectedBulkProducts)->get();

        // If the first row is empty, overwrite it
        $firstItem = $this->data['items'][0] ?? null;
        $isFirstEmpty = count($this->data['items']) === 1 && empty($firstItem['product_id']) && empty($firstItem['description']);

        if ($isFirstEmpty) {
            $this->data['items'] = [];
        }

        foreach ($products as $prod) {
            $this->data['items'][] = [
                'id' => null,
                'product_id' => $prod->id,
                'description' => $prod->name,
                'quantity' => 1,
                'unit_price' => (float) $prod->unit_price,
                'discount_rate' => 0.00,
                'tax' => 0.00,
                'subtotal' => (float) $prod->unit_price,
            ];
        }

        $this->closeBulkModal();
        $this->recalculateZohoTotals();

        Notification::make()
            ->title(count($products).' items added to table')
            ->success()
            ->send();
    }

    public function applySolarPreset(string $presetKey): void
    {
        $companyId = Filament::getTenant()?->id;

        $presets = [
            '5kw' => [
                'name' => '5kW On-Grid Solar Package',
                'kw' => 5.00,
                'phase' => 'single',
                'items' => [
                    ['search' => 'GoodWe GW5000', 'name' => 'GoodWe 5kW On-Grid Solar Inverter (GW5000D-NS)', 'qty' => 1, 'price' => 385000.00],
                    ['search' => 'METEOR', 'name' => 'METEOR 610W-630W N-type TopCon Bifacial PV Panel', 'qty' => 8, 'price' => 54000.00],
                    ['search' => 'Aluminum Solar Rails', 'name' => 'Aluminum Solar Mounting Rails & Clamp Kits (Tile Roof)', 'qty' => 8, 'price' => 6500.00],
                    ['search' => 'DC SPD', 'name' => 'Suntree DC SPD 1000V Surge Protection Device', 'qty' => 2, 'price' => 9500.00],
                    ['search' => 'Solar DC PV Cable', 'name' => '4mm² KBE / Leader Solar DC Cable (Black/Red 100m Roll)', 'qty' => 1, 'price' => 38000.00],
                    ['search' => 'Installation', 'name' => 'Complete System Engineering, Installation & CEB Grid Interconnection', 'qty' => 1, 'price' => 125000.00],
                ],
            ],
            '10kw' => [
                'name' => '10kW Three-Phase Solar Package',
                'kw' => 10.00,
                'phase' => 'three',
                'items' => [
                    ['search' => 'GoodWe GW10K', 'name' => 'GoodWe 10kW Three-Phase Solar Inverter (GW10K-SDT-20)', 'qty' => 1, 'price' => 620000.00],
                    ['search' => 'METEOR', 'name' => 'METEOR 610W-630W N-type TopCon Bifacial PV Panel', 'qty' => 16, 'price' => 54000.00],
                    ['search' => 'Aluminum Solar Rails', 'name' => 'Heavy-Duty Aluminum Solar Mounting Rails & L-Feet Kits', 'qty' => 16, 'price' => 6500.00],
                    ['search' => 'DC SPD', 'name' => 'Suntree DC SPD 1000V Surge Protection Device', 'qty' => 4, 'price' => 9500.00],
                    ['search' => 'Solar DC PV Cable', 'name' => '6mm² Solar DC PV Cable (Black/Red 200m)', 'qty' => 1, 'price' => 76000.00],
                    ['search' => 'Installation', 'name' => 'Turnkey Engineering, Mounting, CEB/LECO Testing & Commissioning', 'qty' => 1, 'price' => 220000.00],
                ],
            ],
            '15kw' => [
                'name' => '15kW Commercial Solar Package',
                'kw' => 15.00,
                'phase' => 'three',
                'items' => [
                    ['search' => 'Huawei 15kW', 'name' => 'Huawei 15kW Three-Phase Smart String Inverter SUN2000', 'qty' => 1, 'price' => 890000.00],
                    ['search' => 'METEOR', 'name' => 'METEOR 610W-630W N-type TopCon Bifacial PV Panel', 'qty' => 24, 'price' => 54000.00],
                    ['search' => 'Aluminum Solar Rails', 'name' => 'Industrial Solar Mounting Structure Kit (Corrugated/Concrete)', 'qty' => 24, 'price' => 7200.00],
                    ['search' => 'Suntree', 'name' => 'Suntree DC Disconnect & Switchgear Distribution Panel', 'qty' => 1, 'price' => 65000.00],
                    ['search' => 'Installation', 'name' => 'Commercial Solar Engineering, CEB Substation Clearance & Net Metering', 'qty' => 1, 'price' => 350000.00],
                ],
            ],
        ];

        if (! isset($presets[$presetKey])) {
            return;
        }

        $preset = $presets[$presetKey];
        $this->data['items'] = [];

        foreach ($preset['items'] as $itemSpec) {
            $product = Product::where('company_id', $companyId)
                ->where('name', 'like', '%'.$itemSpec['search'].'%')
                ->first();

            $this->data['items'][] = [
                'id' => null,
                'product_id' => $product?->id,
                'description' => $product?->name ?? $itemSpec['name'],
                'quantity' => $itemSpec['qty'],
                'unit_price' => $product ? (float) $product->unit_price : $itemSpec['price'],
                'discount_rate' => 0.00,
                'tax' => 0.00,
                'subtotal' => ($itemSpec['qty'] * ($product ? (float) $product->unit_price : $itemSpec['price'])),
            ];
        }

        if ($this->documentType === 'quotation') {
            $this->data['system_kw'] = $preset['kw'];
            $this->data['phase_type'] = $preset['phase'];
        }

        $this->recalculateZohoTotals();

        Notification::make()
            ->title("Preset loaded: {$preset['name']}")
            ->success()
            ->send();
    }

    /**
     * Handle Create Record for Zoho Document
     */
    public function create(bool $another = false): void
    {
        $this->authorizeAccess();

        $this->validate([
            'data.contact_id' => 'required',
            'data.'.($this->documentType === 'quotation' ? 'quote_number' : 'invoice_number') => 'required',
        ], [
            'data.contact_id.required' => 'Please select a customer for this '.($this->documentType === 'quotation' ? 'quotation' : 'invoice').'.',
        ]);

        $this->recalculateZohoTotals();

        $companyId = Filament::getTenant()->id;
        $items = $this->data['items'] ?? [];

        $record = DB::transaction(function () use ($companyId, $items) {
            $recordData = $this->data;
            unset($recordData['items'], $recordData['customer_notes']);

            if ($this->documentType === 'quotation') {
                unset($recordData['paid_amount'], $recordData['due_amount']);
            }

            $record = static::getModel()::create([
                ...$recordData,
                'company_id' => $companyId,
            ]);

            foreach ($items as $item) {
                if (! empty($item['product_id']) || ! empty($item['description'])) {
                    $record->items()->create([
                        'product_id' => $item['product_id'] ?? null,
                        'description' => $item['description'] ?? 'Item',
                        'quantity' => (float) ($item['quantity'] ?? 1),
                        'unit_price' => (float) ($item['unit_price'] ?? 0),
                        'tax' => (float) ($item['tax'] ?? 0),
                        'subtotal' => (float) ($item['subtotal'] ?? 0),
                    ]);
                }
            }

            return $record;
        });

        $this->record = $record;

        Notification::make()
            ->title(ucfirst($this->documentType).' created successfully')
            ->success()
            ->send();

        if ($another) {
            $this->fillForm();
            $this->initZohoDocument();

            return;
        }

        $this->redirect($this->getResource()::getUrl('index'));
    }

    /**
     * Handle Save Record for Zoho Document
     */
    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        $this->authorizeAccess();

        $this->validate([
            'data.contact_id' => 'required',
            'data.'.($this->documentType === 'quotation' ? 'quote_number' : 'invoice_number') => 'required',
        ], [
            'data.contact_id.required' => 'Please select a customer for this '.($this->documentType === 'quotation' ? 'quotation' : 'invoice').'.',
        ]);

        $this->recalculateZohoTotals();

        $items = $this->data['items'] ?? [];

        DB::transaction(function () use ($items) {
            $recordData = $this->data;
            unset($recordData['items'], $recordData['customer_notes']);

            if ($this->documentType === 'quotation') {
                unset($recordData['paid_amount'], $recordData['due_amount']);
            }

            $this->record->update($recordData);

            // Sync items
            $this->record->items()->delete();
            foreach ($items as $item) {
                if (! empty($item['product_id']) || ! empty($item['description'])) {
                    $this->record->items()->create([
                        'product_id' => $item['product_id'] ?? null,
                        'description' => $item['description'] ?? 'Item',
                        'quantity' => (float) ($item['quantity'] ?? 1),
                        'unit_price' => (float) ($item['unit_price'] ?? 0),
                        'tax' => (float) ($item['tax'] ?? 0),
                        'subtotal' => (float) ($item['subtotal'] ?? 0),
                    ]);
                }
            }
        });

        Notification::make()
            ->title(ucfirst($this->documentType).' updated successfully')
            ->success()
            ->send();

        if ($shouldRedirect) {
            $this->redirect($this->getResource()::getUrl('index'));
        }
    }
}

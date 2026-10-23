<?php

namespace App\Filament\App\Concerns;

use App\Models\Contact;
use App\Models\Payment;
use App\Models\Product;
use App\Models\SalesAgent;
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

    // Quick Add Customer Modal
    public bool $showQuickCustomerModal = false;

    public string $quickCustomerName = '';

    public string $quickCustomerPhone = '';

    public string $quickCustomerEmail = '';

    public string $quickCustomerAddress = '';

    // Quick Add Item Modal
    public bool $showQuickItemModal = false;

    public string $quickItemName = '';

    public string $quickItemSku = '';

    public string|float $quickItemPrice = '';

    public string $quickItemType = 'product';

    public bool $quickItemTrackInventory = true;

    // Quick Add Sales Agent Modal
    public bool $showQuickAgentModal = false;

    public string $quickAgentName = '';

    public string $quickAgentCode = '';

    public string $quickAgentPhone = '';

    public string $quickAgentCommissionType = 'fixed';

    public string|float $quickAgentCommissionValue = 0.00;

    // Discount Switching & Tax Presets
    public string $discountType = 'fixed'; // 'fixed' or 'percentage'

    public float $discountPercent = 0.00;

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
                'sales_agent_id' => $this->record->sales_agent_id ?? null,
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
                'price_display_mode' => $this->record->price_display_mode ?? 'itemized',
                'pdf_item_summary_mode' => $this->record->pdf_item_summary_mode ?? 'consolidated',
                'subtotal' => (float) ($this->record->subtotal ?? 0),
                'discount' => (float) ($this->record->discount ?? 0),
                'tax' => (float) ($this->record->tax ?? 0),
                'profit_markup' => (float) ($this->record->profit_markup ?? 0),
                'profit_markup_type' => $this->record->profit_markup_type ?? 'fixed',
                'grand_total' => (float) ($this->record->grand_total ?? 0),
                'commission_amount' => (float) ($this->record->commission_amount ?? 0),
                'commission_status' => $this->record->commission_status ?? 'pending',
            ], $this->data ?? []);

            $this->data['items'] = $this->record->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'description' => $item->description,
                    'warranty_text' => $item->warranty_text ?? ($item->product?->formatted_warranty ?? ''),
                    'quantity' => (float) $item->quantity,
                    'cost_price' => (float) ($item->cost_price ?? ($item->product?->cost_price ?? 0)),
                    'unit_price' => (float) $item->unit_price,
                    'discount_rate' => 0.00,
                    'tax' => (float) ($item->tax ?? 0),
                    'subtotal' => (float) $item->subtotal,
                    'hide_on_customer_pdf' => (bool) ($item->hide_on_customer_pdf ?? false),
                ];
            })->toArray();
        } else {
            $defaultQuoteNo = 'QUO-'.date('Y').'-'.str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $this->data = array_merge([
                'contact_id' => null,
                'sales_agent_id' => null,
                'quote_number' => $defaultQuoteNo,
                'quote_date' => now()->toDateString(),
                'valid_until' => now()->addDays(30)->toDateString(),
                'status' => 'draft',
                'price_display_mode' => 'itemized',
                'pdf_item_summary_mode' => 'consolidated',
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
                'profit_markup' => 0.00,
                'profit_markup_type' => 'fixed',
                'grand_total' => 0.00,
                'commission_amount' => 0.00,
                'commission_status' => 'pending',
                'items' => [],
            ], $this->data ?? []);
        }
    }

    protected function initInvoiceData(?int $companyId): void
    {
        if ($this->record instanceof Model && $this->record->exists) {
            $paid = (float) ($this->record->paid_amount ?? 0);
            $grandTotal = (float) ($this->record->grand_total ?? 0);
            $settlement = 'unpaid';
            if ($paid >= $grandTotal && $grandTotal > 0) {
                $settlement = 'fully_paid';
            } elseif ($paid > 0) {
                $settlement = 'partial';
            }

            $this->data = array_merge([
                'contact_id' => $this->record->contact_id,
                'quotation_id' => $this->record->quotation_id,
                'sales_agent_id' => $this->record->sales_agent_id ?? null,
                'invoice_number' => $this->record->invoice_number,
                'invoice_date' => $this->record->invoice_date?->format('Y-m-d') ?? now()->toDateString(),
                'due_date' => $this->record->due_date?->format('Y-m-d') ?? now()->addDays(14)->toDateString(),
                'status' => $this->record->status ?? 'unpaid',
                'type' => $this->record->type ?? 'standard',
                'terms' => 'Due on Receipt',
                'customer_notes' => 'Thanks for your business. Please remit payments using the verified bank accounts below.',
                'price_display_mode' => $this->record->price_display_mode ?? 'itemized',
                'pdf_item_summary_mode' => $this->record->pdf_item_summary_mode ?? 'consolidated',
                'subtotal' => (float) ($this->record->subtotal ?? 0),
                'discount' => (float) ($this->record->discount ?? 0),
                'tax' => (float) ($this->record->tax ?? 0),
                'profit_markup' => (float) ($this->record->profit_markup ?? 0),
                'profit_markup_type' => $this->record->profit_markup_type ?? 'fixed',
                'grand_total' => $grandTotal,
                'settlement_status' => $settlement,
                'initial_payment_amount' => $paid,
                'paid_amount' => $paid,
                'due_amount' => (float) ($this->record->due_amount ?? max(0, round($grandTotal - $paid, 2))),
                'payment_method' => 'bank_transfer',
                'payment_reference' => '',
                'payment_milestone_label' => $settlement === 'fully_paid' ? 'Full Settlement' : ($settlement === 'partial' ? 'Advance Payment' : '60% Advance'),
                'payment_date' => now()->toDateString(),
                'commission_amount' => (float) ($this->record->commission_amount ?? 0),
                'commission_status' => $this->record->commission_status ?? 'pending',
            ], $this->data ?? []);

            $this->data['items'] = $this->record->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'description' => $item->description,
                    'warranty_text' => $item->warranty_text ?? ($item->product?->formatted_warranty ?? ''),
                    'quantity' => (float) $item->quantity,
                    'cost_price' => (float) ($item->cost_price ?? ($item->product?->cost_price ?? 0)),
                    'unit_price' => (float) $item->unit_price,
                    'discount_rate' => 0.00,
                    'tax' => (float) ($item->tax ?? 0),
                    'subtotal' => (float) $item->subtotal,
                    'hide_on_customer_pdf' => (bool) ($item->hide_on_customer_pdf ?? false),
                ];
            })->toArray();
        } else {
            $defaultInvNo = 'INV-'.date('Y').'-'.str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $this->data = array_merge([
                'contact_id' => null,
                'quotation_id' => null,
                'sales_agent_id' => null,
                'invoice_number' => $defaultInvNo,
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->addDays(14)->toDateString(),
                'status' => 'unpaid',
                'price_display_mode' => 'itemized',
                'pdf_item_summary_mode' => 'consolidated',
                'type' => 'standard',
                'terms' => 'Due on Receipt',
                'customer_notes' => 'Thanks for your business. Please remit payments using the verified bank accounts below.',
                'subtotal' => 0.00,
                'discount' => 0.00,
                'tax' => 0.00,
                'profit_markup' => 0.00,
                'profit_markup_type' => 'fixed',
                'grand_total' => 0.00,
                'settlement_status' => 'unpaid',
                'initial_payment_amount' => 0.00,
                'paid_amount' => 0.00,
                'due_amount' => 0.00,
                'payment_method' => 'bank_transfer',
                'payment_reference' => '',
                'payment_milestone_label' => '60% Advance',
                'payment_date' => now()->toDateString(),
                'commission_amount' => 0.00,
                'commission_status' => 'pending',
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
            'warranty_text' => '',
            'quantity' => 1,
            'cost_price' => 0.00,
            'unit_price' => 0.00,
            'discount_rate' => 0.00,
            'tax' => 0.00,
            'subtotal' => 0.00,
            'hide_on_customer_pdf' => false,
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
                $this->data['items'][$index]['cost_price'] = (float) ($product->cost_price ?? 0);
                $this->data['items'][$index]['unit_price'] = (float) $product->unit_price;
                $this->data['items'][$index]['warranty_text'] = $product->formatted_warranty ?? '';
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

        if ($this->discountType === 'percentage') {
            $percent = (float) ($this->discountPercent ?? 0);
            $docDiscount = round($subtotal * ($percent / 100), 2);
            $this->data['discount'] = $docDiscount;
        } else {
            $docDiscount = (float) ($this->data['discount'] ?? 0);
        }

        $tax = (float) ($this->data['tax'] ?? $totalTax);
        $profitMarkup = (float) ($this->data['profit_markup'] ?? 0.00);
        $grandTotal = max(0, round($subtotal - $docDiscount + $tax + $profitMarkup, 2));

        $this->data['subtotal'] = round($subtotal, 2);
        $this->data['tax'] = round($tax, 2);
        $this->data['profit_markup'] = round($profitMarkup, 2);
        $this->data['grand_total'] = $grandTotal;

        if ($this->documentType === 'invoice') {
            $paid = (float) ($this->data['paid_amount'] ?? ($this->data['initial_payment_amount'] ?? 0));
            $settlement = $this->data['settlement_status'] ?? 'unpaid';

            if ($settlement === 'unpaid' && $paid > 0) {
                $settlement = ($paid >= $grandTotal && $grandTotal > 0) ? 'fully_paid' : 'partial';
                $this->data['settlement_status'] = $settlement;
            }

            if ($settlement === 'fully_paid') {
                $this->data['paid_amount'] = $grandTotal;
                $this->data['initial_payment_amount'] = $grandTotal;
                $this->data['due_amount'] = 0.00;
                $this->data['status'] = $grandTotal > 0 ? 'paid' : 'unpaid';
            } elseif ($settlement === 'unpaid') {
                $this->data['paid_amount'] = 0.00;
                $this->data['initial_payment_amount'] = 0.00;
                $this->data['due_amount'] = $grandTotal;
                $this->data['status'] = 'unpaid';
            } else {
                if ($grandTotal > 0 && $paid > $grandTotal) {
                    $paid = $grandTotal;
                }
                $this->data['paid_amount'] = $paid;
                $this->data['initial_payment_amount'] = $paid;
                $due = max(0, round($grandTotal - $paid, 2));
                $this->data['due_amount'] = $due;

                if ($paid >= $grandTotal && $grandTotal > 0) {
                    $this->data['status'] = 'paid';
                } elseif ($paid > 0) {
                    $this->data['status'] = 'partial';
                } else {
                    $this->data['status'] = 'unpaid';
                }
            }
        }
    }

    public function setDiscountType(string $type): void
    {
        $this->discountType = $type;
        $this->recalculateZohoTotals();
    }

    public function updatedDiscountPercent(): void
    {
        $this->recalculateZohoTotals();
    }

    public function updatedDataProfitMarkup(): void
    {
        $this->recalculateZohoTotals();
    }

    public function updated($property, $value = null): void
    {
        if (is_string($property) && (
            str_starts_with($property, 'data.items') ||
            str_starts_with($property, 'data.discount') ||
            str_starts_with($property, 'data.tax') ||
            str_starts_with($property, 'data.profit_markup')
        )) {
            $this->recalculateZohoTotals();
        }
    }

    public function applyTaxPreset(float $percent, string $label): void
    {
        $subtotal = (float) ($this->data['subtotal'] ?? 0);
        $this->data['tax'] = round($subtotal * ($percent / 100), 2);
        $this->recalculateZohoTotals();

        Notification::make()
            ->title("Tax preset applied: {$label}")
            ->success()
            ->send();
    }

    public function openQuickCustomerModal(): void
    {
        $this->resetQuickCustomerForm();
        $this->showQuickCustomerModal = true;
    }

    public function closeQuickCustomerModal(): void
    {
        $this->showQuickCustomerModal = false;
        $this->resetQuickCustomerForm();
    }

    public function resetQuickCustomerForm(): void
    {
        $this->quickCustomerName = '';
        $this->quickCustomerPhone = '';
        $this->quickCustomerEmail = '';
        $this->quickCustomerAddress = '';
    }

    public function saveQuickCustomer(): void
    {
        $this->validate([
            'quickCustomerName' => 'required|string|min:2|max:255',
        ]);

        $companyId = Filament::getTenant()?->id;

        $customer = Contact::create([
            'company_id' => $companyId,
            'name' => trim($this->quickCustomerName),
            'phone' => trim($this->quickCustomerPhone) ?: null,
            'email' => trim($this->quickCustomerEmail) ?: null,
            'address' => trim($this->quickCustomerAddress) ?: null,
            'type' => 'customer',
        ]);

        $this->data['contact_id'] = $customer->id;
        $this->showQuickCustomerModal = false;
        $this->resetQuickCustomerForm();

        Notification::make()
            ->title("Customer {$customer->name} created and selected")
            ->success()
            ->send();
    }

    public function openQuickItemModal(): void
    {
        $this->resetQuickItemForm();
        $this->showQuickItemModal = true;
    }

    public function closeQuickItemModal(): void
    {
        $this->showQuickItemModal = false;
        $this->resetQuickItemForm();
    }

    public function resetQuickItemForm(): void
    {
        $this->quickItemName = '';
        $this->quickItemSku = '';
        $this->quickItemPrice = '';
        $this->quickItemType = 'product';
        $this->quickItemTrackInventory = true;
    }

    public function saveQuickItem(): void
    {
        $this->validate([
            'quickItemName' => 'required|string|min:2|max:255',
            'quickItemPrice' => 'required|numeric|min:0',
        ]);

        $companyId = Filament::getTenant()?->id;

        $product = Product::create([
            'company_id' => $companyId,
            'name' => trim($this->quickItemName),
            'sku' => trim($this->quickItemSku) ?: ('SKU-'.strtoupper(substr(uniqid(), -6))),
            'unit_price' => (float) $this->quickItemPrice,
            'type' => $this->quickItemType,
            'track_inventory' => $this->quickItemTrackInventory,
            'stock_qty' => 0.00,
        ]);

        $items = $this->data['items'] ?? [];
        $isFirstEmpty = count($items) === 1 && empty($items[0]['product_id']) && empty($items[0]['description']);

        $newItem = [
            'id' => null,
            'product_id' => $product->id,
            'description' => $product->name,
            'quantity' => 1,
            'cost_price' => (float) ($product->cost_price ?? 0),
            'unit_price' => (float) $product->unit_price,
            'discount_rate' => 0.00,
            'tax' => 0.00,
            'subtotal' => (float) $product->unit_price,
        ];

        if ($isFirstEmpty) {
            $this->data['items'] = [$newItem];
        } else {
            $this->data['items'][] = $newItem;
        }

        $this->showQuickItemModal = false;
        $this->resetQuickItemForm();
        $this->recalculateZohoTotals();

        Notification::make()
            ->title("Item '{$product->name}' created and added to table")
            ->success()
            ->send();
    }

    public function openQuickAgentModal(): void
    {
        $this->showQuickAgentModal = true;
        $this->quickAgentCode = 'AGT-'.str_pad((string) rand(1, 999), 3, '0', STR_PAD_LEFT);
        $this->quickAgentName = '';
        $this->quickAgentPhone = '';
        $this->quickAgentCommissionType = 'fixed';
        $this->quickAgentCommissionValue = 0.00;
    }

    public function closeQuickAgentModal(): void
    {
        $this->showQuickAgentModal = false;
    }

    public function saveQuickAgent(): void
    {
        $this->validate([
            'quickAgentName' => 'required|string|min:2|max:255',
        ]);

        $companyId = Filament::getTenant()?->id;

        $agent = SalesAgent::create([
            'company_id' => $companyId,
            'agent_code' => trim($this->quickAgentCode) ?: ('AGT-'.str_pad((string) rand(1, 999), 3, '0', STR_PAD_LEFT)),
            'name' => trim($this->quickAgentName),
            'phone' => trim($this->quickAgentPhone) ?: null,
            'default_commission_type' => $this->quickAgentCommissionType,
            'default_commission_value' => (float) $this->quickAgentCommissionValue,
            'is_active' => true,
        ]);

        $this->data['sales_agent_id'] = $agent->id;
        $this->updateSalesAgentCommission();
        $this->showQuickAgentModal = false;

        Notification::make()
            ->title("Sales Agent '{$agent->name}' created and assigned")
            ->success()
            ->send();
    }

    public function updateSalesAgentCommission(): void
    {
        $agentId = $this->data['sales_agent_id'] ?? null;
        if (! $agentId) {
            return;
        }

        $agent = SalesAgent::find($agentId);
        if (! $agent) {
            return;
        }

        $base = (float) ($this->data['grand_total'] ?? 0);
        $this->data['commission_amount'] = $agent->calculateCommission($base);
    }

    public function setSettlementStatus(string $status): void
    {
        $this->data['settlement_status'] = $status;
        $grandTotal = (float) ($this->data['grand_total'] ?? 0);

        if ($status === 'fully_paid') {
            $this->data['paid_amount'] = $grandTotal;
            $this->data['initial_payment_amount'] = $grandTotal;
            $this->data['due_amount'] = 0.00;
            $this->data['status'] = $grandTotal > 0 ? 'paid' : 'unpaid';
            $this->data['payment_milestone_label'] = 'Full Settlement';
        } elseif ($status === 'unpaid') {
            $this->data['paid_amount'] = 0.00;
            $this->data['initial_payment_amount'] = 0.00;
            $this->data['due_amount'] = $grandTotal;
            $this->data['status'] = 'unpaid';
        } elseif ($status === 'partial') {
            $currentPaid = (float) ($this->data['paid_amount'] ?? 0);
            if ($currentPaid <= 0 || $currentPaid >= $grandTotal) {
                $advance = round($grandTotal * 0.60, 2);
                $this->data['paid_amount'] = $advance;
                $this->data['initial_payment_amount'] = $advance;
                $this->data['payment_milestone_label'] = '60% Advance';
            }
            $due = max(0, round($grandTotal - (float) $this->data['paid_amount'], 2));
            $this->data['due_amount'] = $due;
            $this->data['status'] = $due == 0 ? 'paid' : 'partial';
        }

        $this->recalculateZohoTotals();
    }

    public function updatedData($value, $key): void
    {
        if (str_contains($key, 'settlement_status')) {
            $this->setSettlementStatus((string) $value);
        }

        if (str_contains($key, 'initial_payment_amount')) {
            $this->data['paid_amount'] = (float) $value;
            $this->recalculateZohoTotals();
        }

        if (str_contains($key, 'sales_agent_id')) {
            $this->updateSalesAgentCommission();
        }

        if (str_contains($key, 'quantity') ||
            str_contains($key, 'unit_price') ||
            str_contains($key, 'cost_price') ||
            str_contains($key, 'discount') ||
            str_contains($key, 'tax') ||
            str_contains($key, 'paid_amount') ||
            str_contains($key, 'commission_amount')) {
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
                'warranty_text' => $prod->formatted_warranty ?? '',
                'quantity' => 1,
                'cost_price' => (float) ($prod->cost_price ?? 0),
                'unit_price' => (float) $prod->unit_price,
                'discount_rate' => 0.00,
                'tax' => 0.00,
                'subtotal' => (float) $prod->unit_price,
                'hide_on_customer_pdf' => false,
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
                'warranty_text' => $product ? ($product->formatted_warranty ?? '') : '10 Years Warranty',
                'quantity' => $itemSpec['qty'],
                'cost_price' => $product ? (float) ($product->cost_price ?? 0) : round($itemSpec['price'] * 0.75, 2),
                'unit_price' => $product ? (float) $product->unit_price : $itemSpec['price'],
                'discount_rate' => 0.00,
                'tax' => 0.00,
                'subtotal' => ($itemSpec['qty'] * ($product ? (float) $product->unit_price : $itemSpec['price'])),
                'hide_on_customer_pdf' => false,
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
            unset(
                $recordData['items'],
                $recordData['customer_notes'],
                $recordData['settlement_status'],
                $recordData['initial_payment_amount'],
                $recordData['payment_method'],
                $recordData['payment_reference'],
                $recordData['payment_milestone_label'],
                $recordData['payment_date']
            );

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
                        'warranty_text' => $item['warranty_text'] ?? null,
                        'quantity' => (float) ($item['quantity'] ?? 1),
                        'cost_price' => (float) ($item['cost_price'] ?? 0),
                        'unit_price' => (float) ($item['unit_price'] ?? 0),
                        'tax' => (float) ($item['tax'] ?? 0),
                        'subtotal' => (float) ($item['subtotal'] ?? 0),
                        'hide_on_customer_pdf' => (bool) ($item['hide_on_customer_pdf'] ?? false),
                    ]);
                }
            }

            // If Invoice has an immediate/advance payment, record it in payments table!
            $paidAmount = (float) ($this->data['paid_amount'] ?? 0);
            if ($this->documentType === 'invoice' && $paidAmount > 0) {
                Payment::create([
                    'company_id' => $companyId,
                    'invoice_id' => $record->id,
                    'amount' => $paidAmount,
                    'payment_date' => $this->data['payment_date'] ?? ($record->invoice_date ?? now()->toDateString()),
                    'method' => $this->data['payment_method'] ?? 'cash',
                    'reference_no' => ! empty($this->data['payment_reference']) ? $this->data['payment_reference'] : ('ADV-'.str_replace(['/', '\\'], '-', $record->invoice_number)),
                    'milestone_label' => ! empty($this->data['payment_milestone_label']) ? $this->data['payment_milestone_label'] : ($record->status === 'paid' ? 'Full Settlement' : 'Advance Payment'),
                    'notes' => 'Recorded directly on invoice creation',
                ]);
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
            unset(
                $recordData['items'],
                $recordData['customer_notes'],
                $recordData['settlement_status'],
                $recordData['initial_payment_amount'],
                $recordData['payment_method'],
                $recordData['payment_reference'],
                $recordData['payment_milestone_label'],
                $recordData['payment_date']
            );

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
                        'warranty_text' => $item['warranty_text'] ?? null,
                        'quantity' => (float) ($item['quantity'] ?? 1),
                        'cost_price' => (float) ($item['cost_price'] ?? 0),
                        'unit_price' => (float) ($item['unit_price'] ?? 0),
                        'tax' => (float) ($item['tax'] ?? 0),
                        'subtotal' => (float) ($item['subtotal'] ?? 0),
                        'hide_on_customer_pdf' => (bool) ($item['hide_on_customer_pdf'] ?? false),
                    ]);
                }
            }

            // If Invoice has newly recorded payment and no payment logged yet, record it!
            $paidAmount = (float) ($this->data['paid_amount'] ?? 0);
            if ($this->documentType === 'invoice' && $paidAmount > 0 && $this->record->payments()->count() === 0) {
                Payment::create([
                    'company_id' => $this->record->company_id,
                    'invoice_id' => $this->record->id,
                    'amount' => $paidAmount,
                    'payment_date' => $this->data['payment_date'] ?? ($this->record->invoice_date ?? now()->toDateString()),
                    'method' => $this->data['payment_method'] ?? 'cash',
                    'reference_no' => ! empty($this->data['payment_reference']) ? $this->data['payment_reference'] : ('ADV-'.str_replace(['/', '\\'], '-', $this->record->invoice_number)),
                    'milestone_label' => ! empty($this->data['payment_milestone_label']) ? $this->data['payment_milestone_label'] : ($this->record->status === 'paid' ? 'Full Settlement' : 'Advance Payment'),
                    'notes' => 'Recorded directly on invoice update',
                ]);
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

    public function getTotalCostProperty(): float
    {
        $totalCost = 0.0;
        foreach ($this->data['items'] ?? [] as $item) {
            $qty = (float) ($item['quantity'] ?? 0);
            $cost = (float) ($item['cost_price'] ?? 0);
            $totalCost += ($qty * $cost);
        }

        return round($totalCost, 2);
    }

    public function getGrossProfitProperty(): float
    {
        $grandTotal = (float) ($this->data['grand_total'] ?? 0);
        $tax = (float) ($this->data['tax'] ?? 0);
        $netRevenue = $grandTotal - $tax;

        return round($netRevenue - $this->totalCost, 2);
    }

    public function getAgentCommissionProperty(): float
    {
        return (float) ($this->data['commission_amount'] ?? 0);
    }

    public function getNetProfitProperty(): float
    {
        return round($this->grossProfit - $this->agentCommission, 2);
    }

    public function getProfitMarginPercentageProperty(): float
    {
        $grandTotal = (float) ($this->data['grand_total'] ?? 0);
        if ($grandTotal <= 0) {
            return 0.0;
        }

        return round(($this->netProfit / $grandTotal) * 100, 1);
    }
}

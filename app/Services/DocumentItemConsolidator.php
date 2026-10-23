<?php

namespace App\Services;

use App\Models\InvoiceItem;
use App\Models\QuotationItem;
use Illuminate\Support\Collection;

class DocumentItemConsolidator
{
    /**
     * Consolidate items for Customer View or Detailed View.
     */
    public static function prepareItemsForDisplay(iterable $items, bool $consolidate = true, bool $packageMode = false, float $profitMarkup = 0.00): Collection
    {
        $rawItems = collect($items);

        if (! $consolidate) {
            if ($packageMode) {
                return $rawItems->filter(function ($item) {
                    $hide = is_array($item)
                        ? (bool) ($item['hide_on_customer_pdf'] ?? false)
                        : (bool) ($item->hide_on_customer_pdf ?? false);

                    return ! $hide;
                })->values();
            }

            return $rawItems->values();
        }

        $panels = collect();
        $inverters = collect();
        $batteries = collect();
        $otherMajor = collect();
        $accessories = collect();
        $services = collect();

        foreach ($rawItems as $item) {
            $class = static::classifyItem($item);

            switch ($class) {
                case 'panel':
                    $panels->push($item);
                    break;
                case 'inverter':
                    $inverters->push($item);
                    break;
                case 'battery':
                    $batteries->push($item);
                    break;
                case 'service':
                    $services->push($item);
                    break;
                case 'other_major':
                    $otherMajor->push($item);
                    break;
                case 'accessory':
                default:
                    $accessories->push($item);
                    break;
            }
        }

        $result = collect();

        // 1. Solar Panels
        foreach ($panels as $item) {
            $result->push(static::normalizeItem($item));
        }

        // 2. Inverters
        foreach ($inverters as $item) {
            $result->push(static::normalizeItem($item));
        }

        // 3. Batteries / BESS
        foreach ($batteries as $item) {
            $result->push(static::normalizeItem($item));
        }

        // Other Major Equipment (if any)
        foreach ($otherMajor as $item) {
            $result->push(static::normalizeItem($item));
        }

        $markupApplied = false;

        // 4. Line 4: Consolidated BOS Accessories & Hardware
        if ($accessories->isNotEmpty() || ($consolidate && $profitMarkup > 0 && $services->isEmpty())) {
            $accSubtotal = 0.0;
            $accTax = 0.0;

            foreach ($accessories as $acc) {
                $accSubtotal += is_array($acc) ? (float) ($acc['subtotal'] ?? 0) : (float) ($acc->subtotal ?? 0);
                $accTax += is_array($acc) ? (float) ($acc['tax'] ?? 0) : (float) ($acc->tax ?? 0);
            }

            if ($profitMarkup > 0) {
                $accSubtotal += $profitMarkup;
                $markupApplied = true;
            }

            $result->push((object) [
                'id' => null,
                'product_id' => null,
                'product' => (object) [
                    'id' => null,
                    'name' => 'Solar Mounting Structures, Surge Protection (SPD), DC/AC Switchgear & Electrical Accessories',
                    'formatted_warranty' => '5 - 10 Years Component Warranty',
                ],
                'description' => 'Balance of System (BOS) Accessories & Hardware (Anodized Aluminum Rails, Mid/End Clamps, DC/AC SPDs, Circuit Breakers, DC/AC Solar Cables, Trunking & Enclosures)',
                'warranty_text' => '5 - 10 Years Component Warranty',
                'quantity' => 1.0,
                'formatted_quantity' => '1 Set',
                'unit' => 'Set',
                'unit_price' => round($accSubtotal, 2),
                'tax' => round($accTax, 2),
                'subtotal' => round($accSubtotal, 2),
                'cost_price' => 0.0,
                'hide_on_customer_pdf' => false,
                'is_summary' => true,
            ]);
        }

        // 5. Line 5: Consolidated Logistics & Turnkey Installation Services
        if ($services->isNotEmpty() || ($consolidate && $profitMarkup > 0 && ! $markupApplied)) {
            $srvSubtotal = 0.0;
            $srvTax = 0.0;

            foreach ($services as $srv) {
                $srvSubtotal += is_array($srv) ? (float) ($srv['subtotal'] ?? 0) : (float) ($srv->subtotal ?? 0);
                $srvTax += is_array($srv) ? (float) ($srv['tax'] ?? 0) : (float) ($srv->tax ?? 0);
            }

            if ($profitMarkup > 0 && ! $markupApplied) {
                $srvSubtotal += $profitMarkup;
                $markupApplied = true;
            }

            $result->push((object) [
                'id' => null,
                'product_id' => null,
                'product' => (object) [
                    'id' => null,
                    'name' => 'Site Transport, Mechanical Mounting & Turnkey Electrical Installation',
                    'formatted_warranty' => 'Turnkey Workmanship & Testing Certified',
                ],
                'description' => 'Site Logistics, Transport & Turnkey Installation Services',
                'warranty_text' => 'Turnkey Workmanship & Testing Certified',
                'quantity' => 1.0,
                'formatted_quantity' => '1 Job',
                'unit' => 'Job',
                'unit_price' => round($srvSubtotal, 2),
                'tax' => round($srvTax, 2),
                'subtotal' => round($srvSubtotal, 2),
                'cost_price' => 0.0,
                'hide_on_customer_pdf' => false,
                'is_summary' => true,
            ]);
        }

        return $result;
    }

    /**
     * Normalize individual item to ensure consistent object structure and formatted quantity.
     */
    protected static function normalizeItem(mixed $item): object
    {
        if (is_array($item)) {
            $desc = $item['description'] ?? 'Item';
            $prodName = $item['product_name'] ?? $desc;
            $qty = (float) ($item['quantity'] ?? 1);

            return (object) [
                'id' => $item['id'] ?? null,
                'product_id' => $item['product_id'] ?? null,
                'product' => (object) [
                    'id' => $item['product_id'] ?? null,
                    'name' => $prodName,
                    'formatted_warranty' => $item['warranty_text'] ?? null,
                ],
                'description' => $desc,
                'warranty_text' => $item['warranty_text'] ?? null,
                'quantity' => $qty,
                'formatted_quantity' => number_format($qty, 0),
                'unit_price' => (float) ($item['unit_price'] ?? 0),
                'tax' => (float) ($item['tax'] ?? 0),
                'subtotal' => (float) ($item['subtotal'] ?? 0),
                'hide_on_customer_pdf' => (bool) ($item['hide_on_customer_pdf'] ?? false),
                'is_summary' => false,
            ];
        }

        if ($item instanceof QuotationItem || $item instanceof InvoiceItem) {
            $item->formatted_quantity = number_format((float) $item->quantity, 0);
            $item->is_summary = false;

            return $item;
        }

        if (is_object($item)) {
            if (! isset($item->formatted_quantity)) {
                $item->formatted_quantity = number_format((float) ($item->quantity ?? 1), 0);
            }
            $item->is_summary = $item->is_summary ?? false;

            return $item;
        }

        return (object) $item;
    }

    /**
     * Intelligently classify an item into:
     * - panel
     * - inverter
     * - battery
     * - service
     * - accessory
     */
    public static function classifyItem(mixed $item): string
    {
        $categoryName = '';
        $categorySlug = '';
        $productType = '';
        $sku = '';
        $productName = '';
        $description = '';

        if (is_array($item)) {
            $description = $item['description'] ?? '';
            $sku = $item['sku'] ?? '';
            $productName = $item['product_name'] ?? '';
            $productType = $item['type'] ?? '';
            $categoryName = $item['category_name'] ?? '';
            $categorySlug = $item['category_slug'] ?? '';
        } elseif (is_object($item)) {
            $description = $item->description ?? '';
            if (isset($item->product) && is_object($item->product)) {
                $sku = $item->product->sku ?? '';
                $productName = $item->product->name ?? '';
                $productType = $item->product->type ?? '';
                if (isset($item->product->category) && is_object($item->product->category)) {
                    $categoryName = $item->product->category->name ?? '';
                    $categorySlug = $item->product->category->slug ?? '';
                }
            }
        }

        $haystack = strtolower(trim("{$categoryName} {$categorySlug} {$sku} {$productName} {$description}"));

        // 1. Explicit Service Type or Service Category
        if (
            $productType === 'service' ||
            str_contains($categorySlug, 'service')
        ) {
            return 'service';
        }

        // 2. Keywords that identify hardware accessories / BOS
        $isAccessoryKeyword = (
            str_contains($haystack, 'rail') ||
            str_contains($haystack, 'clamp') ||
            str_contains($haystack, 'bracket') ||
            str_contains($haystack, 'cable') ||
            str_contains($haystack, 'wire') ||
            str_contains($haystack, 'wiring') ||
            str_contains($haystack, 'breaker') ||
            str_contains($haystack, 'spd') ||
            str_contains($haystack, 'surge') ||
            str_contains($haystack, 'switch') ||
            str_contains($haystack, 'isolator') ||
            str_contains($haystack, 'fuse') ||
            str_contains($haystack, 'enclosure') ||
            str_contains($haystack, 'trunking') ||
            str_contains($haystack, 'conduit') ||
            str_contains($haystack, 'rod') ||
            str_contains($haystack, 'earth') ||
            str_contains($haystack, 'grounding') ||
            str_contains($haystack, 'box') ||
            str_contains($haystack, 'connector') ||
            str_contains($haystack, 'mc4') ||
            str_contains($haystack, 'lug') ||
            str_contains($haystack, 'mounting') ||
            str_contains($haystack, 'hardware') ||
            str_contains($haystack, 'accessory') ||
            str_contains($haystack, 'accessories') ||
            str_contains($categorySlug, 'accessory') ||
            str_contains($categorySlug, 'mounting') ||
            str_contains($categorySlug, 'hardware')
        );

        if ($isAccessoryKeyword) {
            return 'accessory';
        }

        // 3. Service / Logistics / Installation keywords
        if (
            str_contains($categorySlug, 'transport') ||
            str_contains($categorySlug, 'logistics') ||
            str_contains($categorySlug, 'installation') ||
            str_contains($haystack, 'transport') ||
            str_contains($haystack, 'logistics') ||
            str_contains($haystack, 'installation') ||
            str_contains($haystack, 'installing') ||
            str_contains($haystack, 'customs') ||
            str_contains($haystack, 'import duty') ||
            str_contains($haystack, 'port duty') ||
            str_contains($haystack, 'duties') ||
            (str_contains($haystack, 'duty') && ! str_contains($haystack, 'heavy duty')) ||
            str_contains($haystack, 'labor') ||
            str_contains($haystack, 'labour') ||
            str_contains($haystack, 'freight') ||
            str_contains($haystack, 'delivery') ||
            str_contains($haystack, 'supervision') ||
            str_contains($haystack, 'overhead') ||
            str_contains($haystack, 'commissioning') ||
            str_contains($haystack, 'turnkey') ||
            str_contains($haystack, 'clearance')
        ) {
            return 'service';
        }

        // 2. Solar PV Panels
        if (
            str_contains($categorySlug, 'panel') ||
            str_contains($categorySlug, 'solar-pv') ||
            str_starts_with(strtoupper($sku), 'PANEL-') ||
            str_starts_with(strtoupper($sku), 'PV-') ||
            str_contains($haystack, 'bifacial') ||
            str_contains($haystack, 'pv panel') ||
            str_contains($haystack, 'solar panel') ||
            str_contains($haystack, 'solar module') ||
            str_contains($haystack, 'topcon') ||
            str_contains($haystack, 'panel') ||
            str_contains($haystack, 'module')
        ) {
            return 'panel';
        }

        // 3. Inverters
        if (
            str_contains($categorySlug, 'inverter') ||
            str_starts_with(strtoupper($sku), 'INV-') ||
            str_contains($haystack, 'inverter') ||
            str_contains($haystack, 'growatt') ||
            str_contains($haystack, 'goodwe') ||
            str_contains($haystack, 'deye') ||
            str_contains($haystack, 'huawei') ||
            str_contains($haystack, 'fronius') ||
            str_contains($haystack, 'solis') ||
            str_contains($haystack, 'sma') ||
            str_contains($haystack, 'sungrow')
        ) {
            return 'inverter';
        }

        // 4. Battery Storage / BESS
        if (
            str_contains($categorySlug, 'battery') ||
            str_contains($categorySlug, 'bess') ||
            str_contains($categorySlug, 'storage') ||
            str_starts_with(strtoupper($sku), 'BAT-') ||
            str_contains($haystack, 'battery') ||
            str_contains($haystack, 'bess') ||
            str_contains($haystack, 'lifepo4') ||
            str_contains($haystack, 'lithium') ||
            str_contains($haystack, 'storage pack') ||
            str_contains($haystack, 'dyness') ||
            str_contains($haystack, 'pylontech')
        ) {
            return 'battery';
        }

        return 'accessory';
    }
}

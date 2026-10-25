<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt - {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            margin: 4mm 4mm;
            size: 80mm 220mm;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10px;
            color: #000000;
            line-height: 1.35;
            margin: 0;
            padding: 2px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-left {
            text-align: left;
        }
        .bold {
            font-weight: bold;
        }
        .company-header {
            margin-bottom: 6px;
        }
        .company-name {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .divider {
            border-top: 1px dashed #000000;
            margin: 6px 0;
        }
        .double-divider {
            border-top: 2px solid #000000;
            margin: 6px 0;
        }
        .meta-table {
            width: 100%;
            font-size: 9.5px;
            margin-bottom: 4px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
            margin: 4px 0;
        }
        .items-table th {
            border-bottom: 1px dashed #000;
            padding: 3px 0;
            font-size: 9px;
            text-transform: uppercase;
        }
        .items-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .totals-table {
            width: 100%;
            font-size: 10px;
            margin: 4px 0;
        }
        .totals-table td {
            padding: 2px 0;
        }
        .grand-total {
            font-size: 12px;
            font-weight: bold;
        }
        .footer {
            margin-top: 10px;
            font-size: 8.5px;
            color: #333;
        }
    </style>
    @php
        $resolveImagePath = function (?string $path): ?string {
            if (empty($path)) {
                return null;
            }

            $resolved = null;
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                $resolved = \Illuminate\Support\Facades\Storage::disk('public')->path($path);
            } elseif (file_exists(public_path('storage/' . $path))) {
                $resolved = public_path('storage/' . $path);
            } elseif (file_exists(storage_path('app/public/' . $path))) {
                $resolved = storage_path('app/public/' . $path);
            } elseif (file_exists($path)) {
                $resolved = realpath($path) ?: $path;
            }

            if (! $resolved || ! file_exists($resolved)) {
                return null;
            }

            $extension = strtolower(pathinfo($resolved, PATHINFO_EXTENSION));
            if ($extension === 'svg') {
                return null;
            }

            return $resolved;
        };

        $logoPath = $resolveImagePath($company->logo_path ?? ($company->settings['logo_path'] ?? null));
        $currencySymbol = method_exists($company, 'getCurrencySymbol') ? $company->getCurrencySymbol() : ($company->currency ?? 'Rs.');
    @endphp
</head>
<body>

    <div class="company-header text-center">
        @if($logoPath)
            <div style="margin-bottom: 5px;">
                <img src="{{ $logoPath }}" style="max-height: 40px; max-width: 140px; margin: 0 auto; display: block;" alt="{{ $company->name }}">
            </div>
        @endif
        <div class="company-name">{{ $company->name ?? config('app.name', 'PixelCraft SaaS') }}</div>
        <div>{{ $company->address ?? 'Colombo, Sri Lanka' }}</div>
        @if(!empty($company->phone)) <div>Tel: {{ $company->phone }}</div> @endif
        @if(!empty($company->br_number)) <div>BR No: {{ $company->br_number }}</div> @endif
        @if(!empty($company->tax_number)) <div>VAT/Tax: {{ $company->tax_number }}</div> @endif
    </div>

    <div class="divider"></div>

    <div class="text-center bold" style="font-size: 11px; margin-bottom: 4px;">
        *** POS SALES RECEIPT ***
    </div>

    <table class="meta-table">
        <tr>
            <td class="text-left">Receipt No:</td>
            <td class="text-right bold">{{ $invoice->invoice_number }}</td>
        </tr>
        <tr>
            <td class="text-left">Date & Time:</td>
            <td class="text-right">{{ $invoice->created_at ? $invoice->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td class="text-left">Customer:</td>
            <td class="text-right bold">{{ $contact->name ?? 'Walk-in Customer' }}</td>
        </tr>
        @if(!empty($contact->phone))
            <tr>
                <td class="text-left">Phone:</td>
                <td class="text-right">{{ $contact->phone }}</td>
            </tr>
        @endif
        @if(!empty($contact->ceb_account_no))
            <tr>
                <td class="text-left">CEB A/C:</td>
                <td class="text-right">{{ $contact->ceb_account_no }}</td>
            </tr>
        @endif
    </table>

    <div class="divider"></div>

    <table class="items-table">
        <thead>
            <tr>
                <th class="text-left" style="width: 50%;">Item</th>
                <th class="text-center" style="width: 15%;">Qty</th>
                <th class="text-right" style="width: 35%;">Total (Rs.)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td class="text-left">
                        <div class="bold">{{ $item->product->name ?? $item->description }}</div>
                        <div style="font-size: 8.5px; color: #444;">{{ number_format((float) $item->quantity, 0) }} x {{ number_format((float) $item->unit_price, 2) }}</div>
                    </td>
                    <td class="text-center">{{ number_format((float) $item->quantity, 0) }}</td>
                    <td class="text-right bold">{{ number_format((float) $item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="divider"></div>

    <table class="totals-table">
        <tr>
            <td class="text-left">Subtotal:</td>
            <td class="text-right">{{ $currencySymbol }} {{ number_format((float) $invoice->subtotal, 2) }}</td>
        </tr>
        @if((float) $invoice->discount > 0)
            <tr>
                <td class="text-left">Discount:</td>
                <td class="text-right">- {{ $currencySymbol }} {{ number_format((float) $invoice->discount, 2) }}</td>
            </tr>
        @endif
        @if((float) $invoice->tax > 0)
            <tr>
                <td class="text-left">Tax:</td>
                <td class="text-right">{{ $currencySymbol }} {{ number_format((float) $invoice->tax, 2) }}</td>
            </tr>
        @endif
        <tr class="grand-total">
            <td class="text-left">NET TOTAL:</td>
            <td class="text-right">{{ $currencySymbol }} {{ number_format((float) $invoice->grand_total, 2) }}</td>
        </tr>
        <tr>
            <td class="text-left">Paid ({{ isset($payments[0]) ? strtoupper($payments[0]->method) : 'CASH' }}):</td>
            <td class="text-right bold">{{ $currencySymbol }} {{ number_format((float) $invoice->paid_amount, 2) }}</td>
        </tr>
        @if($changeDue > 0)
            <tr>
                <td class="text-left">Change Tendered:</td>
                <td class="text-right">{{ $currencySymbol }} {{ number_format((float) $changeDue, 2) }}</td>
            </tr>
        @endif
    </table>

    <div class="double-divider"></div>

    <div class="footer text-center">
        <div class="bold">THANK YOU FOR YOUR BUSINESS!</div>
        <div>Please retain this receipt for warranty claims.</div>
        <div style="margin-top: 4px;">Powered by {{ config('app.name', 'PixelCraft SaaS') }}</div>
        <div>{{ config('app.url', 'app.pixelcraftlk.com') }}</div>
    </div>

</body>
</html>

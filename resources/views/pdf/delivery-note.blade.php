<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Delivery Note - {{ $referenceNumber }}</title>
    @php
        $primaryColor = $company->settings['primary_color'] ?? '#047857';
        $tableHeaderColor = $company->settings['table_header_color'] ?? '#047857';
        $textColor = $company->settings['text_color'] ?? '#ffffff';

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

            $mime = @mime_content_type($resolved);
            if ($mime && str_contains(strtolower($mime), 'svg')) {
                return null;
            }

            return $resolved;
        };

        $logoPath = $resolveImagePath($company->logo_path ?? ($company->settings['logo_path'] ?? null));
        $signaturePath = $resolveImagePath($company->settings['signature_path'] ?? null);
    @endphp
    <style>
        @page {
            margin: 28px 32px 32px 32px;
            size: a4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.45;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        .header-table td {
            vertical-align: top;
        }
        .company-title {
            font-size: 19px;
            font-weight: bold;
            color: {{ $primaryColor }};
            letter-spacing: -0.5px;
            margin: 0 0 4px 0;
        }
        .company-subtitle {
            font-size: 11px;
            color: #64748b;
            margin: 0 0 6px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .doc-badge {
            background-color: {{ $primaryColor }};
            color: #ffffff;
            font-size: 13px;
            font-weight: bold;
            padding: 6px 14px;
            border-radius: 4px;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
        }
        .doc-meta {
            font-size: 11px;
            color: #334155;
            line-height: 1.6;
        }
        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            vertical-align: top;
            font-size: 10.5px;
            line-height: 1.5;
        }
        .card-heading {
            font-size: 11px;
            font-weight: bold;
            color: {{ $primaryColor }};
            text-transform: uppercase;
            margin-bottom: 6px;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 4px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: {{ $tableHeaderColor }};
            color: {{ $textColor }};
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px 10px;
            text-align: left;
            border: 1px solid {{ $tableHeaderColor }};
        }
        .items-table td {
            padding: 9px 10px;
            border: 1px solid #e2e8f0;
            font-size: 10.5px;
            vertical-align: top;
        }
        .items-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .badge-verified {
            display: inline-block;
            background: #dcfce7;
            color: #166534;
            font-size: 9.5px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 4px;
        }
        .terms-card {
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 22px;
            font-size: 10px;
            color: #475569;
            line-height: 1.5;
        }
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .signatures-table td {
            width: 33.33%;
            vertical-align: top;
            font-size: 10px;
            color: #475569;
            padding: 0 10px;
        }
        .sig-line {
            width: 90%;
            border-bottom: 1px solid #94a3b8;
            margin-top: 45px;
            margin-bottom: 4px;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td style="width: 58%;">
                @if($logoPath)
                    <div style="margin-bottom: 8px;">
                        <img src="{{ $logoPath }}" style="max-height: 65px; max-width: 180px; object-fit: contain;" alt="{{ $company->name }}">
                    </div>
                @endif
                <div class="company-title">{{ $company->name ?? 'Vertex Books Solutions' }}</div>
                <div class="company-subtitle">Inventory Dispatch & Material Gate Pass</div>
                <div style="color: #475569; font-size: 10px; line-height: 1.5;">
                    @if(!empty($company->address)) {{ $company->address }}<br> @endif
                    @if(!empty($company->phone)) Tel: {{ $company->phone }} | @endif
                    @if(!empty($company->email)) Email: {{ $company->email }}<br> @endif
                    @if(!empty($company->tax_number)) VAT/SVAT No: <strong>{{ $company->tax_number }}</strong> @endif
                </div>
            </td>
            <td style="width: 42%; text-align: right;">
                <div class="doc-badge">DELIVERY NOTE / GATE PASS</div>
                <div class="doc-meta">
                    <strong>Delivery Note No:</strong> DN-{{ $referenceNumber }}<br>
                    <strong>Ref Document:</strong> {{ $referenceNumber }}<br>
                    <strong>Dispatch Date:</strong> {{ now()->format('d M Y') }}<br>
                    <strong>Gate Pass Status:</strong> <span class="badge-verified">DISPATCH APPROVED</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Customer & Logistics Info -->
    <table class="info-grid">
        <tr>
            <td class="info-card" style="width: 50%;">
                <div class="card-heading">Deliver To (Consignee)</div>
                <strong>{{ $contact->name ?? 'Valued Customer' }}</strong><br>
                @if(!empty($contact->company_name))
                    <span>{{ $contact->company_name }}</span><br>
                @endif
                @if(!empty($contact->address))
                    <span>{{ $contact->address }}</span><br>
                @else
                    <span>Installation Site / Project Location</span><br>
                @endif
                @if(!empty($contact->phone))
                    <span>Contact Tel: {{ $contact->phone }}</span><br>
                @endif
                @if(!empty($contact->email))
                    <span>Email: {{ $contact->email }}</span>
                @endif
            </td>
            <td style="width: 4%;"></td>
            <td class="info-card" style="width: 46%;">
                <div class="card-heading">Logistics & Transportation Details</div>
                <strong>Transport Vehicle No:</strong> {{ $vehicleNo ?? 'WP-CAD-8821 / Company Lorry' }}<br>
                <strong>Driver / Dispatcher Name:</strong> {{ $driverName ?? 'Stores Logistics Officer' }}<br>
                <strong>Delivery Mode:</strong> Direct Commercial Site Delivery<br>
                <strong>Dispatch Time:</strong> {{ now()->format('h:i A') }}<br>
                <strong>Security Seal / Gate Pass:</strong> VERIFIED & CLEARED
            </td>
        </tr>
    </table>

    <!-- Line Items Table (NO PRICES) -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 6%; text-align: center;">#</th>
                <th style="width: 18%;">ITEM CODE / SKU</th>
                <th style="width: 46%;">DESCRIPTION / ENGINEERING SPECIFICATIONS</th>
                <th style="width: 18%;">WARRANTY & SERVICING</th>
                <th style="width: 12%; text-align: right;">QTY DELIVERED</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
                @php
                    $product = $item->product ?? null;
                    $warranty = $product->warranty_months ?? null;
                @endphp
                <tr>
                    <td style="text-align: center; color: #64748b; font-weight: bold;">{{ $index + 1 }}</td>
                    <td style="font-family: monospace; font-size: 10px; font-weight: bold; color: #334155;">
                        {{ $product?->sku ?? 'CUSTOM-ITEM' }}
                    </td>
                    <td>
                        <strong style="color: #0f172a;">{{ $item->description ?? ($product?->name ?? 'Delivered Goods') }}</strong>
                        @if($product && $product->description && $product->description !== $item->description)
                            <div style="color: #64748b; font-size: 9.5px; margin-top: 3px;">
                                {{ Str::limit($product->description, 100) }}
                            </div>
                        @endif
                    </td>
                    <td>
                        @if($warranty)
                            <span class="badge-verified">{{ $warranty }} Months Official Warranty</span>
                        @elseif($product?->track_serial)
                            <span style="font-size: 9.5px; color: #475569;">Serial Verified</span>
                        @else
                            <span style="font-size: 9.5px; color: #94a3b8;">Standard Manufacturer</span>
                        @endif
                    </td>
                    <td style="text-align: right; font-weight: bold; font-size: 12px; color: #047857;">
                        {{ number_format((float) $item->quantity, 0) }} Units
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #94a3b8; padding: 20px;">
                        No delivery line items recorded.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Delivery Declarations & Inspection Notes -->
    <div class="terms-card">
        <strong>Dispatch & Acceptance Declarations:</strong><br>
        1. All listed equipment and hardware items have been inspected, tested, and handed over in clean, brand-new, and undamaged condition.<br>
        2. Any physical transit damage, missing quantities, or packaging discrepancy must be noted immediately on this Delivery Note upon receipt.<br>
        3. Equipment serial numbers will be registered under the customer's warranty file upon receipt endorsement.
    </div>

    <!-- Signatures Section (3 columns: Stores, Driver, Customer) -->
    <table class="signatures-table">
        <tr>
            <td>
                @if($signaturePath)
                    <div style="height: 40px; margin-bottom: 5px;">
                        <img src="{{ $signaturePath }}" style="max-height: 40px; max-width: 140px; object-fit: contain;" alt="Signature">
                    </div>
                @else
                    <div class="sig-line"></div>
                @endif
                <strong>Dispatched By (Storekeeper / QC)</strong><br>
                <span>Vertex Books Warehousing</span><br>
                <span>Date: {{ now()->format('d M Y') }}</span>
            </td>
            <td>
                <div class="sig-line"></div>
                <strong>Delivered By (Driver / Logistics)</strong><br>
                <span>Name / Signature: __________________</span><br>
                <span>Date & Time: _______________________</span>
            </td>
            <td>
                <div class="sig-line"></div>
                <strong>Received in Good Condition</strong><br>
                <span>Customer / Site Representative Signature</span><br>
                <span>Full Name & NIC: ___________________</span>
            </td>
        </tr>
    </table>

</body>
</html>

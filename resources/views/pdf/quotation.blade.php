<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quotation - {{ $quotation->quote_number }}</title>
    @php
        $primaryColor = $company->settings['primary_color'] ?? '#047857';
        $tableHeaderColor = $company->settings['table_header_color'] ?? '#047857';
        $textColor = $company->settings['text_color'] ?? '#ffffff';
        $showSolarSpecs = $company->settings['show_solar_specs'] ?? true;
        $showCebAccount = $company->settings['show_ceb_account'] ?? true;
        $showMilestones = $company->settings['show_milestones'] ?? true;

        $contact = $contact ?? ($quotation->contact ?? null);
        $items = $items ?? ($quotation->items ?? []);

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
            margin-bottom: 20px;
        }
        .header-table td {
            vertical-align: top;
        }
        .company-title {
            font-size: 20px;
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
            font-size: 10.5px;
            color: #334155;
            line-height: 1.5;
        }
        .doc-meta strong {
            color: #0f172a;
        }
        .info-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 12px 0;
            margin-bottom: 16px;
        }
        .info-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
            vertical-align: top;
        }
        .card-heading {
            font-size: 11px;
            font-weight: bold;
            color: {{ $primaryColor }};
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }
        .ceb-highlight {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            color: #92400e;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            margin-top: 6px;
            display: inline-block;
        }
        .specs-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            margin-bottom: 18px;
        }
        .specs-table td {
            padding: 8px 12px;
            font-size: 10.5px;
            border-bottom: 1px solid #dcfce7;
        }
        .specs-table td:nth-child(odd) {
            color: {{ $primaryColor }};
            font-weight: bold;
            width: 25%;
        }
        .specs-table td:nth-child(even) {
            color: #0f172a;
            width: 25%;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .items-table th {
            background-color: {{ $tableHeaderColor }};
            color: {{ $textColor }};
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            text-align: left;
        }
        .items-table td {
            padding: 8px 10px;
            font-size: 10.5px;
            border-bottom: 1px solid #e2e8f0;
        }
        .items-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals-container {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        .totals-container td {
            vertical-align: top;
        }
        .milestones-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 12px;
        }
        .milestones-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        .milestones-table td {
            padding: 4px 6px;
            border-bottom: 1px dashed #cbd5e1;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 5px 8px;
            font-size: 11px;
        }
        .totals-table .grand-row {
            background-color: {{ $primaryColor }};
            color: #ffffff;
            font-weight: bold;
            font-size: 13px;
        }
        .totals-table .grand-row td {
            padding: 8px 10px;
            border-radius: 4px;
        }
        .terms-box {
            background-color: #f8fafc;
            border-left: 3px solid {{ $primaryColor }};
            padding: 10px 14px;
            font-size: 9.5px;
            color: #475569;
            margin-bottom: 24px;
        }
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .signatures-table td {
            width: 50%;
            vertical-align: top;
            font-size: 10px;
            color: #475569;
        }
        .sig-line {
            width: 80%;
            border-bottom: 1px solid #94a3b8;
            margin-top: 40px;
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
                        <img src="{{ $logoPath }}" style="max-height: 70px; max-width: 180px; object-fit: contain;" alt="{{ $company->name }}">
                    </div>
                @endif
                <div class="company-title">{{ $company->name ?? 'Vertex Books Solar Energy' }}</div>
                <div class="company-subtitle">Clean Energy Solutions & Engineering</div>
                <div style="color: #475569; font-size: 10px; line-height: 1.5;">
                    @if(!empty($company->address)) {{ $company->address }}<br> @endif
                    @if(!empty($company->phone)) Phone: {{ $company->phone }} | @endif
                    @if(!empty($company->email)) Email: {{ $company->email }}<br> @endif
                    @if(!empty($company->tax_number)) Tax / VAT No: <strong>{{ $company->tax_number }}</strong> @endif
                </div>
            </td>
            <td style="width: 42%; text-align: right;">
                <div class="doc-badge">SOLAR PV QUOTATION</div>
                <div class="doc-meta">
                    <strong>Quote Ref:</strong> {{ $quotation->quote_number }}<br>
                    <strong>Date:</strong> {{ $quotation->quote_date ? $quotation->quote_date->format('d M Y') : now()->format('d M Y') }}<br>
                    <strong>Valid Until:</strong> {{ $quotation->valid_until ? $quotation->valid_until->format('d M Y') : '30 Days' }}<br>
                    <strong>Status:</strong> <span style="text-transform: capitalize; color: {{ $primaryColor }}; font-weight: bold;">{{ $quotation->status }}</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Client & Project Site Info -->
    <table class="info-grid">
        <tr>
            <td class="info-card" style="width: {{ $showCebAccount ? '50%' : '100%' }};">
                <div class="card-heading">Client & Billing Information</div>
                <strong style="font-size: 12px; color: #0f172a;">{{ $contact->name ?? 'Valued Customer' }}</strong><br>
                @if(!empty($contact->address)) <span style="color: #475569;">{{ $contact->address }}</span><br> @endif
                @if(!empty($contact->phone)) Phone: {{ $contact->phone }}<br> @endif
                @if(!empty($contact->email)) Email: {{ $contact->email }}<br> @endif
                @if(!empty($contact->tax_no)) NIC / Tax No: {{ $contact->tax_no }}<br> @endif
            </td>
            @if($showCebAccount)
                <td class="info-card" style="width: 50%;">
                    <div class="card-heading">CEB / Utility Grid Connection Details</div>
                    <div><strong>Utility Provider:</strong> Ceylon Electricity Board (CEB) / LECO</div>
                    @if(!empty($contact->ceb_account_no))
                        <div class="ceb-highlight">
                            CEB Account No: {{ $contact->ceb_account_no }}
                        </div>
                    @else
                        <div style="color: #64748b; font-style: italic; margin-top: 4px;">CEB Account No: To be verified prior to grid application</div>
                    @endif
                    <div style="font-size: 10px; color: #64748b; margin-top: 6px;">
                        Tariff Category: Net-Accounting / Net-Plus Solar PV Scheme
                    </div>
                </td>
            @endif
        </tr>
    </table>

    <!-- Solar Technical Specifications Block -->
    @if($showSolarSpecs)
        <div style="font-size: 11px; font-weight: bold; color: {{ $primaryColor }}; text-transform: uppercase; margin-bottom: 6px;">
            Solar Engineering & System Sizing Specifications
        </div>
        <table class="specs-table">
            <tr>
                <td>System Capacity:</td>
                <td><strong>{{ $quotation->system_kw ? number_format((float) $quotation->system_kw, 2) . ' kWp DC' : 'Custom Sizing' }}</strong></td>
                <td>Grid Phase:</td>
                <td><strong>{{ $quotation->phase_type === 'three' ? 'Three Phase (400V)' : 'Single Phase (230V)' }}</strong></td>
            </tr>
            <tr>
                <td>Roof Structure:</td>
                <td>{{ $quotation->roof_type ?? 'Tile / Corrugated Mounting' }}</td>
                <td>Inverter Model:</td>
                <td><strong>{{ $quotation->inverter_brand ?? 'Grid-Tied On-Grid Inverter' }}</strong></td>
            </tr>
            <tr>
                <td>Solar PV Panels:</td>
                <td colspan="3"><strong>{{ $quotation->panel_brand ?? 'Tier 1 High-Efficiency Bifacial Solar Modules' }}</strong></td>
            </tr>
        </table>
    @endif

    @php
        $mode = $mode ?? ($quotation->price_display_mode ?? 'itemized');
        $isPackageMode = ($mode === 'package_total_only');
        $visibleItems = collect($items)->filter(function ($item) use ($isPackageMode) {
            if ($isPackageMode && (bool) ($item->hide_on_customer_pdf ?? false)) {
                return false;
            }
            return true;
        });
    @endphp

    <!-- Quotation Line Items Table -->
    @if ($isPackageMode)
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 6%;" class="text-center">#</th>
                    <th style="width: 52%;">Description / Model</th>
                    <th style="width: 14%;" class="text-center">Quantity</th>
                    <th style="width: 28%;">Remarks / Warranty</th>
                </tr>
            </thead>
            <tbody>
                @forelse($visibleItems->values() as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $item->product->name ?? $item->description }}</strong>
                            @if($item->product && $item->product->name !== $item->description)
                                <div style="font-size: 9.5px; color: #64748b;">{{ $item->description }}</div>
                            @endif
                        </td>
                        <td class="text-center">{{ number_format((float) $item->quantity, 0) }}</td>
                        <td style="color: #047857; font-weight: bold;">
                            {{ $item->warranty_text ?: ($item->product?->formatted_warranty ?: '-') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center" style="padding: 16px; color: #94a3b8;">No items listed in this quotation.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @else
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;" class="text-center">#</th>
                    <th style="width: 48%;">Item & Technical Description</th>
                    <th style="width: 8%;" class="text-center">Qty</th>
                    <th style="width: 15%;" class="text-right">Unit Price (Rs.)</th>
                    <th style="width: 10%;" class="text-right">Tax (Rs.)</th>
                    <th style="width: 14%;" class="text-right">Total (Rs.)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $item->product->name ?? $item->description }}</strong>
                            @if($item->product && $item->product->name !== $item->description)
                                <div style="font-size: 9.5px; color: #64748b;">{{ $item->description }}</div>
                            @endif
                            @if(!empty($item->warranty_text))
                                <div style="font-size: 9px; color: #059669; font-weight: bold;">Warranty: {{ $item->warranty_text }}</div>
                            @endif
                        </td>
                        <td class="text-center">{{ number_format((float) $item->quantity, 0) }}</td>
                        <td class="text-right">{{ number_format((float) $item->unit_price, 2) }}</td>
                        <td class="text-right">{{ number_format((float) ($item->tax ?? 0), 2) }}</td>
                        <td class="text-right"><strong>{{ number_format((float) $item->subtotal, 2) }}</strong></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 16px; color: #94a3b8;">No items listed in this quotation.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    <!-- Milestones Breakdown & Totals Table -->
    <table class="totals-container">
        <tr>
            @if($showMilestones)
                <td style="width: 52%; padding-right: 12px;">
                    <div class="milestones-box">
                        <div class="card-heading">Milestone Payment Breakdown</div>
                        <table class="milestones-table">
                            <tr>
                                <td><strong>Milestone 1: 50% Advance</strong><br><span style="color: #64748b;">Confirmation & equipment reservation</span></td>
                                <td class="text-right"><strong>Rs. {{ number_format((float) $quotation->grand_total * 0.50, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td><strong>Milestone 2: 40% Delivery</strong><br><span style="color: #64748b;">Upon site arrival of panels & inverter</span></td>
                                <td class="text-right"><strong>Rs. {{ number_format((float) $quotation->grand_total * 0.40, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td><strong>Milestone 3: 10% Grid Connection</strong><br><span style="color: #64748b;">CEB net-metering & commissioning</span></td>
                                <td class="text-right"><strong>Rs. {{ number_format((float) $quotation->grand_total * 0.10, 2) }}</strong></td>
                            </tr>
                        </table>
                    </div>
                </td>
            @else
                <td style="width: 52%; padding-right: 12px;">
                    @if(!empty($company->settings['bank_details']))
                        <div class="milestones-box">
                            <div class="card-heading">Remittance Information</div>
                            <div style="font-size: 10px; line-height: 1.5;">
                                {!! nl2br(e($company->settings['bank_details'])) !!}
                            </div>
                        </div>
                    @endif
                </td>
            @endif
            <td style="width: 48%;">
                <table class="totals-table">
                    <tr>
                        <td class="text-right" style="color: #64748b;">{{ $isPackageMode ? 'Package Subtotal:' : 'Subtotal:' }}</td>
                        <td class="text-right" style="width: 45%;">Rs. {{ number_format((float) $quotation->subtotal, 2) }}</td>
                    </tr>
                    @if((float) $quotation->discount > 0)
                        <tr>
                            <td class="text-right" style="color: #dc2626;">Promotional Discount:</td>
                            <td class="text-right" style="color: #dc2626;">- Rs. {{ number_format((float) $quotation->discount, 2) }}</td>
                        </tr>
                    @endif
                    @if((float) $quotation->tax > 0)
                        <tr>
                            <td class="text-right" style="color: #64748b;">Applicable Taxes:</td>
                            <td class="text-right">Rs. {{ number_format((float) $quotation->tax, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="grand-row">
                        <td class="text-right">{{ $isPackageMode ? 'Total Package Amount:' : 'Grand Total:' }}</td>
                        <td class="text-right">Rs. {{ number_format((float) $quotation->grand_total, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Terms & Conditions & Remittance -->
    <div class="terms-box">
        @php
            $bankAccounts = $company->getBankAccountsList();
        @endphp
        @if(!empty($bankAccounts))
            <div style="margin-bottom: 8px;">
                <strong>Electronic Bank Wire Remittance Details:</strong>
                <table style="width: 100%; border-collapse: separate; border-spacing: 6px 0; margin-top: 4px;">
                    <tr>
                        @foreach($bankAccounts as $acc)
                            <td style="width: {{ 100 / max(1, count($bankAccounts)) }}%; vertical-align: top; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 4px; padding: 6px 8px;">
                                <div style="font-weight: bold; font-size: 10px; color: {{ $primaryColor }}; border-bottom: 1px solid #e2e8f0; padding-bottom: 2px; margin-bottom: 3px;">
                                    🏛️ {{ $acc['bank'] ?? 'Bank Account' }}
                                </div>
                                <div style="font-size: 8.5px; color: #475569; line-height: 1.4;">
                                    <strong>A/C Name:</strong> {{ $acc['account_name'] ?? $company->name }}<br>
                                    <strong>A/C No:</strong> <span style="font-family: 'Courier New', monospace; font-weight: bold; font-size: 10px; color: {{ $primaryColor }}; letter-spacing: 0.05em;">{{ $acc['account_no'] ?? '-' }}</span>
                                    @if(!empty($acc['branch'])) <br><strong>Branch:</strong> {{ $acc['branch'] }} @endif
                                </div>
                            </td>
                        @endforeach
                    </tr>
                </table>
            </div>
        @endif
        <strong>Terms & Conditions:</strong><br>
        {{ !empty($quotation->terms) ? $quotation->terms : ($company->settings['default_quote_terms'] ?? '1. Quotation validity is 30 days from date of issue due to import currency fluctuations. 2. Milestone payments must be made per schedule above. 3. 25-30 Years Solar Panel Linear Performance Warranty, 5-10 Years Inverter Warranty, 1 Year Free Maintenance.') }}
    </div>

    <!-- Signatures -->
    <table class="signatures-table">
        <tr>
            <td>
                @if($signaturePath)
                    <div style="margin-bottom: 6px;">
                        <img src="{{ $signaturePath }}" style="max-height: 48px; max-width: 140px; object-fit: contain;" alt="Signature">
                    </div>
                @endif
                <div class="sig-line" style="{{ $signaturePath ? 'margin-top: 4px;' : '' }}"></div>
                <strong>Authorized Signatory / Stamp</strong><br>
                {{ $company->name ?? 'Vertex Books Solar Energy' }}
            </td>
            <td>
                <div class="sig-line"></div>
                <strong>Customer Acceptance & Agreement</strong><br>
                Date: ________________________
            </td>
        </tr>
    </table>

</body>
</html>

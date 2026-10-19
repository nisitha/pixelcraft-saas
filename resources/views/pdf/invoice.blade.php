<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $invoice->invoice_number }}</title>
    @php
        $primaryColor = $company->settings['primary_color'] ?? '#047857';
        $tableHeaderColor = $company->settings['table_header_color'] ?? '#047857';
        $textColor = $company->settings['text_color'] ?? '#ffffff';
        $showSolarSpecs = $company->settings['show_solar_specs'] ?? true;
        $showCebAccount = $company->settings['show_ceb_account'] ?? true;
        $showMilestones = $company->settings['show_milestones'] ?? true;

        $contact = $contact ?? ($invoice->contact ?? null);
        $quotation = $quotation ?? ($invoice->quotation ?? null);
        $items = $items ?? ($invoice->items ?? []);
        $payments = $payments ?? ($invoice->payments ?? []);

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
            font-size: 12px;
        }
        .totals-table .due-row {
            background-color: {{ $primaryColor }};
            color: #ffffff;
            font-weight: bold;
            font-size: 13px;
        }
        .totals-table .due-row td {
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
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid { background-color: #dcfce7; color: #166534; }
        .status-partial { background-color: #fef3c7; color: #92400e; }
        .status-unpaid { background-color: #fee2e2; color: #991b1b; }
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
                <div class="doc-badge">{{ $invoice->type === 'milestone' ? 'SOLAR MILESTONE INVOICE' : 'COMMERCIAL TAX INVOICE' }}</div>
                <div class="doc-meta">
                    <strong>Invoice No:</strong> {{ $invoice->invoice_number }}<br>
                    <strong>Date:</strong> {{ $invoice->invoice_date ? $invoice->invoice_date->format('d M Y') : now()->format('d M Y') }}<br>
                    <strong>Due Date:</strong> {{ $invoice->due_date ? $invoice->due_date->format('d M Y') : 'Due on receipt' }}<br>
                    @if(!empty($quotation ?? ($invoice->quotation ?? null)))
                        <strong>Originating Quote:</strong> {{ ($quotation ?? $invoice->quotation)->quote_number }}<br>
                    @endif
                    <strong>Status:</strong> 
                    <span class="status-badge {{ $invoice->status === 'paid' ? 'status-paid' : ($invoice->status === 'partial' ? 'status-partial' : 'status-unpaid') }}">
                        {{ $invoice->status }}
                    </span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Client & Project Site Info -->
    <table class="info-grid">
        <tr>
            <td class="info-card" style="width: {{ $showCebAccount ? '50%' : '100%' }};">
                <div class="card-heading">Billed To (Client Details)</div>
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
                        <div style="color: #64748b; font-style: italic; margin-top: 4px;">CEB Account No: Pending grid verification</div>
                    @endif
                    <div style="font-size: 10px; color: #64748b; margin-top: 6px;">
                        Billing Type: {{ ucfirst($invoice->type) }} Solar Billing Schedule
                    </div>
                </td>
            @endif
        </tr>
    </table>

    <!-- Solar Technical Specifications Block (if linked to quotation) -->
    @if($quotation && $showSolarSpecs)
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

    <!-- Invoice Line Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">#</th>
                <th style="width: 50%;">Item & Technical Description</th>
                <th style="width: 10%;" class="text-center">Qty</th>
                <th style="width: 15%;" class="text-right">Unit Price (Rs.)</th>
                <th style="width: 8%;" class="text-right">Tax (Rs.)</th>
                <th style="width: 12%;" class="text-right">Total (Rs.)</th>
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
                    </td>
                    <td class="text-center">{{ number_format((float) $item->quantity, 0) }}</td>
                    <td class="text-right">{{ number_format((float) $item->unit_price, 2) }}</td>
                    <td class="text-right">{{ number_format((float) ($item->tax ?? 0), 2) }}</td>
                    <td class="text-right"><strong>{{ number_format((float) $item->subtotal, 2) }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 16px; color: #94a3b8;">No items listed in this invoice.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Milestones Breakdown, Recorded Payments & Balances -->
    <table class="totals-container">
        <tr>
            @if($showMilestones)
                <td style="width: 52%; padding-right: 12px;">
                    <div class="milestones-box">
                        <div class="card-heading">Milestone Payment Breakdown & History</div>
                        <table class="milestones-table">
                            <tr>
                                <td><strong>Milestone 1 (50% Advance):</strong> Confirmation & Site Mobilization</td>
                                <td class="text-right"><strong>Rs. {{ number_format((float) $invoice->grand_total * 0.50, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td><strong>Milestone 2 (40% Delivery):</strong> Site Delivery of PV Panels & Inverter</td>
                                <td class="text-right"><strong>Rs. {{ number_format((float) $invoice->grand_total * 0.40, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td><strong>Milestone 3 (10% Grid Connection):</strong> CEB Testing & Net-Metering</td>
                                <td class="text-right"><strong>Rs. {{ number_format((float) $invoice->grand_total * 0.10, 2) }}</strong></td>
                            </tr>
                        </table>

                        @if(isset($payments) && count($payments) > 0)
                            <div style="margin-top: 8px; font-weight: bold; font-size: 10px; color: {{ $primaryColor }};">Payments Received to Date:</div>
                            <table class="milestones-table" style="margin-top: 4px;">
                                @foreach($payments as $payment)
                                    <tr>
                                        <td>{{ $payment->payment_date ? $payment->payment_date->format('d/m/Y') : '-' }} ({{ ucwords(str_replace('_', ' ', $payment->method)) }} - {{ $payment->reference_no }})</td>
                                        <td class="text-right" style="color: {{ $primaryColor }}; font-weight: bold;">- Rs. {{ number_format((float) $payment->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        @endif
                    </div>
                </td>
            @else
                <td style="width: 52%; padding-right: 12px;">
                    @if(isset($payments) && count($payments) > 0)
                        <div class="milestones-box">
                            <div class="card-heading">Payments Received to Date</div>
                            <table class="milestones-table" style="margin-top: 4px;">
                                @foreach($payments as $payment)
                                    <tr>
                                        <td>{{ $payment->payment_date ? $payment->payment_date->format('d/m/Y') : '-' }} ({{ ucwords(str_replace('_', ' ', $payment->method)) }} - {{ $payment->reference_no }})</td>
                                        <td class="text-right" style="color: {{ $primaryColor }}; font-weight: bold;">- Rs. {{ number_format((float) $payment->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    @endif
                </td>
            @endif
            <td style="width: 48%;">
                <table class="totals-table">
                    <tr>
                        <td class="text-right" style="color: #64748b;">Subtotal:</td>
                        <td class="text-right" style="width: 45%;">Rs. {{ number_format((float) $invoice->subtotal, 2) }}</td>
                    </tr>
                    @if((float) $invoice->discount > 0)
                        <tr>
                            <td class="text-right" style="color: #dc2626;">Discount:</td>
                            <td class="text-right" style="color: #dc2626;">- Rs. {{ number_format((float) $invoice->discount, 2) }}</td>
                        </tr>
                    @endif
                    @if((float) $invoice->tax > 0)
                        <tr>
                            <td class="text-right" style="color: #64748b;">Tax / VAT:</td>
                            <td class="text-right">Rs. {{ number_format((float) $invoice->tax, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="grand-row">
                        <td class="text-right">Invoice Grand Total:</td>
                        <td class="text-right">Rs. {{ number_format((float) $invoice->grand_total, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-right" style="color: #047857; font-weight: bold;">Amount Paid:</td>
                        <td class="text-right" style="color: #047857; font-weight: bold;">Rs. {{ number_format((float) $invoice->paid_amount, 2) }}</td>
                    </tr>
                    <tr class="due-row">
                        <td class="text-right">Balance Outstanding:</td>
                        <td class="text-right">Rs. {{ number_format((float) $invoice->due_amount, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Payment Instructions & Terms -->
    <div class="terms-box">
        <strong>Bank Transfer & Payment Remittance Details:</strong><br>
        @if(!empty($company->settings['bank_details']))
            {!! nl2br(e($company->settings['bank_details'])) !!}<br>
        @else
            Please remit payments to: <strong>{{ $company->name ?? 'Vertex Books Solutions' }}</strong> | Bank of Ceylon / Commercial Bank PLC | Account No: <strong>008492019482</strong> | Branch: Corporate City Branch.<br>
        @endif
        <em>Please quote Invoice Ref {{ $invoice->invoice_number }} as transaction description.</em>
        @if(!empty($company->settings['default_invoice_terms']))
            <div style="margin-top: 6px; padding-top: 4px; border-top: 1px dashed #cbd5e1;">
                <strong>Terms & Conditions:</strong> {!! nl2br(e($company->settings['default_invoice_terms'])) !!}
            </div>
        @endif
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
                <strong>Received & Acknowledged by Customer</strong><br>
                Date: ________________________
            </td>
        </tr>
    </table>

</body>
</html>

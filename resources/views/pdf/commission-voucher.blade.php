<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Commission Voucher - {{ $payout->voucher_number }}</title>
    @php
        $company = $company ?? $payout->company;
        $agent = $agent ?? $payout->salesAgent;
        $invoice = $invoice ?? $payout->invoice;
        $paperSize = strtolower($payout->paper_size ?? 'a4');
        $orientation = strtolower($payout->orientation ?? 'portrait');

        $isA5 = $paperSize === 'a5';
        $margin = $isA5 ? '8mm' : '12mm';

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

        $logoPath = $resolveImagePath($company?->logo_path ?? ($company?->settings['logo_path'] ?? null));
        $signaturePath = $resolveImagePath($company?->settings['signature_path'] ?? null);
    @endphp

    <style>
        @page {
            size: {{ $paperSize }} {{ $orientation }};
            margin: {{ $margin }};
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 0;
            line-height: 1.4;
            font-size: {{ $isA5 ? '9px' : '11px' }};
            background-color: #ffffff;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            border-bottom: 2px solid #0f766e;
            padding-bottom: 8px;
        }

        .header-table td {
            vertical-align: middle;
        }

        .company-name {
            font-size: {{ $isA5 ? '13px' : '16px' }};
            font-weight: 800;
            color: #0f766e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 2px 0;
        }

        .voucher-title {
            font-size: {{ $isA5 ? '14px' : '18px' }};
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin: 0;
        }

        .voucher-subtitle {
            font-size: {{ $isA5 ? '8px' : '9.5px' }};
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .meta-pill {
            display: inline-block;
            background: #f0fdfa;
            border: 1px solid #99f6e4;
            color: #0f766e;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: 700;
            font-size: {{ $isA5 ? '8.5px' : '10px' }};
            margin-top: 4px;
        }

        /* 2-Column Info Grid */
        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .info-grid td {
            width: 50%;
            vertical-align: top;
            padding: 4px 6px;
        }

        .card-box {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: {{ $isA5 ? '6px 8px' : '10px 12px' }};
            background-color: #f8fafc;
            height: 100%;
        }

        .card-title {
            font-size: {{ $isA5 ? '9px' : '11px' }};
            font-weight: 800;
            color: #0f766e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }

        .card-row {
            margin-bottom: 3px;
        }

        .card-row .label {
            font-weight: 600;
            color: #64748b;
            display: inline-block;
            width: 38%;
        }

        .card-row .val {
            font-weight: 700;
            color: #1e293b;
            display: inline-block;
            width: 60%;
        }

        /* Settlement Table */
        .settlement-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 14px;
        }

        .settlement-table th {
            background-color: #0f766e;
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: {{ $isA5 ? '8.5px' : '10px' }};
            padding: {{ $isA5 ? '4px 6px' : '6px 8px' }};
            border: 1px solid #0f766e;
        }

        .settlement-table td {
            border: 1px solid #cbd5e1;
            padding: {{ $isA5 ? '5px 6px' : '8px 10px' }};
            vertical-align: middle;
        }

        .amount-highlight {
            background-color: #ecfdf5;
            color: #047857;
            font-weight: 800;
            font-size: {{ $isA5 ? '12px' : '15px' }};
            text-align: right;
        }

        /* Signatures */
        .sig-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: {{ $isA5 ? '16px' : '26px' }};
        }

        .sig-grid td {
            width: 33.33%;
            text-align: center;
            vertical-align: bottom;
            padding: 0 10px;
        }

        .sig-line {
            border-top: 1.5px dashed #64748b;
            margin-bottom: 4px;
            width: 85%;
            margin-left: auto;
            margin-right: auto;
        }

        .sig-title {
            font-weight: 700;
            color: #1e293b;
            font-size: {{ $isA5 ? '8px' : '9.5px' }};
            text-transform: uppercase;
        }

        .sig-sub {
            font-size: {{ $isA5 ? '7px' : '8px' }};
            color: #64748b;
        }

        .footer-note {
            margin-top: 14px;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
            font-size: {{ $isA5 ? '7px' : '8.5px' }};
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <table class="header-table">
        <tr>
            @if ($logoPath)
                <td style="width: 20%;">
                    <img src="{{ $logoPath }}" style="max-height: {{ $isA5 ? '36px' : '50px' }}; max-width: 130px;" alt="Logo">
                </td>
                <td style="width: 50%;">
            @else
                <td style="width: 70%;">
            @endif
                <div class="company-name">{{ $company->name ?? 'VERTEX HOME SOLUTIONS (PVT) LTD' }}</div>
                <div class="voucher-title">COMMISSION PAYMENT VOUCHER</div>
                <div class="voucher-subtitle">Sales Agent / Referrer Commission Settlement Record</div>
            </td>
            <td style="width: 30%; text-align: right;">
                <div class="meta-pill">VOUCHER: {{ $payout->voucher_number }}</div>
                <div style="font-weight: 700; color: #475569; margin-top: 3px;">
                    Date: {{ $payout->payout_date?->format('d M Y') ?? now()->format('d M Y') }}
                </div>
                <div style="font-size: {{ $isA5 ? '7.5px' : '9px' }}; color: #64748b;">
                    Status: <strong style="color: #059669; text-transform: uppercase;">{{ $payout->status }}</strong>
                </div>
            </td>
        </tr>
    </table>

    {{-- Agent & Project 2-Column Info --}}
    <table class="info-grid">
        <tr>
            <td>
                <div class="card-box">
                    <div class="card-title">Beneficiary Sales Agent Details</div>
                    <div class="card-row">
                        <span class="label">Agent Name:</span>
                        <span class="val">{{ $agent?->name ?? 'N/A' }} ({{ $agent?->agent_code ?? 'AGT' }})</span>
                    </div>
                    <div class="card-row">
                        <span class="label">NIC Number:</span>
                        <span class="val">{{ $agent?->nic_number ?: '—' }}</span>
                    </div>
                    <div class="card-row">
                        <span class="label">Phone / Mobile:</span>
                        <span class="val">{{ $agent?->phone ?: '—' }}</span>
                    </div>
                    <div class="card-row">
                        <span class="label">Bank Account:</span>
                        <span class="val">{{ $agent?->bank_account_no ?: '—' }}</span>
                    </div>
                    <div class="card-row">
                        <span class="label">Bank & Branch:</span>
                        <span class="val">{{ $agent?->bank_name ?: '—' }} ({{ $agent?->bank_branch ?: 'Main' }})</span>
                    </div>
                </div>
            </td>
            <td>
                <div class="card-box">
                    <div class="card-title">Referral Project Reference</div>
                    <div class="card-row">
                        <span class="label">Invoice No:</span>
                        <span class="val">{{ $invoice?->invoice_number ?? 'General / Batch Settlement' }}</span>
                    </div>
                    <div class="card-row">
                        <span class="label">Customer Name:</span>
                        <span class="val">{{ $invoice?->contact?->name ?? 'Direct Referral' }}</span>
                    </div>
                    <div class="card-row">
                        <span class="label">Project Total:</span>
                        <span class="val">{{ $invoice ? 'Rs. ' . number_format($invoice->grand_total, 2) : '—' }}</span>
                    </div>
                    <div class="card-row">
                        <span class="label">Payment Method:</span>
                        <span class="val" style="text-transform: uppercase;">{{ str_replace('_', ' ', $payout->payment_method) }}</span>
                    </div>
                    <div class="card-row">
                        <span class="label">Reference / Ref #:</span>
                        <span class="val">{{ $payout->reference_no ?: 'N/A' }}</span>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    {{-- Commission Breakdown Table --}}
    <table class="settlement-table">
        <thead>
            <tr>
                <th style="width: 50%;">Description / Settlement Item</th>
                <th style="width: 25%; text-align: center;">Reference</th>
                <th style="width: 25%; text-align: right;">Amount (LKR)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>Solar Project Commission Payout</strong>
                    @if ($invoice)
                        <div style="font-size: {{ $isA5 ? '7.5px' : '9.5px' }}; color: #64748b;">
                            Settlement for Invoice #{{ $invoice->invoice_number }} (Customer: {{ $invoice->contact?->name }})
                        </div>
                    @endif
                    @if ($payout->notes)
                        <div style="font-size: {{ $isA5 ? '7.5px' : '9.5px' }}; color: #475569; margin-top: 2px;">
                            Notes: {{ $payout->notes }}
                        </div>
                    @endif
                </td>
                <td style="text-align: center; font-family: monospace;">
                    {{ $payout->reference_no ?: $payout->voucher_number }}
                </td>
                <td class="amount-highlight">
                    Rs. {{ number_format($payout->amount, 2) }}
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: right; font-weight: 800; text-transform: uppercase;">
                    Total Net Commission Disbursed:
                </td>
                <td class="amount-highlight" style="font-size: {{ $isA5 ? '13px' : '16px' }}; color: #047857;">
                    Rs. {{ number_format($payout->amount, 2) }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Signatures --}}
    <table class="sig-grid">
        <tr>
            <td>
                <div style="height: 35px;"></div>
                <div class="sig-line"></div>
                <div class="sig-title">Prepared By</div>
                <div class="sig-sub">Finance & Accounts Department</div>
            </td>
            <td>
                @if ($signaturePath)
                    <div style="height: 35px; text-align: center;">
                        <img src="{{ $signaturePath }}" style="max-height: 35px; max-width: 120px;" alt="Signature">
                    </div>
                @else
                    <div style="height: 35px;"></div>
                @endif
                <div class="sig-line"></div>
                <div class="sig-title">Approved By</div>
                <div class="sig-sub">Managing Director / Authorized Officer</div>
            </td>
            <td>
                <div style="height: 35px;"></div>
                <div class="sig-line"></div>
                <div class="sig-title">Agent Receiver Signature</div>
                <div class="sig-sub">Acknowledgement of Payout Receipt</div>
            </td>
        </tr>
    </table>

    {{-- Footer --}}
    <div class="footer-note">
        Official Commission Settlement Document issued by {{ $company->name ?? 'Vertex Home Solutions (Pvt) Ltd' }}. Generated electronically.
    </div>

</body>
</html>

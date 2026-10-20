<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Commission Voucher - {{ $payout->voucher_number }}</title>
    @php
        $company = $company ?? $payout->company;
        $agent = $agent ?? $payout->salesAgent;
        $invoice = $invoice ?? $payout->invoice;

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
            size: a5 landscape;
            margin: 6mm 8mm;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 10px;
            line-height: 1.25;
            background-color: #ffffff;
        }

        .voucher-sheet {
            width: 100%;
            height: 100%;
            page-break-inside: avoid;
            page-break-after: avoid;
            overflow: hidden;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            border-bottom: 2px solid #0f766e;
            padding-bottom: 5px;
        }

        .header-table td {
            vertical-align: middle;
            padding: 0;
        }

        .company-name {
            font-size: 12px;
            font-weight: 800;
            color: #0f766e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .voucher-title {
            font-size: 14px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin: 1px 0;
        }

        .voucher-subtitle {
            font-size: 8px;
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
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 800;
            font-size: 9px;
            letter-spacing: 0.5px;
        }

        /* 2-Column Info Grid */
        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .info-grid td {
            width: 50%;
            vertical-align: top;
            padding: 0 4px;
        }

        .info-grid td:first-child {
            padding-left: 0;
        }

        .info-grid td:last-child {
            padding-right: 0;
        }

        .card-box {
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 5px 8px;
            background-color: #f8fafc;
        }

        .card-title {
            font-size: 9px;
            font-weight: 800;
            color: #0f766e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 2px;
            margin-bottom: 4px;
        }

        .card-row {
            margin-bottom: 2px;
            font-size: 8.5px;
            line-height: 1.2;
        }

        .card-row .label {
            font-weight: 600;
            color: #64748b;
            display: inline-block;
            width: 36%;
        }

        .card-row .val {
            font-weight: 700;
            color: #1e293b;
            display: inline-block;
            width: 62%;
        }

        /* Settlement Breakdown Table */
        .settlement-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            margin-bottom: 8px;
        }

        .settlement-table th {
            background-color: #0f766e;
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 8.5px;
            padding: 4px 6px;
            border: 1px solid #0f766e;
        }

        .settlement-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
            vertical-align: middle;
            font-size: 9px;
        }

        .amount-highlight {
            background-color: #ecfdf5;
            color: #047857;
            font-weight: 800;
            font-size: 11px;
            text-align: right;
        }

        /* Signatures Grid */
        .sig-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .sig-grid td {
            width: 33.33%;
            text-align: center;
            vertical-align: bottom;
            padding: 0 8px;
        }

        .sig-line {
            border-top: 1.2px solid #0f766e;
            margin-bottom: 3px;
            width: 85%;
            margin-left: auto;
            margin-right: auto;
        }

        .sig-title {
            font-weight: 800;
            color: #0f172a;
            font-size: 8.5px;
            text-transform: uppercase;
        }

        .sig-sub {
            font-size: 7.5px;
            color: #64748b;
            margin-top: 1px;
        }

        .footer-note {
            margin-top: 8px;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
            font-size: 7.5px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="voucher-sheet">
    {{-- Top Header Table --}}
    <table class="header-table">
        <tr>
            @if ($logoPath)
                <td style="width: 18%;">
                    <img src="{{ $logoPath }}" style="max-height: 38px; max-width: 110px; object-fit: contain;" alt="Company Logo">
                </td>
                <td style="width: 54%;">
            @else
                <td style="width: 72%;">
            @endif
                <div class="company-name">{{ $company->name ?? 'VERTEX HOME SOLUTIONS (PVT) LTD' }}</div>
                <div class="voucher-title">COMMISSION PAYMENT VOUCHER</div>
                <div class="voucher-subtitle">Sales Agent & Referrer Settlement Documentation</div>
            </td>
            <td style="width: 28%; text-align: right;">
                <div class="meta-pill">VOUCHER: {{ $payout->voucher_number }}</div>
                <div style="font-weight: 700; color: #334155; margin-top: 2px; font-size: 8.5px;">
                    Date: {{ $payout->payout_date?->format('d M Y') ?? now()->format('d M Y') }}
                </div>
                <div style="font-size: 8px; color: #64748b; margin-top: 1px;">
                    Status: <strong style="color: #047857; text-transform: uppercase;">{{ $payout->status }}</strong>
                </div>
            </td>
        </tr>
    </table>

    {{-- Agent & Project 2-Column Info Grid --}}
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
                <th style="width: 55%;">Settlement Description</th>
                <th style="width: 20%; text-align: center;">Reference / Invoice</th>
                <th style="width: 25%; text-align: right;">Amount (LKR)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>Solar PV Referral Commission Disbursement</strong>
                    @if ($invoice)
                        <div style="font-size: 8px; color: #64748b; margin-top: 1px;">
                            Settlement for Invoice #{{ $invoice->invoice_number }} (Customer: {{ $invoice->contact?->name }})
                        </div>
                    @endif
                    @if ($payout->notes)
                        <div style="font-size: 8px; color: #475569; margin-top: 1px;">
                            Notes: {{ $payout->notes }}
                        </div>
                    @endif
                </td>
                <td style="text-align: center; font-family: monospace; font-size: 8.5px;">
                    {{ $payout->reference_no ?: $payout->voucher_number }}
                </td>
                <td class="amount-highlight">
                    Rs. {{ number_format($payout->amount, 2) }}
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: right; font-weight: 800; text-transform: uppercase; font-size: 9px; background-color: #f8fafc;">
                    Total Net Commission Disbursed:
                </td>
                <td class="amount-highlight" style="font-size: 12px; color: #047857;">
                    Rs. {{ number_format($payout->amount, 2) }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Signatures Block (Prepared By, Approved By, Agent Receiver) --}}
    <table class="sig-grid">
        <tr>
            <td>
                <div style="height: 20px;"></div>
                <div class="sig-line"></div>
                <div class="sig-title">Prepared By</div>
                <div class="sig-sub">Finance & Accounts</div>
            </td>
            <td>
                @if ($signaturePath)
                    <div style="height: 20px; text-align: center;">
                        <img src="{{ $signaturePath }}" style="max-height: 20px; max-width: 90px; object-fit: contain;" alt="Signature">
                    </div>
                @else
                    <div style="height: 20px;"></div>
                @endif
                <div class="sig-line"></div>
                <div class="sig-title">Approved By</div>
                <div class="sig-sub">Managing Director</div>
            </td>
            <td>
                <div style="height: 20px;"></div>
                <div class="sig-line"></div>
                <div class="sig-title">Agent Receiver Signature</div>
                <div class="sig-sub">Payout Acknowledgement</div>
            </td>
        </tr>
    </table>

    {{-- Compact Footer Note --}}
    <div class="footer-note">
        Official Commission Settlement Document issued by {{ $company->name ?? 'Vertex Home Solutions (Pvt) Ltd' }}. Generated electronically.
    </div>
</div>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Receipt Voucher - {{ $payment->reference_no ?? ('REC-' . str_pad($payment->id, 5, '0', STR_PAD_LEFT)) }}</title>
    @php
        $primaryColor = $company->settings['primary_color'] ?? '#047857';
        $tableHeaderColor = $company->settings['table_header_color'] ?? '#047857';
        $textColor = $company->settings['text_color'] ?? '#ffffff';
        $invoice = $payment->invoice;
        $contact = $invoice?->contact ?? null;

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

        // Simple helper for number in words
        $numberToWords = function (float $number): string {
            $f = new \NumberFormatter('en_US', \NumberFormatter::SPELLOUT);
            $parts = explode('.', number_format($number, 2, '.', ''));
            $words = ucfirst($f->format((int) $parts[0])) . ' Sri Lankan Rupees';
            if (isset($parts[1]) && (int)$parts[1] > 0) {
                $words .= ' and ' . $f->format((int) $parts[1]) . ' Cents';
            }
            return $words . ' Only';
        };

        $words = $numberToWords((float) $payment->amount);
        $voucherNo = $payment->reference_no ? $payment->reference_no : ('REC-' . date('Y', strtotime($payment->payment_date ?? now())) . '-' . str_pad($payment->id, 5, '0', STR_PAD_LEFT));
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
            font-size: 11px;
            color: #334155;
            line-height: 1.6;
        }
        .voucher-highlight-card {
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
            border: 2px solid {{ $primaryColor }};
            border-radius: 8px;
            padding: 18px 22px;
            margin-bottom: 20px;
        }
        .amount-row {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
        }
        .amount-label {
            font-size: 12px;
            font-weight: bold;
            color: #166534;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .amount-value {
            font-size: 26px;
            font-weight: 800;
            color: {{ $primaryColor }};
        }
        .amount-words {
            margin-top: 8px;
            font-size: 12px;
            font-style: italic;
            color: #1e293b;
            border-top: 1px dashed #86efac;
            padding-top: 8px;
        }
        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 14px;
            vertical-align: top;
            font-size: 10.5px;
            line-height: 1.5;
        }
        .card-heading {
            font-size: 11px;
            font-weight: bold;
            color: {{ $primaryColor }};
            text-transform: uppercase;
            margin-bottom: 8px;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 4px;
        }
        .settlement-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 22px;
        }
        .settlement-table th {
            background-color: {{ $tableHeaderColor }};
            color: {{ $textColor }};
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px 12px;
            text-align: left;
        }
        .settlement-table td {
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            font-size: 11px;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid { background-color: #dcfce7; color: #166534; }
        .status-partial { background-color: #fef3c7; color: #92400e; }
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        .signatures-table td {
            width: 50%;
            vertical-align: top;
            font-size: 10.5px;
            color: #475569;
            padding: 0 15px;
        }
        .sig-line {
            width: 85%;
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
                <div class="company-subtitle">Official Payment Receipt & Wire Acknowledgment</div>
                <div style="color: #475569; font-size: 10px; line-height: 1.5;">
                    @if(!empty($company->address)) {{ $company->address }}<br> @endif
                    @if(!empty($company->phone)) Tel: {{ $company->phone }} | @endif
                    @if(!empty($company->email)) Email: {{ $company->email }}<br> @endif
                    @if(!empty($company->tax_number)) VAT/SVAT No: <strong>{{ $company->tax_number }}</strong> @endif
                </div>
            </td>
            <td style="width: 42%; text-align: right;">
                <div class="doc-badge">OFFICIAL RECEIPT VOUCHER</div>
                <div class="doc-meta">
                    <strong>Voucher No:</strong> {{ $voucherNo }}<br>
                    <strong>Receipt Date:</strong> {{ $payment->payment_date ? date('d M Y', strtotime($payment->payment_date)) : now()->format('d M Y') }}<br>
                    <strong>Payment Mode:</strong> {{ strtoupper(str_replace('_', ' ', $payment->method ?? 'Bank Transfer')) }}<br>
                    @if(!empty($payment->reference_no))
                        <strong>Bank / Cheque Ref:</strong> {{ $payment->reference_no }}<br>
                    @endif
                    <strong>Status:</strong> <span class="status-badge status-paid">PAYMENT RECEIVED</span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Amount Received Highlight Box -->
    <div class="voucher-highlight-card">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="vertical-align: middle;">
                    <div class="amount-label">Amount Received & Credited</div>
                    <div class="amount-words">
                        <strong>In Words:</strong> {{ $words }}
                    </div>
                </td>
                <td style="text-align: right; vertical-align: middle;">
                    <div class="amount-value">
                        Rs. {{ number_format((float) $payment->amount, 2) }}
                    </div>
                    <div style="font-size: 10px; color: #166534; font-weight: bold; margin-top: 4px;">
                        LKR — SRI LANKAN RUPEES
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Payer & Invoice Details -->
    <table class="info-grid">
        <tr>
            <td class="info-card" style="width: 50%;">
                <div class="card-heading">Received With Thanks From (Payer)</div>
                <strong style="font-size: 12px; color: #0f172a;">{{ $contact->name ?? 'Valued Customer' }}</strong><br>
                @if(!empty($contact->company_name))
                    <span>{{ $contact->company_name }}</span><br>
                @endif
                @if(!empty($contact->address))
                    <span>{{ $contact->address }}</span><br>
                @endif
                @if(!empty($contact->phone))
                    <span>Phone: {{ $contact->phone }}</span><br>
                @endif
                @if(!empty($contact->email))
                    <span>Email: {{ $contact->email }}</span>
                @endif
            </td>
            <td style="width: 4%;"></td>
            <td class="info-card" style="width: 46%;">
                <div class="card-heading">Payment Allocation & Invoice Reference</div>
                <strong>Applied To Invoice:</strong> {{ $invoice?->invoice_number ?? 'General Advance Payment' }}<br>
                <strong>Invoice Date:</strong> {{ $invoice?->invoice_date ? $invoice->invoice_date->format('d M Y') : 'N/A' }}<br>
                <strong>Payment Description:</strong> {{ $payment->milestone_label ?? 'Project Milestone / Services Payment' }}<br>
                @if(!empty($payment->notes))
                    <strong>Remittance Memo:</strong> {{ $payment->notes }}<br>
                @endif
                <strong>Payment Channel:</strong> {{ ucwords(str_replace('_', ' ', $payment->method ?? 'Cash/Wire')) }}
            </td>
        </tr>
    </table>

    <!-- Invoice Settlement Status Table -->
    @if($invoice)
        <table class="settlement-table">
            <thead>
                <tr>
                    <th>INVOICE TOTAL</th>
                    <th>PRIOR PAYMENTS</th>
                    <th>THIS PAYMENT</th>
                    <th>TOTAL SETTLED</th>
                    <th>REMAINING BALANCE DUE</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight: bold;">Rs. {{ number_format((float) $invoice->grand_total, 2) }}</td>
                    <td>Rs. {{ number_format(max(0, (float) $invoice->paid_amount - (float) $payment->amount), 2) }}</td>
                    <td style="color: #047857; font-weight: bold;">Rs. {{ number_format((float) $payment->amount, 2) }}</td>
                    <td style="font-weight: bold; color: #166534;">Rs. {{ number_format((float) $invoice->paid_amount, 2) }}</td>
                    <td style="font-weight: 800; color: {{ (float)$invoice->due_amount > 0 ? '#dc2626' : '#166534' }};">
                        Rs. {{ number_format((float) $invoice->due_amount, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    @endif

    <div style="background-color: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 6px; padding: 10px 14px; font-size: 10px; color: #475569; line-height: 1.5;">
        <strong>Official Receipt Notice:</strong> This document serves as an official receipt voucher for funds remitted to {{ $company->name ?? 'our company' }}. Cheque payments are subject to actual realization in the bank. Please retain this voucher for your financial and warranty records.
    </div>

    <!-- Signatures Section -->
    <table class="signatures-table">
        <tr>
            <td>
                <div class="sig-line"></div>
                <strong>Payer / Remitter Signature</strong><br>
                <span>Name: {{ $contact->name ?? 'Customer' }}</span><br>
                <span>Date: {{ now()->format('d M Y') }}</span>
            </td>
            <td style="text-align: right;">
                @if($signaturePath)
                    <div style="height: 40px; margin-bottom: 5px; display: inline-block;">
                        <img src="{{ $signaturePath }}" style="max-height: 40px; max-width: 140px; object-fit: contain;" alt="Signature">
                    </div>
                @else
                    <div class="sig-line" style="margin-left: auto;"></div>
                @endif
                <div style="clear: both;"></div>
                <strong>Authorized Signatory / Accounts</strong><br>
                <span>For {{ $company->name ?? 'Vertex Books Solutions' }}</span><br>
                <span>Verified & Endorsed</span>
            </td>
        </tr>
    </table>

</body>
</html>

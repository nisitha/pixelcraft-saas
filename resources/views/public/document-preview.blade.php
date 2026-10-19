<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ucfirst($type) }} #{{ $type === 'quotation' ? $document->quote_number : $document->invoice_number }} - {{ $company->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: {{ $company->settings['primary_color'] ?? '#047857' }};
            --table-header: {{ $company->settings['table_header_color'] ?? '#0f172a' }};
            --text-color: {{ $company->settings['text_color'] ?? '#ffffff' }};
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            padding-bottom: 60px;
        }
        .portal-header {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .portal-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .portal-logo {
            max-height: 38px;
            max-width: 140px;
            object-fit: contain;
        }
        .portal-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
        }
        .portal-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease;
            border: none;
        }
        .btn-primary {
            background-color: var(--primary);
            color: #ffffff;
        }
        .btn-primary:hover {
            filter: brightness(0.92);
        }
        .btn-secondary {
            background-color: #f8fafc;
            color: #475569;
            border: 1px solid #cbd5e1;
        }
        .btn-secondary:hover {
            background-color: #e2e8f0;
            color: #0f172a;
        }

        /* Centered A4 Paper Sheet */
        .a4-wrapper {
            max-width: 210mm;
            margin: 32px auto;
            background: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.06);
            border-radius: 4px;
            padding: 44px 48px;
            position: relative;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        /* Diagonal Corner Ribbon */
        .corner-ribbon {
            position: absolute;
            top: 24px;
            right: -42px;
            width: 160px;
            text-align: center;
            transform: rotate(45deg);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 6px 0;
            color: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        }
        .ribbon-draft { background-color: #64748b; }
        .ribbon-sent { background-color: #2563eb; }
        .ribbon-paid, .ribbon-accepted { background-color: #059669; }
        .ribbon-unpaid { background-color: #e11d48; }

        .header-grid {
            display: grid;
            grid-template-columns: 3fr 2fr;
            gap: 24px;
            margin-bottom: 28px;
        }
        .doc-badge {
            display: inline-block;
            background-color: var(--primary);
            color: #ffffff;
            font-size: 13px;
            font-weight: 800;
            padding: 6px 14px;
            border-radius: 4px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .info-cards-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 24px;
        }
        .info-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 14px 16px;
            font-size: 12px;
            line-height: 1.5;
        }
        .info-card-title {
            font-size: 11px;
            font-weight: 700;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: var(--table-header);
            color: var(--text-color);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 10px 12px;
            text-align: left;
        }
        .items-table th.right, .items-table td.right {
            text-align: right;
        }
        .items-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #edf2f7;
            vertical-align: top;
        }
        .items-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .summary-split {
            display: grid;
            grid-template-columns: 3fr 2fr;
            gap: 24px;
            margin-top: 10px;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .totals-table td {
            padding: 6px 10px;
        }
        .grand-total-row {
            background-color: var(--primary);
            color: #ffffff;
            font-size: 15px;
            font-weight: 800;
        }
        .grand-total-row td {
            padding: 10px 12px;
            border-radius: 4px;
        }

        .bank-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 14px;
            font-size: 11px;
            line-height: 1.6;
            margin-top: 14px;
        }

        @media print {
            .portal-header { display: none !important; }
            body { background: #ffffff; padding: 0; }
            .a4-wrapper { box-shadow: none; border: none; margin: 0; padding: 20px; max-width: 100%; }
        }
    </style>
</head>
<body>

    @php
        $status = strtolower($document->status ?? 'draft');
        $ribbonClass = match ($status) {
            'sent' => 'ribbon-sent',
            'paid', 'accepted', 'invoiced' => 'ribbon-paid',
            'unpaid' => 'ribbon-unpaid',
            default => 'ribbon-draft',
        };
        $ribbonText = strtoupper($status === 'invoiced' ? 'ACCEPTED' : $status);
        $docNum = $type === 'quotation' ? $document->quote_number : $document->invoice_number;
        $docDate = $type === 'quotation' ? ($document->quote_date?->format('d M Y') ?? now()->format('d M Y')) : ($document->invoice_date?->format('d M Y') ?? now()->format('d M Y'));
        $dueDate = $type === 'quotation' ? ($document->valid_until?->format('d M Y') ?? '30 Days') : ($document->due_date?->format('d M Y') ?? '14 Days');
    @endphp

    <header class="portal-header">
        <div class="portal-brand">
            @if ($company->getFilamentAvatarUrl())
                <img src="{{ $company->getFilamentAvatarUrl() }}" class="portal-logo" alt="{{ $company->name }}">
            @endif
            <div>
                <div class="portal-title">{{ $company->name }}</div>
                <div style="font-size: 11px; color: #64748b;">Customer Document Portal • Verified Official Document</div>
            </div>
        </div>

        <div class="portal-actions">
            <button type="button" onclick="window.print()" class="btn btn-secondary">
                🖨️ Print
            </button>
            <a href="{{ \Illuminate\Support\Facades\URL::signedRoute('public.document.download', ['type' => $type, 'id' => $document->id]) }}" class="btn btn-primary">
                ⬇️ Download Official PDF
            </a>
        </div>
    </header>

    <div class="a4-wrapper">
        <div class="corner-ribbon {{ $ribbonClass }}">{{ $ribbonText }}</div>

        <!-- Document Header -->
        <div class="header-grid">
            <div>
                @if ($company->getFilamentAvatarUrl())
                    <img src="{{ $company->getFilamentAvatarUrl() }}" style="max-height: 52px; max-width: 200px; margin-bottom: 8px;" alt="{{ $company->name }}">
                @endif
                <h1 style="font-size: 20px; font-weight: 800; color: var(--primary); margin-bottom: 4px;">{{ $company->name }}</h1>
                <div style="font-size: 12px; color: #475569; line-height: 1.5;">
                    @if ($company->address) {{ $company->address }}<br> @endif
                    @if ($company->phone) Tel: {{ $company->phone }} | @endif
                    @if ($company->email) Email: {{ $company->email }}<br> @endif
                    @if ($company->tax_number) Tax/VAT/TIN: <strong>{{ $company->tax_number }}</strong> @endif
                </div>
            </div>

            <div style="text-align: right;">
                <div class="doc-badge">{{ $type === 'quotation' ? 'SOLAR PV QUOTATION' : 'TAX INVOICE' }}</div>
                <div style="font-size: 12px; color: #334155; line-height: 1.6;">
                    <div><strong>{{ $type === 'quotation' ? 'Quote No:' : 'Invoice No:' }}</strong> {{ $docNum }}</div>
                    <div><strong>Issue Date:</strong> {{ $docDate }}</div>
                    <div><strong>{{ $type === 'quotation' ? 'Valid Until:' : 'Due Date:' }}</strong> {{ $dueDate }}</div>
                    <div><strong>Status:</strong> <span style="font-weight: 700; color: var(--primary); text-transform: uppercase;">{{ $status }}</span></div>
                </div>
            </div>
        </div>

        <!-- Client & Project Site Details -->
        <div class="info-cards-grid">
            <div class="info-card">
                <div class="info-card-title">Bill To / Client Details</div>
                <div style="font-weight: 700; font-size: 13px; color: #0f172a;">{{ $contact->name ?? 'Valued Customer' }}</div>
                @if ($contact->address) <div>{{ $contact->address }}</div> @endif
                @if ($contact->phone) <div>Phone: {{ $contact->phone }}</div> @endif
                @if ($contact->email) <div>Email: {{ $contact->email }}</div> @endif
            </div>

            <div class="info-card">
                <div class="info-card-title">Utility & Engineering Reference</div>
                <div>Utility Provider: Ceylon Electricity Board (CEB) / LECO</div>
                @if (!empty($contact->ceb_account_no))
                    <div style="margin-top: 4px; font-weight: 700; color: #92400e; background: #fef3c7; padding: 2px 6px; border-radius: 4px; display: inline-block;">
                        CEB Acc No: {{ $contact->ceb_account_no }}
                    </div>
                @endif
                @if (!empty($document->system_kw))
                    <div style="margin-top: 4px;">System Size: <strong>{{ $document->system_kw }} kWp</strong> ({{ ucfirst($document->phase_type ?? 'Single') }} Phase)</div>
                @endif
            </div>
        </div>

        <!-- Line Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 50%;">Item & Specifications</th>
                    <th style="width: 12%;" class="right">Qty</th>
                    <th style="width: 15%;" class="right">Rate (LKR)</th>
                    <th style="width: 18%;" class="right">Amount (LKR)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $idx => $item)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>
                            <div style="font-weight: 600; color: #0f172a;">{{ $item->product?->name ?? $item->description }}</div>
                            @if (!empty($item->description) && $item->description !== ($item->product?->name ?? ''))
                                <div style="font-size: 11px; color: #64748b;">{{ $item->description }}</div>
                            @endif
                        </td>
                        <td class="right">{{ number_format($item->quantity, 2) }}</td>
                        <td class="right">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="right" style="font-weight: 700; color: #0f172a;">{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Bottom Summary & Bank Details -->
        <div class="summary-split">
            <div>
                @php
                    $bankAccounts = $company->getBankAccountsList();
                @endphp
                @if (!empty($bankAccounts))
                    <div style="margin-top: 14px;">
                        <div style="font-size: 11px; font-weight: 800; color: var(--primary); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                            <span>🏦</span> Electronic Bank Wire Remittance
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
                            @foreach ($bankAccounts as $acc)
                                <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-top: 3px solid var(--primary); border-radius: 6px; padding: 10px 12px; font-size: 11px; line-height: 1.5;">
                                    <div style="font-weight: 800; color: #0f172a; font-size: 12px; margin-bottom: 4px;">{{ $acc['bank'] ?? 'Bank Transfer' }}</div>
                                    @if (!empty($acc['account_name']))
                                        <div style="color: #475569; font-size: 10px;">Account Name:</div>
                                        <div style="font-weight: 600; color: #1e293b; margin-bottom: 3px;">{{ $acc['account_name'] }}</div>
                                    @endif
                                    @if (!empty($acc['account_no']))
                                        <div style="color: #475569; font-size: 10px;">Account Number:</div>
                                        <div style="font-family: monospace; font-size: 13px; font-weight: 800; color: #0f172a; letter-spacing: 0.05em; margin-bottom: 3px;">{{ $acc['account_no'] }}</div>
                                    @endif
                                    @if (!empty($acc['branch']))
                                        <div style="color: #64748b; font-size: 10px;">Branch: <strong>{{ $acc['branch'] }}</strong></div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @elseif (!empty($company->settings['bank_details']))
                    <div class="bank-box">
                        <div style="font-weight: 700; color: #0f172a; margin-bottom: 4px;">🏦 Electronic Bank Wire Transfer Details:</div>
                        {!! nl2br(e($company->settings['bank_details'])) !!}
                    </div>
                @endif

                @if (!empty($document->terms))
                    <div style="margin-top: 12px; font-size: 11px; color: #475569; line-height: 1.5;">
                        <div style="font-weight: 700; color: #0f172a;">Terms & Conditions:</div>
                        {!! nl2br(e($document->terms)) !!}
                    </div>
                @endif
            </div>

            <div>
                <table class="totals-table">
                    <tr>
                        <td>Subtotal:</td>
                        <td style="text-align: right; font-weight: 600;">Rs. {{ number_format($document->subtotal, 2) }}</td>
                    </tr>
                    @if ((float) $document->discount > 0)
                        <tr>
                            <td>Promotional Discount:</td>
                            <td style="text-align: right; color: #dc2626;">- Rs. {{ number_format($document->discount, 2) }}</td>
                        </tr>
                    @endif
                    @if ((float) $document->tax > 0)
                        <tr>
                            <td>Applicable VAT / Tax:</td>
                            <td style="text-align: right;">Rs. {{ number_format($document->tax, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="grand-total-row">
                        <td>Total (LKR):</td>
                        <td style="text-align: right;">Rs. {{ number_format($document->grand_total, 2) }}</td>
                    </tr>
                    @if ($type === 'invoice')
                        <tr>
                            <td>Amount Paid:</td>
                            <td style="text-align: right; font-weight: 700; color: #059669;">Rs. {{ number_format($document->paid_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight: 700; color: #dc2626;">Balance Due:</td>
                            <td style="text-align: right; font-weight: 800; color: #dc2626;">Rs. {{ number_format($document->due_amount, 2) }}</td>
                        </tr>
                    @endif
                </table>

                <!-- Authorized Signature Stamp -->
                @if (!empty($company->settings['signature_path']))
                    <div style="margin-top: 24px; text-align: right;">
                        <img src="{{ asset('storage/' . $company->settings['signature_path']) }}" style="max-height: 52px; max-width: 160px; display: inline-block;" alt="Signature">
                        <div style="font-size: 11px; font-weight: 600; color: #475569; border-top: 1px solid #cbd5e1; padding-top: 4px; display: inline-block; min-width: 160px;">
                            Authorized Signatory
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

</body>
</html>

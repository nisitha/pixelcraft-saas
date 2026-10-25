<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payslip - {{ $employee->name }} - {{ $payroll->month }}</title>
    <style>
        @page {
            size: a4 portrait;
            margin: 15mm 15mm 15mm 15mm;
        }

        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Arial, sans-serif;
            color: #1d2630;
            background: #ffffff;
            margin: 0;
            padding: 0;
            font-size: 11px;
            line-height: 1.4;
        }

        .container {
            width: 100%;
        }

        /* Top Header */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #4680ff;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .company-meta {
            font-size: 9.5px;
            color: #5b6b79;
            line-height: 1.35;
        }

        .payslip-badge-title {
            text-align: right;
        }

        .payslip-badge {
            display: inline-block;
            background-color: #eef4fe;
            border: 1px solid #b9d5ff;
            color: #1e40af;
            font-size: 13px;
            font-weight: bold;
            padding: 6px 14px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .payslip-month {
            font-size: 11px;
            color: #4680ff;
            font-weight: 600;
            margin-top: 4px;
        }

        /* Employee Information Box */
        .meta-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 12px;
            margin-bottom: 15px;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 3px 6px;
            vertical-align: top;
            font-size: 10.5px;
        }

        .meta-label {
            color: #64748b;
            font-weight: 600;
            width: 18%;
        }

        .meta-value {
            color: #0f172a;
            font-weight: 600;
            width: 32%;
        }

        /* Earnings & Deductions Tables */
        .breakdown-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .breakdown-table td {
            vertical-align: top;
            padding: 0;
        }

        .grid-box {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            overflow: hidden;
        }

        .grid-header {
            background-color: #f1f5f9;
            padding: 7px 10px;
            font-size: 11.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #cbd5e1;
        }

        .earnings-header {
            color: #0369a1;
            background-color: #f0f9ff;
            border-bottom-color: #bae6fd;
        }

        .deductions-header {
            color: #b91c1c;
            background-color: #fef2f2;
            border-bottom-color: #fecaca;
        }

        .item-row-table {
            width: 100%;
            border-collapse: collapse;
        }

        .item-row-table td {
            padding: 6px 10px;
            font-size: 10.5px;
            border-bottom: 1px solid #f1f5f9;
        }

        .item-row-table tr:last-child td {
            border-bottom: none;
        }

        .item-label {
            color: #334155;
            text-align: left;
        }

        .item-amount {
            color: #0f172a;
            text-align: right;
            font-weight: 600;
            font-variant-numeric: tabular-nums;
        }

        .subtotal-row {
            background-color: #f8fafc;
            font-weight: bold;
            border-top: 2px solid #cbd5e1;
        }

        .subtotal-row td {
            padding: 7px 10px;
            font-size: 11px;
        }

        /* Net Pay Banner */
        .net-pay-banner {
            background-color: #ecfdf5;
            border: 2px solid #10b981;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 15px;
            width: 100%;
            box-sizing: border-box;
        }

        .net-pay-table {
            width: 100%;
        }

        .net-pay-title {
            font-size: 13px;
            font-weight: bold;
            color: #047857;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .net-pay-sub {
            font-size: 9.5px;
            color: #065f46;
        }

        .net-pay-amount {
            text-align: right;
            font-size: 20px;
            font-weight: bold;
            color: #065f46;
            font-variant-numeric: tabular-nums;
        }

        /* Employer Statutory Contributions Box */
        .statutory-box {
            background-color: #faf5ff;
            border: 1px solid #e9d5ff;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 18px;
        }

        .statutory-header {
            font-size: 10px;
            font-weight: bold;
            color: #6b21a8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .statutory-table {
            width: 100%;
            border-collapse: collapse;
        }

        .statutory-table td {
            padding: 3px 6px;
            font-size: 9.5px;
            color: #581c87;
        }

        .statutory-value {
            text-align: right;
            font-weight: bold;
        }

        /* Signatures */
        .signatures-table {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
        }

        .signatures-table td {
            width: 33.33%;
            vertical-align: top;
            text-align: center;
            padding: 0 10px;
        }

        .sign-line {
            border-top: 1px dashed #64748b;
            margin-bottom: 5px;
            height: 1px;
        }

        .sign-title {
            font-size: 10px;
            font-weight: bold;
            color: #1e293b;
        }

        .sign-subtitle {
            font-size: 8.5px;
            color: #64748b;
        }

        .footer-note {
            margin-top: 20px;
            font-size: 8px;
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Top Header -->
    <table class="header-table">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <div class="company-name">{{ $company->name ?: 'Vertex Home Solutions (Pvt) Ltd' }}</div>
                <div class="company-meta">
                    {{ $company->address ?: 'Mirigama, Sri Lanka' }}<br>
                    @if($company->phone) Phone: {{ $company->phone }} | @endif
                    @if($company->email) Email: {{ $company->email }} | @endif
                    @if($company->tax_number) TIN / Reg: {{ $company->tax_number }} @endif
                </div>
            </td>
            <td style="width: 40%; vertical-align: top;" class="payslip-badge-title">
                <div class="payslip-badge">PAYSLIP</div>
                <div class="payslip-month">
                    {{ strtoupper(\Carbon\Carbon::createFromFormat('Y-m', $payroll->month)->format('F Y')) }}
                </div>
                <div style="font-size: 9px; color: #64748b; margin-top: 3px;">
                    Ref: PAY-{{ $payroll->month }}-{{ str_pad((string)$item->id, 3, '0', STR_PAD_LEFT) }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Employee Meta Card -->
    <div class="meta-card">
        <table class="meta-table">
            <tr>
                <td class="meta-label">Employee Name:</td>
                <td class="meta-value">{{ $employee->name }} @if($employee->calling_name)({{ $employee->calling_name }})@endif</td>
                <td class="meta-label">Designation:</td>
                <td class="meta-value">{{ $employee->designation }}</td>
            </tr>
            <tr>
                <td class="meta-label">EPF / Member No:</td>
                <td class="meta-value">{{ $employee->epf_number ?: 'N/A' }}</td>
                <td class="meta-label">NIC Number:</td>
                <td class="meta-value">{{ $employee->nic_number ?: 'N/A' }}</td>
            </tr>
            <tr>
                <td class="meta-label">Bank Remittance:</td>
                <td class="meta-value">
                    {{ $employee->bank_name ?: 'Bank Remittance' }}
                    @if($employee->bank_branch) - {{ $employee->bank_branch }} @endif
                </td>
                <td class="meta-label">Account No:</td>
                <td class="meta-value">{{ $employee->bank_account_number ?: 'Direct Transfer' }}</td>
            </tr>
            @if($employee->joined_date)
            <tr>
                <td class="meta-label">Joined Date:</td>
                <td class="meta-value">{{ $employee->joined_date->format('d M Y') }}</td>
                <td class="meta-label">Payroll Status:</td>
                <td class="meta-value" style="color: {{ $payroll->isPaid() ? '#059669' : ($payroll->isConfirmed() ? '#2563eb' : '#d97706') }}; text-transform: uppercase;">
                    {{ $payroll->status }}
                </td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Earnings & Deductions Tables (Two Columns Side-by-Side) -->
    <table class="breakdown-table">
        <tr>
            <!-- Left: Earnings -->
            <td style="width: 48.5%; vertical-align: top;">
                <div class="grid-box">
                    <div class="grid-header earnings-header">Earnings</div>
                    <table class="item-row-table">
                        <tr>
                            <td class="item-label">Basic Salary</td>
                            <td class="item-amount">Rs. {{ number_format($item->basic_salary, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="item-label">Fixed Monthly Allowance</td>
                            <td class="item-amount">Rs. {{ number_format($item->fixed_allowance, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="item-label">Additional / Site Allowance (OT)</td>
                            <td class="item-amount">Rs. {{ number_format($item->additional_allowance, 2) }}</td>
                        </tr>
                        @if($employee->daily_rate && $employee->daily_rate > 0)
                        <tr>
                            <td class="item-label">Agreed Daily Wage Rate (Ref)</td>
                            <td class="item-amount" style="color: #64748b; font-weight: normal;">Rs. {{ number_format($employee->daily_rate, 2) }}/day</td>
                        </tr>
                        @endif
                        <tr class="subtotal-row">
                            <td class="item-label" style="color: #0369a1;">TOTAL GROSS EARNINGS</td>
                            <td class="item-amount" style="color: #0369a1;">Rs. {{ number_format($item->gross_salary, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </td>

            <td style="width: 3%;"></td>

            <!-- Right: Deductions -->
            <td style="width: 48.5%; vertical-align: top;">
                <div class="grid-box">
                    <div class="grid-header deductions-header">Deductions</div>
                    <table class="item-row-table">
                        <tr>
                            <td class="item-label">EPF Contribution (Employee 8%)</td>
                            <td class="item-amount">Rs. {{ number_format($item->epf_employee, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="item-label">Salary Advance Recoveries</td>
                            <td class="item-amount" style="{{ $item->advance_deduction > 0 ? 'color: #dc2626;' : '' }}">
                                Rs. {{ number_format($item->advance_deduction, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="item-label">Other Deductions / Penalties</td>
                            <td class="item-amount">Rs. {{ number_format($item->other_deductions, 2) }}</td>
                        </tr>
                        @if($employee->daily_rate && $employee->daily_rate > 0)
                        <tr>
                            <td class="item-label">&nbsp;</td>
                            <td class="item-amount">&nbsp;</td>
                        </tr>
                        @endif
                        <tr class="subtotal-row">
                            <td class="item-label" style="color: #b91c1c;">TOTAL DEDUCTIONS</td>
                            <td class="item-amount" style="color: #b91c1c;">Rs. {{ number_format($item->total_deductions, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <!-- Prominent Net Take-Home Pay Banner -->
    <div class="net-pay-banner">
        <table class="net-pay-table">
            <tr>
                <td style="vertical-align: middle;">
                    <div class="net-pay-title">NET TAKE-HOME REMITTANCE</div>
                    <div class="net-pay-sub">Gross Earnings minus Statutory EPF 8% and Advance Recoveries</div>
                </td>
                <td style="vertical-align: middle;">
                    <div class="net-pay-amount">Rs. {{ number_format($item->net_salary, 2) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Employer Statutory Contributions Box (Informational) -->
    <div class="statutory-box">
        <div class="statutory-header">Statutory Employer Contributions (Company Remittance - Not Deducted from Salary)</div>
        <table class="statutory-table">
            <tr>
                <td style="width: 25%;">Employer EPF (12%):</td>
                <td class="statutory-value" style="width: 25%;">Rs. {{ number_format($item->epf_employer, 2) }}</td>
                <td style="width: 25%;">Employer ETF (3%):</td>
                <td class="statutory-value" style="width: 25%;">Rs. {{ number_format($item->etf_employer, 2) }}</td>
            </tr>
            <tr>
                <td style="width: 25%; font-weight: bold; border-top: 1px dashed #d8b4fe; padding-top: 4px;">Total Statutory Remittance:</td>
                <td class="statutory-value" style="width: 25%; border-top: 1px dashed #d8b4fe; padding-top: 4px;">
                    Rs. {{ number_format($item->epf_employer + $item->etf_employer, 2) }}
                </td>
                <td style="width: 25%; font-weight: bold; border-top: 1px dashed #d8b4fe; padding-top: 4px;">Total Cost to Employer (CTC):</td>
                <td class="statutory-value" style="width: 25%; border-top: 1px dashed #d8b4fe; padding-top: 4px; color: #581c87;">
                    Rs. {{ number_format($item->gross_salary + $item->epf_employer + $item->etf_employer, 2) }}
                </td>
            </tr>
        </table>
    </div>

    @if($item->notes)
    <div style="font-size: 9.5px; color: #475569; margin-bottom: 15px; background: #f8fafc; padding: 6px 10px; border-left: 3px solid #3b82f6;">
        <strong>Adjustment Notes:</strong> {{ $item->notes }}
    </div>
    @endif

    <!-- Three-Column Signature Lines -->
    <table class="signatures-table">
        <tr>
            <td>
                <div style="height: 38px;"></div>
                <div class="sign-line"></div>
                <div class="sign-title">Prepared By</div>
                <div class="sign-subtitle">Payroll Officer / Accountant</div>
            </td>
            <td>
                <div style="height: 38px;"></div>
                <div class="sign-line"></div>
                <div class="sign-title">Approved By</div>
                <div class="sign-subtitle">Managing Director / Partner</div>
            </td>
            <td>
                <div style="height: 38px;"></div>
                <div class="sign-line"></div>
                <div class="sign-title">Employee Acknowledgment</div>
                <div class="sign-subtitle">Signature & Date Received</div>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        This is a confidential computer-generated payslip generated by Vertex Books ERP for {{ $company->name }}. Printed on {{ now()->format('d M Y, h:i A') }}.
    </div>
</div>
</body>
</html>

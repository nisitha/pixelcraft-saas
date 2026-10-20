<div class="zoho-dashboard-top-section">
    <style>
        .zoho-dashboard-top-section {
            display: flex;
            flex-direction: column;
            gap: 16px;
            width: 100%;
        }

        /* Quick Action Bar */
        .zoho-quick-actions-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 18px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        html.dark .zoho-quick-actions-bar {
            background: #111827;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        }

        .zoho-quick-action-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            margin-right: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        html.dark .zoho-quick-action-label {
            color: #94a3b8;
        }

        .zoho-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.15s ease-in-out;
            border: 1px solid transparent;
            cursor: pointer;
        }

        .zoho-action-btn-primary {
            background-color: #3b82f6;
            color: #ffffff !important;
            box-shadow: 0 1px 2px rgba(59, 130, 246, 0.3);
        }
        .zoho-action-btn-primary:hover {
            background-color: #2563eb;
            transform: translateY(-1px);
        }

        .zoho-action-btn-emerald {
            background-color: #059669;
            color: #ffffff !important;
            box-shadow: 0 1px 2px rgba(5, 150, 105, 0.3);
        }
        .zoho-action-btn-emerald:hover {
            background-color: #047857;
            transform: translateY(-1px);
        }

        .zoho-action-btn-amber {
            background-color: #f59e0b;
            color: #ffffff !important;
            box-shadow: 0 1px 2px rgba(245, 158, 11, 0.3);
        }
        .zoho-action-btn-amber:hover {
            background-color: #d97706;
            transform: translateY(-1px);
        }

        .zoho-action-btn-indigo {
            background-color: #6366f1;
            color: #ffffff !important;
            box-shadow: 0 1px 2px rgba(99, 102, 241, 0.3);
        }
        .zoho-action-btn-indigo:hover {
            background-color: #4f46e5;
            transform: translateY(-1px);
        }

        .zoho-action-btn-slate {
            background-color: #f1f5f9;
            color: #334155 !important;
            border-color: #cbd5e1;
        }
        .zoho-action-btn-slate:hover {
            background-color: #e2e8f0;
            color: #0f172a !important;
            transform: translateY(-1px);
        }
        html.dark .zoho-action-btn-slate {
            background-color: #1f2937;
            color: #f8fafc !important;
            border-color: #374151;
        }
        html.dark .zoho-action-btn-slate:hover {
            background-color: #374151;
        }

        /* Executive Overview Grid */
        .zoho-receivables-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 16px;
        }

        @media (max-width: 1024px) {
            .zoho-receivables-grid {
                grid-template-columns: 1fr;
            }
        }

        .zoho-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            position: relative;
        }

        html.dark .zoho-card {
            background: #111827;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        }

        .zoho-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .zoho-card-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #64748b;
        }

        html.dark .zoho-card-title {
            color: #94a3b8;
        }

        .zoho-main-metric {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
            line-height: 1.2;
            margin-bottom: 12px;
        }

        html.dark .zoho-main-metric {
            color: #f8fafc;
        }

        /* Zoho Multi-Segmented Progress Bar */
        .zoho-progress-track {
            width: 100%;
            height: 14px;
            background: #f1f5f9;
            border-radius: 9999px;
            overflow: hidden;
            display: flex;
            margin-bottom: 16px;
            border: 1px solid #e2e8f0;
        }

        html.dark .zoho-progress-track {
            background: #1f2937;
            border-color: #374151;
        }

        .zoho-progress-seg-current {
            background: #10b981;
            height: 100%;
            transition: width 0.4s ease;
        }
        .zoho-progress-seg-15 {
            background: #f59e0b;
            height: 100%;
            transition: width 0.4s ease;
        }
        .zoho-progress-seg-30 {
            background: #f97316;
            height: 100%;
            transition: width 0.4s ease;
        }
        .zoho-progress-seg-over30 {
            background: #ef4444;
            height: 100%;
            transition: width 0.4s ease;
        }

        /* Breakdown Badges Legend */
        .zoho-aging-legend {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 10px;
        }

        .zoho-aging-item {
            display: flex;
            flex-direction: column;
            gap: 2px;
            padding: 8px 12px;
            border-radius: 8px;
            background: #f8fafc;
            border-left: 3px solid transparent;
        }

        html.dark .zoho-aging-item {
            background: #1f2937;
        }

        .zoho-aging-current { border-left-color: #10b981; }
        .zoho-aging-15 { border-left-color: #f59e0b; }
        .zoho-aging-30 { border-left-color: #f97316; }
        .zoho-aging-over30 { border-left-color: #ef4444; }

        .zoho-aging-title {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
        }

        html.dark .zoho-aging-title {
            color: #94a3b8;
        }

        .zoho-aging-val {
            font-size: 12.5px;
            font-weight: 700;
            color: #0f172a;
        }

        html.dark .zoho-aging-val {
            color: #f8fafc;
        }

        /* Right Side Metric Cards */
        .zoho-side-metrics {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .zoho-mini-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        html.dark .zoho-mini-card {
            background: #111827;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        }
    </style>

    <!-- Top Quick Action Bar -->
    <div class="zoho-quick-actions-bar">
        <span class="zoho-quick-action-label">
            <svg style="width: 14px; height: 14px; color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Quick Actions:
        </span>

        @if($canAccessQuotations)
            <a href="{{ $newQuotationUrl }}" class="zoho-action-btn zoho-action-btn-emerald">
                <span>+ New Quotation</span>
            </a>
        @endif

        <a href="{{ $newInvoiceUrl }}" class="zoho-action-btn zoho-action-btn-primary">
            <span>+ New Invoice</span>
        </a>

        @if($canAccessPos)
            <a href="{{ $posTerminalUrl }}" class="zoho-action-btn zoho-action-btn-amber">
                <span>+ POS Terminal</span>
            </a>
        @endif

        @if($canAccessPayments)
            <a href="{{ $recordPaymentUrl }}" class="zoho-action-btn zoho-action-btn-indigo">
                <span>+ Record Payment</span>
            </a>
        @endif

        @if($canAccessExpenses)
            <a href="{{ $logExpenseUrl }}" class="zoho-action-btn zoho-action-btn-slate">
                <span>+ Log Expense</span>
            </a>
        @endif

        @if($canAccessCertificates)
            <a href="{{ $issueCertificateUrl }}" class="zoho-action-btn zoho-action-btn-slate">
                <span>+ Issue Certificate</span>
            </a>
        @endif
    </div>

    <!-- Executive Overview Row -->
    <div class="zoho-receivables-grid">
        <!-- Zoho Total Receivables Card (Left 2 cols) -->
        <div class="zoho-card">
            <div class="zoho-card-header">
                <div class="zoho-card-title">Total Receivables (Outstanding Invoices)</div>
                <span style="font-size: 11px; color: #64748b; font-weight: 600;">Current vs Overdue Aging</span>
            </div>

            <div class="zoho-main-metric">
                Rs. {{ number_format($totalReceivables, 2) }}
            </div>

            <!-- Segmented Progress Bar -->
            <div class="zoho-progress-track" title="Receivables Aging Breakdown">
                @if($totalReceivables > 0)
                    @if($currentPct > 0)
                        <div class="zoho-progress-seg-current" style="width: {{ $currentPct }}%;" title="Current (Not Due): {{ $currentPct }}%"></div>
                    @endif
                    @if($overdue1To15Pct > 0)
                        <div class="zoho-progress-seg-15" style="width: {{ $overdue1To15Pct }}%;" title="1-15 Days: {{ $overdue1To15Pct }}%"></div>
                    @endif
                    @if($overdue16To30Pct > 0)
                        <div class="zoho-progress-seg-30" style="width: {{ $overdue16To30Pct }}%;" title="16-30 Days: {{ $overdue16To30Pct }}%"></div>
                    @endif
                    @if($overdueOver30Pct > 0)
                        <div class="zoho-progress-seg-over30" style="width: {{ $overdueOver30Pct }}%;" title=">30 Days: {{ $overdueOver30Pct }}%"></div>
                    @endif
                @else
                    <div style="width: 100%; height: 100%; background: #10b981; opacity: 0.3;" title="No outstanding invoices"></div>
                @endif
            </div>

            <!-- Aging Buckets Legend -->
            <div class="zoho-aging-legend">
                <div class="zoho-aging-item zoho-aging-current">
                    <span class="zoho-aging-title">Current (Not Yet Due)</span>
                    <span class="zoho-aging-val" style="color: #059669;">Rs. {{ number_format($currentAmount, 2) }}</span>
                    <span style="font-size: 10px; color: #64748b;">{{ $currentPct }}% of total</span>
                </div>

                <div class="zoho-aging-item zoho-aging-15">
                    <span class="zoho-aging-title">Overdue 1-15 Days</span>
                    <span class="zoho-aging-val" style="color: #d97706;">Rs. {{ number_format($overdue1To15, 2) }}</span>
                    <span style="font-size: 10px; color: #64748b;">{{ $overdue1To15Pct }}% of total</span>
                </div>

                <div class="zoho-aging-item zoho-aging-30">
                    <span class="zoho-aging-title">Overdue 16-30 Days</span>
                    <span class="zoho-aging-val" style="color: #ea580c;">Rs. {{ number_format($overdue16To30, 2) }}</span>
                    <span style="font-size: 10px; color: #64748b;">{{ $overdue16To30Pct }}% of total</span>
                </div>

                <div class="zoho-aging-item zoho-aging-over30">
                    <span class="zoho-aging-title">Overdue > 30 Days</span>
                    <span class="zoho-aging-val" style="color: #dc2626;">Rs. {{ number_format($overdueOver30, 2) }}</span>
                    <span style="font-size: 10px; color: #64748b;">{{ $overdueOver30Pct }}% of total</span>
                </div>
            </div>
        </div>

        <!-- Monthly Performance Metrics (Right 1 col) -->
        <div class="zoho-side-metrics">
            <div class="zoho-mini-card" style="border-left: 4px solid #10b981;">
                <div>
                    <div class="zoho-card-title">Total Collected (This Month)</div>
                    <div style="font-size: 20px; font-weight: 800; color: #059669; margin-top: 4px;">
                        Rs. {{ number_format($totalCollectedThisMonth, 2) }}
                    </div>
                    <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                        Received customer payments
                    </div>
                </div>
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #ecfdf5; display: flex; align-items: center; justify-content: center; color: #059669;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            @if($isOwnerOrAdmin)
                <div class="zoho-mini-card" style="border-left: 4px solid {{ $netProfitThisMonth >= 0 ? '#3b82f6' : '#ef4444' }};">
                    <div>
                        <div class="zoho-card-title">Net Profit (This Month)</div>
                        <div style="font-size: 20px; font-weight: 800; color: {{ $netProfitThisMonth >= 0 ? '#2563eb' : '#dc2626' }}; margin-top: 4px;">
                            Rs. {{ number_format($netProfitThisMonth, 2) }}
                        </div>
                        <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                            Revenue minus operational expenses
                        </div>
                    </div>
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: {{ $netProfitThisMonth >= 0 ? '#eff6ff' : '#fef2f2' }}; display: flex; align-items: center; justify-content: center; color: {{ $netProfitThisMonth >= 0 ? '#2563eb' : '#dc2626' }};">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

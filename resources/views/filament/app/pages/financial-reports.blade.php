<x-filament-panels::page>
    <style>
        .vb-metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 1rem;
        }
        .vb-metric-card {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 0.75rem;
            padding: 1rem 1.25rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .dark .vb-metric-card {
            background-color: #111827;
            border-color: #1f2937;
        }
        .vb-pnl-layout {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        @media (min-width: 1024px) {
            .vb-pnl-layout {
                flex-direction: row;
                align-items: flex-start;
            }
            .vb-pnl-statement {
                flex: 7;
                min-width: 0;
            }
            .vb-pnl-expenses {
                flex: 5;
                min-width: 0;
            }
        }

        /* Dark Mode Financial Reports */
        .dark button[style*="background-color: #f9fafb"] {
            background-color: #1e293b !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
            color: #cbd5e1 !important;
        }
        .dark input[type="date"], .dark select {
            background-color: #1e293b !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
            color: #f8fafc !important;
        }
        .dark div[style*="background-color: #f9fafb"] {
            background-color: #111827 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            color: #f8fafc !important;
        }
        .dark div[style*="border-bottom: 1px solid #f3f4f6"],
        .dark tr[style*="border-bottom: 1px solid #f3f4f6"] {
            border-bottom-color: rgba(255, 255, 255, 0.06) !important;
        }
        .dark div[style*="color: #111827"], .dark span[style*="color: #111827"], .dark td[style*="color: #111827"] {
            color: #f8fafc !important;
        }
        .dark div[style*="color: #4b5563"], .dark span[style*="color: #4b5563"] {
            color: #94a3b8 !important;
        }
        .dark div[style*="background-color: #ecfdf5"] {
            background-color: rgba(5, 150, 105, 0.15) !important;
            color: #a7f3d0 !important;
            border-color: rgba(5, 150, 105, 0.3) !important;
        }
        .dark .bg-emerald-50 {
            background-color: rgba(16, 185, 129, 0.12) !important;
            border-color: rgba(16, 185, 129, 0.35) !important;
            color: #a7f3d0 !important;
        }
        .dark .bg-rose-50 {
            background-color: rgba(239, 68, 68, 0.12) !important;
            border-color: rgba(239, 68, 68, 0.35) !important;
            color: #fecdd3 !important;
        }
    </style>

    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        <!-- 1. Top Prominent Cash Collection KPI Badges (Requirement 2) -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.25rem;">
            <!-- Realized Cash Collected (Green #10b981) -->
            <div style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.09) 0%, rgba(16, 185, 129, 0.02) 100%); border: 1.5px solid #10b981; border-radius: 0.875rem; padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.1);">
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                        <span style="display: inline-block; width: 0.65rem; height: 0.65rem; border-radius: 9999px; background-color: #10b981;"></span>
                        <span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #10b981;">
                            Realized Cash Collected
                        </span>
                    </div>
                    <div style="font-size: 1.85rem; font-weight: 900; color: #10b981; line-height: 1.2;">
                        Rs. {{ number_format($this->getRealizedCashCollected(), 2) }}
                    </div>
                    <div style="font-size: 0.75rem; color: #059669; font-weight: 600; margin-top: 0.4rem; display: flex; align-items: center; gap: 0.35rem;">
                        <span>✓ Verified & approved payments received</span>
                    </div>
                </div>
                <div style="background-color: rgba(16, 185, 129, 0.15); border-radius: 0.75rem; padding: 0.85rem; display: flex; align-items: center; justify-content: center;">
                    <x-filament::icon icon="heroicon-o-banknotes" style="width: 2.25rem; height: 2.25rem; color: #10b981;" />
                </div>
            </div>

            <!-- Pending Cash to Collect (Amber #f59e0b) -->
            <div style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.09) 0%, rgba(245, 158, 11, 0.02) 100%); border: 1.5px solid #f59e0b; border-radius: 0.875rem; padding: 1.25rem 1.5rem; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.1);">
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                        <span style="display: inline-block; width: 0.65rem; height: 0.65rem; border-radius: 9999px; background-color: #f59e0b;"></span>
                        <span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #f59e0b;">
                            Pending Cash to Collect
                        </span>
                    </div>
                    <div style="font-size: 1.85rem; font-weight: 900; color: #f59e0b; line-height: 1.2;">
                        Rs. {{ number_format($this->getPendingCashToCollect(), 2) }}
                    </div>
                    <div style="font-size: 0.75rem; color: #d97706; font-weight: 600; margin-top: 0.4rem; display: flex; align-items: center; gap: 0.35rem;">
                        <span>⏳ Outstanding customer balance across active invoices</span>
                    </div>
                </div>
                <div style="background-color: rgba(245, 158, 11, 0.15); border-radius: 0.75rem; padding: 0.85rem; display: flex; align-items: center; justify-content: center;">
                    <x-filament::icon icon="heroicon-o-clock" style="width: 2.25rem; height: 2.25rem; color: #f59e0b;" />
                </div>
            </div>
        </div>

        <!-- 2. Invoice / Project Profitability Explorer (Requirement 3) -->
        @php
            $pnl = $pnl ?? $this->getInvoiceProfitabilityMetrics();
            $availableInvoices = $availableInvoices ?? $this->getInvoicesList();
        @endphp

        <x-filament::section>
            <x-slot name="heading">
                <div style="display: flex; align-items: center; justify-content: space-between; width: 100%; flex-wrap: wrap; gap: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <x-filament::icon 
                            icon="heroicon-o-presentation-chart-line" 
                            style="width: 1.35rem; height: 1.35rem; color: #4f46e5;" 
                        />
                        <div>
                            <div style="font-size: 0.95rem; font-weight: 800; color: #111827;">
                                Project / Invoice Profitability Explorer (P&L Drill-Down)
                            </div>
                            <div style="font-size: 0.75rem; color: #6b7280;">
                                Select any invoice/project to inspect linked direct supplier bills, labour costs, sales commissions, and visual profit share.
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Selector Dropdown -->
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <label for="invoiceSelect" style="font-size: 0.75rem; font-weight: 700; color: #6b7280; text-transform: uppercase;">
                            Select Project / Invoice:
                        </label>
                        <select 
                            id="invoiceSelect"
                            wire:model.live="selectedInvoiceId" 
                            style="padding: 0.45rem 1rem; border: 1.5px solid #6366f1; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 700; background-color: #ffffff; color: #111827; cursor: pointer; min-width: 280px; max-width: 420px;"
                        >
                            @forelse($availableInvoices as $invOption)
                                <option value="{{ $invOption->id }}">
                                    {{ $invOption->invoice_number }} — {{ $invOption->contact->name ?? 'Client' }} (Rs. {{ number_format($invOption->grand_total, 0) }})
                                </option>
                            @empty
                                <option value="">No Invoices Available</option>
                            @endforelse
                        </select>
                    </div>
                </div>
            </x-slot>

            @if($pnl['has_data'])
                <div style="display: flex; flex-direction: column; gap: 1.25rem; padding-top: 0.5rem;">

                    <!-- Project Metadata Header Bar -->
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; padding: 0.75rem 1rem; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 0.625rem; font-size: 0.8rem;">
                        <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                            <div>
                                <span style="color: #6b7280; font-size: 0.7rem; display: block;">INVOICE NUMBER</span>
                                <span style="font-family: monospace; font-weight: 800; font-size: 0.95rem; color: #111827;">{{ $pnl['invoice']->invoice_number }}</span>
                            </div>
                            <div style="border-left: 1px solid #e5e7eb; padding-left: 1rem;">
                                <span style="color: #6b7280; font-size: 0.7rem; display: block;">CUSTOMER / CLIENT</span>
                                <span style="font-weight: 700; color: #111827;">{{ $pnl['invoice']->contact->name ?? 'N/A' }}</span>
                                @if(!empty($pnl['invoice']->contact->ceb_account_no))
                                    <span style="font-size: 0.7rem; color: #b45309; font-weight: 600; margin-left: 0.35rem;">(CEB: {{ $pnl['invoice']->contact->ceb_account_no }})</span>
                                @endif
                            </div>
                            <div style="border-left: 1px solid #e5e7eb; padding-left: 1rem;">
                                <span style="color: #6b7280; font-size: 0.7rem; display: block;">INVOICE DATE</span>
                                <span style="color: #374151; font-weight: 600;">{{ $pnl['invoice']->invoice_date ? $pnl['invoice']->invoice_date->format('d M Y') : 'N/A' }}</span>
                            </div>
                            @if($pnl['invoice']->salesAgent)
                                <div style="border-left: 1px solid #e5e7eb; padding-left: 1rem;">
                                    <span style="color: #6b7280; font-size: 0.7rem; display: block;">SALES AGENT</span>
                                    <span style="color: #8b5cf6; font-weight: 700;">{{ $pnl['invoice']->salesAgent->name }}</span>
                                </div>
                            @endif
                        </div>

                        <div>
                            <span style="padding: 0.3rem 0.75rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; background-color: {{ $pnl['invoice']->status === 'paid' ? '#ecfdf5' : ($pnl['invoice']->status === 'partial' ? '#fffbeb' : '#fef2f2') }}; color: {{ $pnl['invoice']->status === 'paid' ? '#059669' : ($pnl['invoice']->status === 'partial' ? '#d97706' : '#e11d48') }}; border: 1px solid currentColor;">
                                {{ ucfirst($pnl['invoice']->status) }}
                            </span>
                        </div>
                    </div>

                    <!-- 5 Summary Metric Cards for this Project -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                        <!-- Contract Value / Revenue -->
                        <div class="vb-metric-card" style="border-top: 3px solid #6366f1;">
                            <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #6b7280;">
                                Contract Value / Revenue
                            </div>
                            <div style="font-size: 1.35rem; font-weight: 900; color: #111827; margin-top: 0.25rem;">
                                Rs. {{ number_format($pnl['contract_value'], 2) }}
                            </div>
                            <div style="font-size: 0.7rem; color: #6b7280; margin-top: 0.25rem;">
                                Subtotal: Rs. {{ number_format((float) $pnl['invoice']->subtotal, 2) }}
                                @if((float) ($pnl['invoice']->profit_markup ?? 0) > 0)
                                    <span style="color: #059669; font-weight: 700; display: block;">+ Rs. {{ number_format((float) $pnl['invoice']->profit_markup, 2) }} Markup</span>
                                @endif
                            </div>
                        </div>

                        <!-- Collected Amount vs Balance Due -->
                        <div class="vb-metric-card" style="border-top: 3px solid #10b981;">
                            <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #059669;">
                                Collected vs Balance Due
                            </div>
                            <div style="font-size: 1.35rem; font-weight: 900; color: #059669; margin-top: 0.25rem;">
                                Rs. {{ number_format($pnl['collected_amount'], 2) }}
                            </div>
                            <div style="font-size: 0.7rem; margin-top: 0.25rem; display: flex; justify-content: space-between;">
                                <span style="color: #6b7280;">Paid: {{ $pnl['collection_percentage'] }}%</span>
                                <span style="font-weight: 700; color: {{ $pnl['balance_due'] > 0 ? '#f59e0b' : '#059669' }};">
                                    Due: Rs. {{ number_format($pnl['balance_due'], 2) }}
                                </span>
                            </div>
                        </div>

                        <!-- Direct External Outflow -->
                        <div class="vb-metric-card" style="border-top: 3px solid #ef4444;">
                            <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #e11d48;">
                                Direct External Outflow
                            </div>
                            <div style="font-size: 1.35rem; font-weight: 900; color: #e11d48; margin-top: 0.25rem;">
                                Rs. {{ number_format($pnl['direct_external_outflow'], 2) }}
                            </div>
                            <div style="font-size: 0.7rem; color: #6b7280; margin-top: 0.25rem;">
                                Materials + Labour + Commission
                            </div>
                        </div>

                        <!-- Company Retained Margin -->
                        <div class="vb-metric-card" style="border-top: 3px solid #3b82f6; background-color: #f8fafc;">
                            <div style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: #1d4ed8;">
                                Company Retained Margin
                            </div>
                            <div style="font-size: 1.35rem; font-weight: 900; color: #1d4ed8; margin-top: 0.25rem;">
                                Rs. {{ number_format($pnl['company_retained_margin'], 2) }}
                            </div>
                            <div style="font-size: 0.7rem; color: #3b82f6; font-weight: 700; margin-top: 0.25rem;">
                                {{ $pnl['retained_margin_percentage'] }}% Retained (Keeps Overhead In-House)
                            </div>
                        </div>

                        <!-- NET PROFIT / LOSS (Bold highlight) -->
                        <div 
                            class="vb-metric-card {{ $pnl['is_profitable'] ? 'bg-emerald-50' : 'bg-rose-50' }}" 
                            style="border: 2px solid {{ $pnl['is_profitable'] ? '#10b981' : '#ef4444' }};"
                        >
                            <div style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: {{ $pnl['is_profitable'] ? '#059669' : '#e11d48' }};">
                                {{ $pnl['is_profitable'] ? 'NET PROFIT' : 'NET LOSS' }}
                            </div>
                            <div style="font-size: 1.45rem; font-weight: 900; color: {{ $pnl['is_profitable'] ? '#059669' : '#e11d48' }}; margin-top: 0.25rem;">
                                Rs. {{ number_format($pnl['net_profit'], 2) }}
                            </div>
                            <div style="font-size: 0.75rem; font-weight: 800; margin-top: 0.25rem; color: {{ $pnl['is_profitable'] ? '#059669' : '#e11d48' }};">
                                Margin: {{ $pnl['net_margin'] }}%
                            </div>
                        </div>
                    </div>

                    <!-- 2 Columns: Cost Ledger & Interactive ApexCharts Donut Chart -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 1.5rem; align-items: start;">
                        
                        <!-- Left Column: Cost Breakdown & Linked Expenses -->
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <div style="background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.25rem;" class="dark:bg-gray-900 dark:border-gray-800">
                                <div style="font-size: 0.85rem; font-weight: 800; color: #111827; margin-bottom: 0.85rem; display: flex; align-items: center; justify-content: space-between;">
                                    <span>Project Cost & P&L Statement</span>
                                    <span style="font-size: 0.7rem; color: #6b7280; font-weight: normal;">All amounts in LKR</span>
                                </div>

                                <div style="display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.8rem;">
                                    <!-- Contract Value -->
                                    <div style="display: flex; justify-content: space-between; padding: 0.35rem 0; border-bottom: 1px solid #f3f4f6;">
                                        <span style="font-weight: 600; color: #111827;">Contract Revenue (Grand Total)</span>
                                        <span style="font-weight: 700; color: #111827;">Rs. {{ number_format($pnl['contract_value'], 2) }}</span>
                                    </div>

                                    <!-- Section A: Direct External Vendor Outflow -->
                                    <div style="margin-top: 0.35rem; padding: 0.3rem 0.5rem; background-color: #fef2f2; border-left: 3px solid #ef4444; border-radius: 0.25rem; font-size: 0.72rem; font-weight: 800; color: #991b1b; text-transform: uppercase;">
                                        Section A: Direct External Vendor Outflow
                                    </div>

                                    <!-- Direct Materials & Hardware -->
                                    <div style="display: flex; justify-content: space-between; padding: 0.35rem 0.5rem; border-bottom: 1px solid #f3f4f6; color: #0284c7;">
                                        <div style="display: flex; align-items: center; gap: 0.35rem;">
                                            <span style="width: 0.5rem; height: 0.5rem; border-radius: 9999px; background-color: #0284c7;"></span>
                                            <span>Material Bills & Procurement</span>
                                        </div>
                                        <span style="font-weight: 700;">- Rs. {{ number_format($pnl['materials_cost'], 2) }}</span>
                                    </div>

                                    <!-- Labour & Operations -->
                                    <div style="display: flex; justify-content: space-between; padding: 0.35rem 0.5rem; border-bottom: 1px solid #f3f4f6; color: #f59e0b;">
                                        <div style="display: flex; align-items: center; gap: 0.35rem;">
                                            <span style="width: 0.5rem; height: 0.5rem; border-radius: 9999px; background-color: #f59e0b;"></span>
                                            <span>Installation Labour & Site Logistics</span>
                                        </div>
                                        <span style="font-weight: 700;">- Rs. {{ number_format($pnl['labour_operational_cost'], 2) }}</span>
                                    </div>

                                    <!-- Commission -->
                                    <div style="display: flex; justify-content: space-between; padding: 0.35rem 0.5rem; border-bottom: 1px solid #f3f4f6; color: #8b5cf6;">
                                        <div style="display: flex; align-items: center; gap: 0.35rem;">
                                            <span style="width: 0.5rem; height: 0.5rem; border-radius: 9999px; background-color: #8b5cf6;"></span>
                                            <span>Sales Agent Commission</span>
                                        </div>
                                        <span style="font-weight: 700;">- Rs. {{ number_format($pnl['commission_amount'], 2) }}</span>
                                    </div>

                                    <!-- Total Section A Outflow -->
                                    <div style="display: flex; justify-content: space-between; padding: 0.45rem 0.65rem; background-color: #fff1f2; border-radius: 0.375rem; font-weight: 700; color: #be123c;">
                                        <span>Total Direct External Outflow</span>
                                        <span>- Rs. {{ number_format($pnl['direct_external_outflow'], 2) }}</span>
                                    </div>

                                    <!-- Company Retained Margin Box -->
                                    <div style="margin: 0.25rem 0; padding: 0.65rem 0.85rem; background-color: #eff6ff; border: 1.5px solid #3b82f6; border-radius: 0.5rem; display: flex; flex-direction: column; gap: 0.2rem;">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <span style="font-weight: 800; color: #1e40af; font-size: 0.8rem;">
                                                Company Retained Margin = Contract Revenue - Direct External Outflow
                                            </span>
                                            <span style="font-weight: 900; color: #1d4ed8; font-size: 0.95rem;">
                                                Rs. {{ number_format($pnl['company_retained_margin'], 2) }}
                                            </span>
                                        </div>
                                        <div style="font-size: 0.68rem; color: #2563eb; display: flex; justify-content: space-between;">
                                            <span>Keeps office overhead inside company gross profit pool</span>
                                            <span style="font-weight: 700;">{{ $pnl['retained_margin_percentage'] }}% Retained</span>
                                        </div>
                                    </div>

                                    <!-- Section B: Internal Company Allocation -->
                                    <div style="margin-top: 0.25rem; padding: 0.3rem 0.5rem; background-color: #fffbeb; border-left: 3px solid #d97706; border-radius: 0.25rem; font-size: 0.72rem; font-weight: 800; color: #92400e; text-transform: uppercase;">
                                        Section B: Internal Company Allocation
                                    </div>

                                    <div style="display: flex; justify-content: space-between; padding: 0.35rem 0.5rem; border-bottom: 1px solid #f3f4f6; color: #d97706;">
                                        <div style="display: flex; align-items: center; gap: 0.35rem;">
                                            <span style="width: 0.5rem; height: 0.5rem; border-radius: 9999px; background-color: #d97706;"></span>
                                            <span>Office Overhead Absorption (Internal Retained Fund)</span>
                                        </div>
                                        <span style="font-weight: 700;">- Rs. {{ number_format($pnl['internal_company_overhead'], 2) }}</span>
                                    </div>

                                    <!-- NET PROFIT -->
                                    <div style="display: flex; justify-content: space-between; padding: 0.65rem 0.85rem; border-radius: 0.5rem; font-weight: 900; font-size: 0.95rem; background-color: {{ $pnl['is_profitable'] ? '#ecfdf5' : '#fff1f2' }}; color: {{ $pnl['is_profitable'] ? '#059669' : '#e11d48' }};">
                                        <span>NET PROFIT / (LOSS)</span>
                                        <span>Rs. {{ number_format($pnl['net_profit'], 2) }}</span>
                                    </div>

                                    @if((float) ($pnl['invoice']->profit_markup ?? 0) > 0)
                                        <div style="font-size: 0.7rem; color: #059669; font-weight: 600; text-align: right;">
                                            ✨ Includes Rs. {{ number_format((float) $pnl['invoice']->profit_markup, 2) }} Hidden Margin Markup (100% pure profit / 0 COGS)
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Linked Expenses Table for this Invoice -->
                            <div style="background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.25rem;" class="dark:bg-gray-900 dark:border-gray-800">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                                    <div style="font-size: 0.85rem; font-weight: 800; color: #111827;">
                                        Linked Expense Vouchers ({{ $pnl['linked_expenses']->count() }})
                                    </div>
                                    <a 
                                        href="{{ route('filament.app.resources.expenses.index', ['tenant' => \Filament\Facades\Filament::getTenant()]) }}?tableFilters[invoice_id][value]={{ $pnl['invoice']->id }}"
                                        style="font-size: 0.7rem; font-weight: 700; color: #4f46e5; text-decoration: underline;"
                                    >
                                        Manage Expenses →
                                    </a>
                                </div>

                                <div style="overflow-x: auto;">
                                    <table style="width: 100%; font-size: 0.75rem; text-align: left; border-collapse: collapse;">
                                        <thead>
                                            <tr style="background-color: #f9fafb; color: #6b7280; font-size: 0.65rem; text-transform: uppercase; border-bottom: 1px solid #e5e7eb;">
                                                <th style="padding: 0.45rem 0.65rem;">Title / Payee</th>
                                                <th style="padding: 0.45rem 0.65rem;">Category</th>
                                                <th style="padding: 0.45rem 0.65rem;">Date</th>
                                                <th style="padding: 0.45rem 0.65rem; text-align: right;">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($pnl['linked_expenses'] as $expItem)
                                                <tr style="border-bottom: 1px solid #f3f4f6;">
                                                    <td style="padding: 0.45rem 0.65rem; font-weight: 600; color: #111827;">{{ $expItem->title }}</td>
                                                    <td style="padding: 0.45rem 0.65rem;">
                                                        <span style="padding: 0.15rem 0.45rem; border-radius: 0.25rem; font-size: 0.65rem; {{ $expItem->is_internal_overhead ? 'background-color: #fef3c7; color: #92400e; font-weight: 700;' : 'background-color: #f3f4f6; color: #374151;' }}">
                                                            {{ $expItem->category }}
                                                            @if($expItem->is_internal_overhead)
                                                                (Internal Overhead)
                                                            @endif
                                                        </span>
                                                    </td>
                                                    <td style="padding: 0.45rem 0.65rem; color: #6b7280;">{{ $expItem->expense_date ? $expItem->expense_date->format('d M') : '-' }}</td>
                                                    <td style="padding: 0.45rem 0.65rem; text-align: right; font-weight: 700; color: #111827;">
                                                        Rs. {{ number_format((float) $expItem->amount, 2) }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" style="padding: 1rem; text-align: center; color: #9ca3af; font-size: 0.75rem;">
                                                        No expense vouchers directly linked to this invoice yet.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Interactive ApexCharts Donut Chart -->
                        <div style="background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem;" class="dark:bg-gray-900 dark:border-gray-800">
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 800; color: #111827;">
                                    Revenue Share & Cost Breakdown
                                </div>
                                <div style="font-size: 0.7rem; color: #6b7280;">
                                    Interactive visual distribution of project value and profit margins.
                                </div>
                            </div>

                            <!-- ApexCharts Container -->
                            <div 
                                wire:key="pnl-chart-{{ $pnl['invoice']->id }}"
                                x-data="{
                                    chart: null,
                                    init() {
                                        const series = @js($pnl['chart_series']);
                                        const amounts = @js($pnl['chart_amounts']);
                                        const labels = @js($pnl['chart_labels']);
                                        const colors = @js($pnl['chart_colors']);
                                        const isDark = document.documentElement.classList.contains('dark');

                                        const options = {
                                            chart: {
                                                type: 'donut',
                                                height: 300,
                                                fontFamily: 'inherit',
                                                background: 'transparent',
                                                toolbar: { show: false }
                                            },
                                            series: series,
                                            labels: labels,
                                            colors: colors,
                                            stroke: { width: 2, colors: [isDark ? '#111827' : '#ffffff'] },
                                            plotOptions: {
                                                pie: {
                                                    donut: {
                                                        size: '68%',
                                                        labels: {
                                                            show: true,
                                                            name: { show: true, fontSize: '12px', color: isDark ? '#9ca3af' : '#6b7280' },
                                                            value: {
                                                                show: true,
                                                                fontSize: '17px',
                                                                fontWeight: 800,
                                                                color: isDark ? '#f9fafb' : '#111827',
                                                                formatter: (val) => val + '%'
                                                            },
                                                            total: {
                                                                show: true,
                                                                label: 'Net Margin',
                                                                fontSize: '11px',
                                                                fontWeight: 700,
                                                                color: isDark ? '#9ca3af' : '#6b7280',
                                                                formatter: () => '{{ $pnl['net_margin'] }}%'
                                                            }
                                                        }
                                                    }
                                                }
                                            },
                                            dataLabels: { enabled: false },
                                            legend: { show: false },
                                            tooltip: {
                                                theme: isDark ? 'dark' : 'light',
                                                y: {
                                                    formatter: function(val, opts) {
                                                        const amt = amounts[opts.seriesIndex] || 0;
                                                        return 'Rs. ' + Number(amt).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' (' + val + '%)';
                                                    }
                                                }
                                            }
                                        };

                                        const render = () => {
                                            if (window.ApexCharts) {
                                                const el = this.$refs.chartBox;
                                                if (el) {
                                                    el.innerHTML = '';
                                                    this.chart = new ApexCharts(el, options);
                                                    this.chart.render();
                                                }
                                            } else {
                                                setTimeout(render, 50);
                                            }
                                        };
                                        render();
                                    }
                                }"
                            >
                                <div x-ref="chartBox" style="min-height: 300px; display: flex; align-items: center; justify-content: center;"></div>
                            </div>

                            <!-- Custom Legend with exact amounts & percentages -->
                            <div style="display: flex; flex-direction: column; gap: 0.45rem; font-size: 0.75rem; border-top: 1px solid #f3f4f6; padding-top: 0.85rem;">
                                @php
                                    $legendItems = [
                                        ['label' => 'Direct Materials & Hardware', 'pct' => $pnl['chart_series'][0] ?? 0, 'amt' => $pnl['materials_cost'], 'color' => '#0284c7'],
                                        ['label' => 'Labour & Operational Costs', 'pct' => $pnl['chart_series'][1] ?? 0, 'amt' => $pnl['labour_operational_cost'], 'color' => '#f59e0b'],
                                        ['label' => 'Sales Agent Commission', 'pct' => $pnl['chart_series'][2] ?? 0, 'amt' => $pnl['commission_amount'], 'color' => '#8b5cf6'],
                                        ['label' => 'Internal Company Overhead', 'pct' => $pnl['chart_series'][3] ?? 0, 'amt' => $pnl['internal_company_overhead'], 'color' => '#d97706'],
                                        ['label' => 'Pure Net Profit Share', 'pct' => $pnl['chart_series'][4] ?? 0, 'amt' => max(0, $pnl['net_profit']), 'color' => '#10b981'],
                                    ];
                                @endphp
                                @foreach($legendItems as $item)
                                    <div style="display: flex; align-items: center; justify-content: space-between;">
                                        <div style="display: flex; align-items: center; gap: 0.45rem;">
                                            <span style="display: inline-block; width: 0.65rem; height: 0.65rem; border-radius: 9999px; background-color: {{ $item['color'] }};"></span>
                                            <span style="color: #4b5563;">{{ $item['label'] }}:</span>
                                        </div>
                                        <div style="font-weight: 700; color: #111827;">
                                            <span>Rs. {{ number_format($item['amt'], 2) }}</span>
                                            <span style="color: #9ca3af; font-size: 0.7rem; font-weight: normal; margin-left: 0.25rem;">({{ $item['pct'] }}%)</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </div>

                </div>
            @else
                <div style="padding: 2rem; text-align: center; color: #9ca3af; font-size: 0.85rem;">
                    No invoice selected or no invoices found for this company. Create an invoice to view project-level profitability.
                </div>
            @endif
        </x-filament::section>

        <!-- 3. Overall Period Financial Statement & Filter Bar -->
        <x-filament::section>
            <div style="display: flex; flex-direction: column; gap: 1rem; justify-content: space-between;">
                
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                    <!-- Period Presets -->
                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                        <span style="font-size: 0.75rem; font-weight: 700; color: #6b7280; text-transform: uppercase;">Accounting Period:</span>
                        @foreach([
                            'all' => 'All Time',
                            'this_month' => 'This Month',
                            'last_month' => 'Last Month',
                            'this_quarter' => 'This Quarter',
                            'this_year' => 'This Year',
                        ] as $key => $label)
                            <button 
                                type="button"
                                wire:click="setPeriod('{{ $key }}')" 
                                style="padding: 0.35rem 0.85rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600; border: 1px solid #d1d5db; cursor: pointer; transition: all 0.15s; {{ $period === $key ? 'background-color: #059669; color: #ffffff; border-color: #059669;' : 'background-color: #f9fafb; color: #374151;' }}"
                            >
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Custom Date Inputs -->
                    <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem;">
                        <span style="color: #6b7280;">From:</span>
                        <input 
                            type="date" 
                            wire:model.live="startDate" 
                            style="padding: 0.35rem 0.65rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.75rem; background-color: #f9fafb;"
                        />
                        <span style="color: #6b7280;">To:</span>
                        <input 
                            type="date" 
                            wire:model.live="endDate" 
                            style="padding: 0.35rem 0.65rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.75rem; background-color: #f9fafb;"
                        />
                    </div>
                </div>

            </div>
        </x-filament::section>

        <!-- 4. 6 Key Financial Metric Cards for the Selected Accounting Period -->
        <div class="vb-metrics-grid">
            
            <!-- Invoiced Sales -->
            <div class="vb-metric-card">
                <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #6b7280; margin-bottom: 0.25rem;">
                    Invoiced Sales
                </div>
                <div style="font-size: 1.25rem; font-weight: 800; color: #111827;">
                    Rs. {{ number_format($this->getInvoicedSales(), 2) }}
                </div>
                <div style="font-size: 0.65rem; color: #9ca3af; margin-top: 0.25rem;">
                    Discounts: Rs. {{ number_format($this->getTotalDiscounts(), 2) }}
                </div>
            </div>

            <!-- Cash Collected -->
            <div class="vb-metric-card">
                <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #059669; margin-bottom: 0.25rem;">
                    Cash Collected
                </div>
                <div style="font-size: 1.25rem; font-weight: 800; color: #059669;">
                    Rs. {{ number_format($this->getPaymentsCollected(), 2) }}
                </div>
                <div style="font-size: 0.65rem; color: #9ca3af; margin-top: 0.25rem;">
                    Realized payments in period
                </div>
            </div>

            <!-- Cost of Goods Sold -->
            <div class="vb-metric-card">
                <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #6b7280; margin-bottom: 0.25rem;">
                    Cost of Goods (COGS)
                </div>
                <div style="font-size: 1.25rem; font-weight: 800; color: #111827;">
                    Rs. {{ number_format($this->getCostOfGoodsSold(), 2) }}
                </div>
                <div style="font-size: 0.65rem; color: #9ca3af; margin-top: 0.25rem;">
                    Hardware & procurement costs
                </div>
            </div>

            <!-- Operating Expenses -->
            <div class="vb-metric-card">
                <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #e11d48; margin-bottom: 0.25rem;">
                    External Operating Expenses
                </div>
                <div style="font-size: 1.25rem; font-weight: 800; color: #e11d48;">
                    Rs. {{ number_format($this->getOperatingExpenses(), 2) }}
                </div>
                <div style="font-size: 0.65rem; color: #9ca3af; margin-top: 0.25rem;">
                    Direct vendor outflows (excl. absorbed overhead)
                </div>
            </div>

            <!-- Agent Commissions -->
            <div class="vb-metric-card">
                <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #d97706; margin-bottom: 0.25rem;">
                    Agent Commissions
                </div>
                <div style="font-size: 1.25rem; font-weight: 800; color: #d97706;">
                    Rs. {{ number_format($this->getAgentCommissions(), 2) }}
                </div>
                <div style="font-size: 0.65rem; color: #9ca3af; margin-top: 0.25rem;">
                    Project introducer payouts
                </div>
            </div>

            <!-- Net Profit / Loss -->
            <div class="vb-metric-card" style="border-left: 4px solid {{ $this->getNetProfit() >= 0 ? '#059669' : '#e11d48' }};">
                <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #6b7280; margin-bottom: 0.25rem;">
                    Period Net Profit / (Loss)
                </div>
                <div style="font-size: 1.25rem; font-weight: 800; color: {{ $this->getNetProfit() >= 0 ? '#059669' : '#e11d48' }};">
                    Rs. {{ number_format($this->getNetProfit(), 2) }}
                </div>
                <div style="font-size: 0.7rem; font-weight: 700; margin-top: 0.25rem; color: {{ $this->getNetProfit() >= 0 ? '#059669' : '#e11d48' }};">
                    Net Margin: {{ $this->getNetMarginPercentage() }}%
                </div>
            </div>

        </div>

        <!-- 5. 2 Columns: P&L Statement and Expense Breakdown -->
        <div class="vb-pnl-layout">
            
            <!-- Left: Detailed P&L Income Statement -->
            <div class="vb-pnl-statement">
                <x-filament::section>
                    <x-slot name="heading">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <x-filament::icon 
                                icon="heroicon-o-document-chart-bar" 
                                style="width: 1.25rem; height: 1.25rem; min-width: 1.25rem; min-height: 1.25rem; color: #059669;" 
                            />
                            <span>Profit & Loss (Income Statement) Summary</span>
                        </div>
                    </x-slot>

                    <div style="font-size: 0.8rem; display: flex; flex-direction: column; gap: 0.65rem; padding-top: 0.25rem;">
                        <div style="display: flex; justify-content: space-between; padding: 0.35rem 0; border-bottom: 1px solid #f3f4f6; color: #4b5563;">
                            <span>Gross Sales / Revenue</span>
                            <span style="font-weight: 600; color: #111827;">Rs. {{ number_format($this->getGrossSales(), 2) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.35rem 0; border-bottom: 1px solid #f3f4f6; color: #e11d48;">
                            <span>Less: Sales Discounts</span>
                            <span>- Rs. {{ number_format($this->getTotalDiscounts(), 2) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.5rem 0.65rem; border-bottom: 1px solid #e5e7eb; font-weight: 700; background-color: #f9fafb; border-radius: 0.375rem;">
                            <span>Net Invoiced Revenue</span>
                            <span>Rs. {{ number_format($this->getInvoicedSales(), 2) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.35rem 0; border-bottom: 1px solid #f3f4f6; color: #4b5563;">
                            <span>Less: Cost of Goods Sold (Hardware / Components)</span>
                            <span style="color: #e11d48;">- Rs. {{ number_format($this->getCostOfGoodsSold(), 2) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.5rem 0.65rem; border-bottom: 1px solid #e5e7eb; font-weight: 700; background-color: #ecfdf5; color: #065f46; border-radius: 0.375rem;">
                            <span>Gross Profit (Margin: {{ $this->getGrossMarginPercentage() }}%)</span>
                            <span>Rs. {{ number_format($this->getGrossProfit(), 2) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.35rem 0; border-bottom: 1px solid #f3f4f6; color: #4b5563;">
                            <span>Less: External Vendor Operating Expenses</span>
                            <span style="color: #e11d48;">- Rs. {{ number_format($this->getExternalExpenses(), 2) }}</span>
                        </div>
                        @if($this->getInternalOverhead() > 0)
                            <div style="display: flex; justify-content: space-between; padding: 0.35rem 0; border-bottom: 1px solid #f3f4f6; color: #059669; font-size: 0.75rem;">
                                <span>Company Absorbed Overhead (Retained Operating Fund)</span>
                                <span style="font-weight: 700;">Rs. {{ number_format($this->getInternalOverhead(), 2) }}</span>
                            </div>
                        @endif
                        <div style="display: flex; justify-content: space-between; padding: 0.35rem 0; border-bottom: 1px solid #f3f4f6; color: #d97706;">
                            <span>Less: Sales Agent Referral Commissions</span>
                            <span style="color: #d97706;">- Rs. {{ number_format($this->getAgentCommissions(), 2) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.75rem 0.85rem; border-top: 2px solid #059669; font-size: 0.95rem; font-weight: 800; background-color: #f9fafb; border-radius: 0.5rem; margin-top: 0.25rem;">
                            <span style="color: {{ $this->getNetProfit() >= 0 ? '#059669' : '#e11d48' }};">
                                NET PROFIT / (LOSS)
                            </span>
                            <span style="color: {{ $this->getNetProfit() >= 0 ? '#059669' : '#e11d48' }};">
                                Rs. {{ number_format($this->getNetProfit(), 2) }}
                            </span>
                        </div>
                    </div>
                </x-filament::section>
            </div>

            <!-- Right: Expense Breakdown by Category -->
            <div class="vb-pnl-expenses">
                <x-filament::section heading="Operating Expenses by Category">
                    <div style="display: flex; flex-direction: column; gap: 0.85rem; padding-top: 0.25rem;">
                        @forelse($this->getExpenseBreakdown() as $exp)
                            <div style="font-size: 0.75rem; display: flex; flex-direction: column; gap: 0.25rem;">
                                <div style="display: flex; justify-content: space-between; font-weight: 600;">
                                    <span style="color: #111827;">{{ is_object($exp) ? $exp->category : ($exp['category'] ?? '') }} ({{ is_object($exp) ? $exp->count : ($exp['count'] ?? 0) }})</span>
                                    <span style="font-weight: 700; color: #111827;">Rs. {{ number_format((float) (is_object($exp) ? $exp->total : ($exp['total'] ?? 0)), 2) }}</span>
                                </div>
                                <div style="width: 100%; background-color: #f3f4f6; border-radius: 9999px; height: 0.4rem; overflow: hidden;">
                                    <div style="background-color: #f43f5e; height: 100%; border-radius: 9999px; width: {{ is_object($exp) ? $exp->percentage : ($exp['percentage'] ?? 0) }}%;"></div>
                                </div>
                                <div style="font-size: 0.65rem; color: #9ca3af; text-align: right;">
                                    {{ is_object($exp) ? $exp->percentage : ($exp['percentage'] ?? 0) }}% of total expenses
                                </div>
                            </div>
                        @empty
                            <div style="padding: 1.5rem; text-align: center; color: #9ca3af; font-size: 0.75rem;">
                                No expenses recorded during this period.
                            </div>
                        @endforelse
                    </div>
                </x-filament::section>
            </div>

        </div>

        <!-- 6. Customer Receivables Aging Summary (Who Owes Money) -->
        <x-filament::section>
            <x-slot name="heading">
                <div style="display: flex; align-items: center; justify-content: space-between; width: 100%; flex-wrap: wrap; gap: 0.5rem;">
                    <div>
                        <div style="font-size: 0.9rem; font-weight: 700; color: #111827;">
                            Customer Receivables Aging Summary (Outstanding Balances)
                        </div>
                        <div style="font-size: 0.75rem; color: #6b7280;">Clients with pending invoice payments — Click any invoice to analyze its project P&L</div>
                    </div>
                    <div style="text-align: right;">
                        <span style="font-size: 0.7rem; color: #9ca3af; display: block;">Total Outstanding:</span>
                        <span style="font-size: 0.95rem; font-weight: 800; color: #e11d48;">
                            Rs. {{ number_format($this->getTotalReceivables(), 2) }}
                        </span>
                    </div>
                </div>
            </x-slot>

            <div style="overflow-x: auto; margin-top: 0.5rem;">
                <table style="width: 100%; font-size: 0.75rem; text-align: left; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f9fafb; color: #6b7280; font-size: 0.65rem; text-transform: uppercase; border-bottom: 1px solid #e5e7eb;">
                            <th style="padding: 0.65rem 0.85rem;">Invoice #</th>
                            <th style="padding: 0.65rem 0.85rem;">Customer Name</th>
                            <th style="padding: 0.65rem 0.85rem;">CEB Account No</th>
                            <th style="padding: 0.65rem 0.85rem;">Invoice Date</th>
                            <th style="padding: 0.65rem 0.85rem; text-align: right;">Invoice Total</th>
                            <th style="padding: 0.65rem 0.85rem; text-align: right;">Amount Paid</th>
                            <th style="padding: 0.65rem 0.85rem; text-align: right;">Balance Due</th>
                            <th style="padding: 0.65rem 0.85rem; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->getReceivablesSummary() as $rec)
                            @php
                                $recInvNo = is_object($rec) ? $rec->invoice_number : ($rec['invoice_number'] ?? '');
                                $recContact = is_object($rec) ? $rec->contact : ($rec['contact'] ?? null);
                                $recContactName = is_object($recContact) ? $recContact->name : ($recContact['name'] ?? 'Unknown');
                                $recContactPhone = is_object($recContact) ? $recContact->phone : ($recContact['phone'] ?? null);
                                $recCebNo = is_object($recContact) ? $recContact->ceb_account_no : ($recContact['ceb_account_no'] ?? null);
                                $recDateRaw = is_object($rec) ? $rec->invoice_date : ($rec['invoice_date'] ?? null);
                                $recDate = $recDateRaw ? ($recDateRaw instanceof \Carbon\CarbonInterface ? $recDateRaw->format('d M Y') : \Carbon\Carbon::parse($recDateRaw)->format('d M Y')) : '-';
                                $recTotal = (float) (is_object($rec) ? $rec->grand_total : ($rec['grand_total'] ?? 0));
                                $recPaid = (float) (is_object($rec) ? $rec->paid_amount : ($rec['paid_amount'] ?? 0));
                                $recDue = (float) (is_object($rec) ? $rec->due_amount : ($rec['due_amount'] ?? 0));
                                $recId = is_object($rec) ? $rec->id : ($rec['id'] ?? 0);
                            @endphp
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 0.65rem 0.85rem; font-family: monospace; font-weight: 700; color: #111827;">{{ $recInvNo }}</td>
                                <td style="padding: 0.65rem 0.85rem; font-weight: 600; color: #111827;">
                                    {{ $recContactName }}
                                    @if(!empty($recContactPhone))
                                        <span style="display: block; font-size: 0.65rem; color: #9ca3af; font-weight: normal;">{{ $recContactPhone }}</span>
                                    @endif
                                </td>
                                <td style="padding: 0.65rem 0.85rem; color: #b45309; font-weight: 600;">
                                    {{ $recCebNo ?? 'N/A' }}
                                </td>
                                <td style="padding: 0.65rem 0.85rem; color: #6b7280;">{{ $recDate }}</td>
                                <td style="padding: 0.65rem 0.85rem; text-align: right;">Rs. {{ number_format($recTotal, 2) }}</td>
                                <td style="padding: 0.65rem 0.85rem; text-align: right; color: #059669; font-weight: 600;">Rs. {{ number_format($recPaid, 2) }}</td>
                                <td style="padding: 0.65rem 0.85rem; text-align: right; font-weight: 800; color: #e11d48;">
                                    Rs. {{ number_format($recDue, 2) }}
                                </td>
                                <td style="padding: 0.65rem 0.85rem; text-align: center;">
                                    <button 
                                        type="button"
                                        wire:click="selectInvoice({{ $recId }})"
                                        style="padding: 0.25rem 0.5rem; background-color: #eef2ff; color: #4f46e5; border-radius: 0.375rem; font-size: 0.7rem; font-weight: 700; border: 1px solid #c7d2fe; cursor: pointer;"
                                        title="Examine project P&L and cost breakdown"
                                    >
                                        Analyze P&L →
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="padding: 2rem; text-align: center; color: #9ca3af;">
                                    No outstanding receivables found. All invoices are fully paid!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>

    </div>
</x-filament-panels::page>

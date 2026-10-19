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
        .dark input[type="date"] {
            background-color: #1e293b !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
            color: #f8fafc !important;
        }
        .dark div[style*="background-color: #f9fafb"] {
            background-color: #111827 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            color: #f8fafc !important;
        }
        .dark div[style*="border-bottom: 1px solid #f3f4f6"] {
            border-bottom-color: rgba(255, 255, 255, 0.06) !important;
        }
        .dark div[style*="color: #111827"], .dark span[style*="color: #111827"] {
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
    </style>

    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        <!-- Date Range & Period Filter Bar -->
        <x-filament::section>
            <div style="display: flex; flex-direction: column; gap: 1rem; justify-content: space-between;">
                
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                    <!-- Period Presets -->
                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                        <span style="font-size: 0.75rem; font-weight: 700; color: #6b7280; text-transform: uppercase;">Period:</span>
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

        <!-- 5 Key Financial Metric Cards -->
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
                    Realized payments
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
                    Operating Expenses
                </div>
                <div style="font-size: 1.25rem; font-weight: 800; color: #e11d48;">
                    Rs. {{ number_format($this->getOperatingExpenses(), 2) }}
                </div>
                <div style="font-size: 0.65rem; color: #9ca3af; margin-top: 0.25rem;">
                    Overheads & operating bills
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
                    Net Profit / (Loss)
                </div>
                <div style="font-size: 1.25rem; font-weight: 800; color: {{ $this->getNetProfit() >= 0 ? '#059669' : '#e11d48' }};">
                    Rs. {{ number_format($this->getNetProfit(), 2) }}
                </div>
                <div style="font-size: 0.7rem; font-weight: 700; margin-top: 0.25rem; color: {{ $this->getNetProfit() >= 0 ? '#059669' : '#e11d48' }};">
                    Net Margin: {{ $this->getNetMarginPercentage() }}%
                </div>
            </div>

        </div>

        <!-- 2 Columns: P&L Statement and Expense Breakdown -->
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
                            <span>Less: Total Operating Overhead Expenses</span>
                            <span style="color: #e11d48;">- Rs. {{ number_format($this->getOperatingExpenses(), 2) }}</span>
                        </div>
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
                                    <span style="color: #111827;">{{ $exp->category }} ({{ $exp->count }})</span>
                                    <span style="font-weight: 700; color: #111827;">Rs. {{ number_format((float) $exp->total, 2) }}</span>
                                </div>
                                <div style="width: 100%; background-color: #f3f4f6; border-radius: 9999px; height: 0.4rem; overflow: hidden;">
                                    <div style="background-color: #f43f5e; height: 100%; border-radius: 9999px; width: {{ $exp->percentage }}%;"></div>
                                </div>
                                <div style="font-size: 0.65rem; color: #9ca3af; text-align: right;">
                                    {{ $exp->percentage }}% of total expenses
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

        <!-- Customer Receivables Aging Summary (Who Owes Money) -->
        <x-filament::section>
            <x-slot name="heading">
                <div style="display: flex; align-items: center; justify-content: space-between; width: 100%; flex-wrap: wrap; gap: 0.5rem;">
                    <div>
                        <div style="font-size: 0.9rem; font-weight: 700; color: #111827;">
                            Customer Receivables Aging Summary (Outstanding Balances)
                        </div>
                        <div style="font-size: 0.75rem; color: #6b7280;">Clients with pending invoice payments</div>
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
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($this->getReceivablesSummary() as $rec)
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 0.65rem 0.85rem; font-family: monospace; font-weight: 700; color: #111827;">{{ $rec->invoice_number }}</td>
                                <td style="padding: 0.65rem 0.85rem; font-weight: 600; color: #111827;">
                                    {{ $rec->contact->name ?? 'Unknown' }}
                                    @if(!empty($rec->contact->phone))
                                        <span style="display: block; font-size: 0.65rem; color: #9ca3af; font-weight: normal;">{{ $rec->contact->phone }}</span>
                                    @endif
                                </td>
                                <td style="padding: 0.65rem 0.85rem; color: #b45309; font-weight: 600;">
                                    {{ $rec->contact->ceb_account_no ?? 'N/A' }}
                                </td>
                                <td style="padding: 0.65rem 0.85rem; color: #6b7280;">{{ $rec->invoice_date ? $rec->invoice_date->format('d M Y') : '-' }}</td>
                                <td style="padding: 0.65rem 0.85rem; text-align: right;">Rs. {{ number_format((float) $rec->grand_total, 2) }}</td>
                                <td style="padding: 0.65rem 0.85rem; text-align: right; color: #059669; font-weight: 600;">Rs. {{ number_format((float) $rec->paid_amount, 2) }}</td>
                                <td style="padding: 0.65rem 0.85rem; text-align: right; font-weight: 800; color: #e11d48;">
                                    Rs. {{ number_format((float) $rec->due_amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding: 2rem; text-align: center; color: #9ca3af;">
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

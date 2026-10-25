@php
    /** @var \App\Models\Payroll|null $record */
    $payroll = $record ?? null;
    
    $totalBasic = (float) ($payroll?->total_basic ?? $get('total_basic') ?? 0);
    $totalAllowances = (float) ($payroll?->total_allowances ?? $get('total_allowances') ?? 0);
    $totalGross = (float) ($payroll?->total_gross ?? $get('total_gross') ?? 0);
    $totalNet = (float) ($payroll?->total_net_payable ?? $get('total_net_payable') ?? 0);
    $totalAdvances = (float) ($payroll?->total_advances ?? $get('total_advances') ?? 0);
    
    $epfEmp = (float) ($payroll?->total_epf_employee ?? $get('total_epf_employee') ?? 0);
    $epfEmpr = (float) ($payroll?->total_epf_employer ?? $get('total_epf_employer') ?? 0);
    $etfEmpr = (float) ($payroll?->total_etf_employer ?? $get('total_etf_employer') ?? 0);
    
    $statutoryCombined = round($epfEmp + $epfEmpr + $etfEmpr, 2);
    $employerStatutory = round($epfEmpr + $etfEmpr, 2);
    $totalEmployerCost = round($totalGross + $epfEmpr + $etfEmpr, 2);
    
    $status = $payroll?->status ?? 'draft';
    $statusColor = match($status) {
        'paid' => 'emerald',
        'confirmed' => 'blue',
        default => 'amber',
    };
    
    $statusLabel = match($status) {
        'paid' => 'Paid & Settled',
        'confirmed' => 'Confirmed (Ready for Payout)',
        default => 'Draft Run (Editable)',
    };

    $headcount = $payroll?->items()->count() ?? 0;
    
    // Bar segment calculations
    $netShare = $totalGross > 0 ? min(100, max(5, round(($totalNet / $totalGross) * 100))) : 70;
    $epfShare = $totalGross > 0 ? min(100, max(5, round(($epfEmp / $totalGross) * 100))) : 20;
    $advShare = $totalGross > 0 ? min(100, max(0, round(($totalAdvances / $totalGross) * 100))) : 10;
@endphp

<div class="payroll-executive-dashboard w-full mb-6">
    <style>
        .payroll-dashboard-card {
            background: #ffffff;
            border: 1px solid #e8ebee;
            border-radius: 14px;
            padding: 20px 22px;
            box-shadow: 0 2px 10px rgba(33, 40, 50, 0.04);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.2s ease;
        }
        .dark .payroll-dashboard-card {
            background: #111827;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        .payroll-dashboard-card:hover {
            transform: translateY(-2px);
            border-color: #3b82f6;
        }
        
        .payroll-progress-track {
            height: 12px;
            border-radius: 9999px;
            background: #f1f5f9;
            overflow: hidden;
            display: flex;
            border: 1px solid #e2e8f0;
        }
        .dark .payroll-progress-track {
            background: #1f2937;
            border-color: #374151;
        }
    </style>

    {{-- Top Executive Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-2xl mb-4 shadow-xs">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center text-2xl font-black shadow-xs">
                💼
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-extrabold text-gray-900 dark:text-white">
                        {{ $payroll?->title ?: 'Monthly Payroll Run' }}
                    </h3>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider
                        {{ $status === 'paid' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : '' }}
                        {{ $status === 'confirmed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' : '' }}
                        {{ $status === 'draft' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : '' }}
                    ">
                        {{ $statusLabel }}
                    </span>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 flex items-center gap-3">
                    <span>📅 Target Month: <strong class="text-gray-700 dark:text-gray-300">{{ $payroll?->month ?: now()->format('Y-m') }}</strong></span>
                    <span>•</span>
                    <span>👥 Total Headcount: <strong class="text-gray-700 dark:text-gray-300">{{ $headcount }} Employees</strong></span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs text-gray-500 dark:text-gray-400 hidden sm:inline">Statutory CTC:</span>
            <span class="text-sm font-black text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800 px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-700">
                Rs. {{ number_format($totalEmployerCost, 2) }}
            </span>
        </div>
    </div>

    {{-- 4 Metric Cards (Matching Zoho & Able Pro Dashboard Styling) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        {{-- Card 1: Total Gross Pay --}}
        <div class="payroll-dashboard-card">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Gross Remuneration</span>
                <span class="p-1.5 rounded-lg bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 text-sm">💵</span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                    Rs. {{ number_format($totalGross, 2) }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center justify-between">
                    <span>Basic: Rs. {{ number_format($totalBasic, 2) }}</span>
                    <span>Allow: Rs. {{ number_format($totalAllowances, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Card 2: Net Payable to Staff (Hero Emerald) --}}
        <div class="payroll-dashboard-card border-emerald-200 dark:border-emerald-800/80 bg-emerald-50/20 dark:bg-emerald-950/20">
            <div class="flex items-center justify-between">
                <span class="text-xs font-extrabold text-emerald-800 dark:text-emerald-300 uppercase tracking-wider">Net Payable to Staff</span>
                <span class="p-1.5 rounded-lg bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300 text-sm">💳</span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">
                    Rs. {{ number_format($totalNet, 2) }}
                </div>
                <div class="text-xs text-emerald-700/80 dark:text-emerald-400/80 mt-1 flex items-center justify-between font-medium">
                    <span>Bank Remittance Sum</span>
                    <span class="font-bold">{{ $headcount }} Payouts</span>
                </div>
            </div>
        </div>

        {{-- Card 3: Statutory Liabilities (EPF & ETF) --}}
        <div class="payroll-dashboard-card">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Statutory EPF & ETF</span>
                <span class="p-1.5 rounded-lg bg-purple-50 dark:bg-purple-950 text-purple-600 dark:text-purple-400 text-sm">🏛️</span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-purple-600 dark:text-purple-400 tracking-tight">
                    Rs. {{ number_format($statutoryCombined, 2) }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center justify-between">
                    <span>EPF 8%: Rs. {{ number_format($epfEmp, 2) }}</span>
                    <span>Gov Remittance</span>
                </div>
            </div>
        </div>

        {{-- Card 4: Advance Recoveries --}}
        <div class="payroll-dashboard-card">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Advance Recoveries</span>
                <span class="p-1.5 rounded-lg bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400 text-sm">✂️</span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-amber-600 dark:text-amber-400 tracking-tight">
                    Rs. {{ number_format($totalAdvances, 2) }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center justify-between">
                    <span>Recovered from Gross</span>
                    <span class="text-amber-600 dark:text-amber-400 font-semibold">Deducted</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Visual Cost Distribution Track & Statutory Breakdown Card --}}
    <div class="p-4 bg-white dark:bg-gray-900 border border-gray-200/80 dark:border-gray-800 rounded-2xl shadow-xs">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Gross Payroll Allocation</span>
            <div class="flex items-center gap-4 text-xs">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Take-Home Pay ({{ $netShare }}%)</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span> Employee EPF 8% ({{ $epfShare }}%)</span>
                @if($totalAdvances > 0)
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Advances ({{ $advShare }}%)</span>
                @endif
            </div>
        </div>

        <div class="payroll-progress-track mb-4">
            <div style="width: {{ $netShare }}%;" class="bg-emerald-500 h-full"></div>
            <div style="width: {{ $epfShare }}%;" class="bg-purple-500 h-full"></div>
            @if($totalAdvances > 0)
            <div style="width: {{ $advShare }}%;" class="bg-amber-500 h-full"></div>
            @endif
        </div>

        {{-- Detailed Statutory Liabilities Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-3 border-t border-gray-100 dark:border-gray-800 text-xs">
            <div class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-800">
                <div class="text-gray-500 dark:text-gray-400 text-[11px] font-medium">Employee EPF (8%)</div>
                <div class="text-sm font-extrabold text-gray-900 dark:text-white mt-0.5">Rs. {{ number_format($epfEmp, 2) }}</div>
                <div class="text-[10px] text-gray-400">Deducted from wages</div>
            </div>

            <div class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-800">
                <div class="text-gray-500 dark:text-gray-400 text-[11px] font-medium">Employer EPF (12%)</div>
                <div class="text-sm font-extrabold text-blue-600 dark:text-blue-400 mt-0.5">Rs. {{ number_format($epfEmpr, 2) }}</div>
                <div class="text-[10px] text-gray-400">Company statutory cost</div>
            </div>

            <div class="p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-800">
                <div class="text-gray-500 dark:text-gray-400 text-[11px] font-medium">Employer ETF (3%)</div>
                <div class="text-sm font-extrabold text-indigo-600 dark:text-indigo-400 mt-0.5">Rs. {{ number_format($etfEmpr, 2) }}</div>
                <div class="text-[10px] text-gray-400">Trust Fund remittance</div>
            </div>

            <div class="p-2.5 rounded-xl bg-purple-50/50 dark:bg-purple-950/30 border border-purple-200/60 dark:border-purple-800/50">
                <div class="text-purple-700 dark:text-purple-300 text-[11px] font-bold">Total Cost to Company (CTC)</div>
                <div class="text-sm font-black text-purple-900 dark:text-purple-200 mt-0.5">Rs. {{ number_format($totalEmployerCost, 2) }}</div>
                <div class="text-[10px] text-purple-600/80 dark:text-purple-400/80">Gross + 15% Statutory</div>
            </div>
        </div>
    </div>
</div>

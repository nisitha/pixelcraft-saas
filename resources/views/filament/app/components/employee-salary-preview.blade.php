@php
    $basic = (float) ($get('basic_salary') ?? 0);
    $allowance = (float) ($get('fixed_allowance') ?? 0);
    $gross = $basic + $allowance;
    $epfEmp = round($basic * 0.08, 2);
    $epfEmpr = round($basic * 0.12, 2);
    $etfEmpr = round($basic * 0.03, 2);
    $net = round($gross - $epfEmp, 2);
    $ctc = round($gross + $epfEmpr + $etfEmpr, 2);
@endphp

<div 
    x-data="{
        basic: {{ $basic }},
        allowance: {{ $allowance }},
        init() {
            const findInputs = () => {
                const basicInput = document.querySelector('input[wire\\:model*=\"basic_salary\"]') || document.querySelector('input[id*=\"basic_salary\"]');
                const allowanceInput = document.querySelector('input[wire\\:model*=\"fixed_allowance\"]') || document.querySelector('input[id*=\"fixed_allowance\"]');
                
                if (basicInput) {
                    basicInput.addEventListener('input', (e) => {
                        this.basic = parseFloat(e.target.value.replace(/[^0-9.]/g, '')) || 0;
                    });
                }
                if (allowanceInput) {
                    allowanceInput.addEventListener('input', (e) => {
                        this.allowance = parseFloat(e.target.value.replace(/[^0-9.]/g, '')) || 0;
                    });
                }
            };
            findInputs();
            setTimeout(findInputs, 500);
        },
        get gross() { return this.basic + this.allowance; },
        get epfEmp() { return Math.round(this.basic * 0.08 * 100) / 100; },
        get epfEmpr() { return Math.round(this.basic * 0.12 * 100) / 100; },
        get etfEmpr() { return Math.round(this.basic * 0.03 * 100) / 100; },
        get net() { return Math.round((this.gross - this.epfEmp) * 100) / 100; },
        get ctc() { return Math.round((this.gross + this.epfEmpr + this.etfEmpr) * 100) / 100; },
        format(val) {
            return 'Rs. ' + (val || 0).toLocaleString('en-LK', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    }"
    class="w-full mt-4"
>
    <style>
        .salary-calc-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            transition: all 0.2s ease;
        }
        .dark .salary-calc-box {
            background: #111827;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        .salary-metric-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
    </style>

    <div class="salary-calc-box">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg font-bold">
                    📊
                </div>
                <div>
                    <h4 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                        Live Remuneration & Statutory Estimation
                    </h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Calculates statutory EPF/ETF contributions and net take-home salary in real time
                    </p>
                </div>
            </div>
            <span class="salary-metric-badge bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                Automated Sri Lankan Statutory Math
            </span>
        </div>

        {{-- 3 Key Financial Highlights --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 my-4">
            {{-- Monthly Gross --}}
            <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-800">
                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                    Monthly Gross Remuneration
                </div>
                <div class="text-lg font-extrabold text-gray-900 dark:text-white mt-1" x-text="format(gross)">
                    Rs. {{ number_format($gross, 2) }}
                </div>
                <div class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">
                    Basic Salary + Fixed Allowance
                </div>
            </div>

            {{-- Net Take-Home (Prominent) --}}
            <div class="p-3.5 rounded-xl bg-emerald-50/70 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60">
                <div class="text-xs font-bold text-emerald-800 dark:text-emerald-300 uppercase tracking-wide flex items-center justify-between">
                    <span>Estimated Net Take-Home</span>
                    <span class="text-[10px] bg-emerald-200/60 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200 px-1.5 py-0.5 rounded">Payout</span>
                </div>
                <div class="text-xl font-black text-emerald-600 dark:text-emerald-400 mt-1" x-text="format(net)">
                    Rs. {{ number_format($net, 2) }}
                </div>
                <div class="text-[11px] text-emerald-700/80 dark:text-emerald-400/80 mt-0.5">
                    Gross Pay minus 8% Employee EPF
                </div>
            </div>

            {{-- Total Employer Cost (CTC) --}}
            <div class="p-3.5 rounded-xl bg-blue-50/70 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800/60">
                <div class="text-xs font-bold text-blue-800 dark:text-blue-300 uppercase tracking-wide flex items-center justify-between">
                    <span>Total Employer Cost (CTC)</span>
                    <span class="text-[10px] bg-blue-200/60 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-1.5 py-0.5 rounded">Liability</span>
                </div>
                <div class="text-xl font-black text-blue-600 dark:text-blue-400 mt-1" x-text="format(ctc)">
                    Rs. {{ number_format($ctc, 2) }}
                </div>
                <div class="text-[11px] text-blue-700/80 dark:text-blue-400/80 mt-0.5">
                    Gross Pay + 12% EPF + 3% ETF
                </div>
            </div>
        </div>

        {{-- Statutory Breakdown Pill Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-3 border-t border-gray-100 dark:border-gray-800 text-xs">
            <div class="flex flex-col gap-0.5 bg-gray-50/80 dark:bg-gray-800/40 p-2.5 rounded-lg border border-gray-100 dark:border-gray-800">
                <span class="text-gray-500 dark:text-gray-400 text-[11px]">Employee EPF (8%)</span>
                <span class="font-bold text-amber-600 dark:text-amber-400" x-text="format(epfEmp)">
                    Rs. {{ number_format($epfEmp, 2) }}
                </span>
                <span class="text-[10px] text-gray-400">Salary Deduction</span>
            </div>

            <div class="flex flex-col gap-0.5 bg-gray-50/80 dark:bg-gray-800/40 p-2.5 rounded-lg border border-gray-100 dark:border-gray-800">
                <span class="text-gray-500 dark:text-gray-400 text-[11px]">Employer EPF (12%)</span>
                <span class="font-bold text-blue-600 dark:text-blue-400" x-text="format(epfEmpr)">
                    Rs. {{ number_format($epfEmpr, 2) }}
                </span>
                <span class="text-[10px] text-gray-400">Company Contribution</span>
            </div>

            <div class="flex flex-col gap-0.5 bg-gray-50/80 dark:bg-gray-800/40 p-2.5 rounded-lg border border-gray-100 dark:border-gray-800">
                <span class="text-gray-500 dark:text-gray-400 text-[11px]">Employer ETF (3%)</span>
                <span class="font-bold text-indigo-600 dark:text-indigo-400" x-text="format(etfEmpr)">
                    Rs. {{ number_format($etfEmpr, 2) }}
                </span>
                <span class="text-[10px] text-gray-400">Trust Fund Remittance</span>
            </div>

            <div class="flex flex-col gap-0.5 bg-gray-50/80 dark:bg-gray-800/40 p-2.5 rounded-lg border border-gray-100 dark:border-gray-800">
                <span class="text-gray-500 dark:text-gray-400 text-[11px]">Total Statutory (23%)</span>
                <span class="font-bold text-purple-600 dark:text-purple-400" x-text="format(epfEmp + epfEmpr + etfEmpr)">
                    Rs. {{ number_format($epfEmp + $epfEmpr + $etfEmpr, 2) }}
                </span>
                <span class="text-[10px] text-gray-400">Central Bank Remittance</span>
            </div>
        </div>
    </div>
</div>

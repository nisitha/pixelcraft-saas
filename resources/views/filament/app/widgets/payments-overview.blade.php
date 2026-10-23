<div style="margin-bottom: 24px;">
    <style>
        .able-payment-stat-card {
            background: #ffffff;
            border: 1px solid #e8ebee;
            border-radius: 12px;
            padding: 18px 20px;
            box-shadow: 0 2px 12px rgba(33, 40, 50, 0.04);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .dark .able-payment-stat-card {
            background: #182230;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
        }
        .able-payment-stat-card:hover {
            transform: translateY(-2px);
            border-color: #4680ff;
        }
    </style>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 18px;">
        {{-- Card 1: Total Payments Collected (Reactive to Active Filters) --}}
        <div class="able-payment-stat-card">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: #eef4fe; color: #4680ff; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        💰
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 700; color: #1d2630;" class="dark:text-slate-100">Total Payments Collected</div>
                        <div style="font-size: 11px; color: #64748b;" class="dark:text-slate-400">Filtered Table Records</div>
                    </div>
                </div>
                <span style="background: #ecfdf5; color: #059669; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 9999px;">
                    Active Inflow
                </span>
            </div>
            <div style="margin-top: 16px; background: #f8f9fa; border-radius: 10px; padding: 12px 14px; display: flex; align-items: center; justify-content: space-between; border: 1px solid #eef1f4;" class="dark:bg-slate-900 dark:border-slate-800">
                <span style="font-size: 11.5px; color: #64748b;" class="dark:text-slate-400">Total Inflow</span>
                <span style="font-size: 16.5px; font-weight: 800; color: #4680ff;">Rs. {{ number_format($totalCollected ?? $thisMonthTotal, 2) }}</span>
            </div>
        </div>

        {{-- Card 2: Transactions Count --}}
        <div class="able-payment-stat-card">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: #e8fbf5; color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        🧾
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 700; color: #1d2630;" class="dark:text-slate-100">Transactions Count</div>
                        <div style="font-size: 11px; color: #64748b;" class="dark:text-slate-400">Filtered Entries</div>
                    </div>
                </div>
                <span style="background: #e8fbf5; color: #10b981; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 9999px;">
                    Volume
                </span>
            </div>
            <div style="margin-top: 16px; background: #f8f9fa; border-radius: 10px; padding: 12px 14px; display: flex; align-items: center; justify-content: space-between; border: 1px solid #eef1f4;" class="dark:bg-slate-900 dark:border-slate-800">
                <span style="font-size: 11.5px; color: #64748b;" class="dark:text-slate-400">Transactions</span>
                <span style="font-size: 16.5px; font-weight: 800; color: #10b981;">{{ number_format($transactionsCount ?? 0) }} Payments</span>
            </div>
        </div>

        {{-- Card 3: Bank Transfer vs Cash Breakdown --}}
        <div class="able-payment-stat-card">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: #f5f3ff; color: #8b5cf6; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        🏛️
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 700; color: #1d2630;" class="dark:text-slate-100">Bank vs Cash Breakdown</div>
                        <div style="font-size: 11px; color: #64748b;" class="dark:text-slate-400">Method Split</div>
                    </div>
                </div>
                <span style="background: #f5f3ff; color: #7c3aed; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 9999px;">
                    {{ $bankPercentage ?? 0 }}% Bank
                </span>
            </div>
            <div style="margin-top: 16px; background: #f8f9fa; border-radius: 10px; padding: 10px 14px; display: flex; flex-direction: column; gap: 4px; border: 1px solid #eef1f4;" class="dark:bg-slate-900 dark:border-slate-800">
                <div style="display: flex; justify-content: space-between; font-size: 11.5px;">
                    <span style="color: #64748b;" class="dark:text-slate-400">Bank / Online:</span>
                    <span style="font-weight: 700; color: #7c3aed;">Rs. {{ number_format($bankTransferTotal ?? $bankCardTotal ?? 0, 2) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 11.5px;">
                    <span style="color: #64748b;" class="dark:text-slate-400">Cash / Counter POS:</span>
                    <span style="font-weight: 700; color: #d97706;">Rs. {{ number_format($cashPosTotal ?? 0, 2) }} ({{ $cashPercentage ?? 0 }}%)</span>
                </div>
            </div>
        </div>

        {{-- Card 4: Today's Collections --}}
        <div class="able-payment-stat-card">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: #fff8ec; color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        📅
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 700; color: #1d2630;" class="dark:text-slate-100">Today's Collections</div>
                        <div style="font-size: 11px; color: #64748b;" class="dark:text-slate-400">{{ date('d M Y') }}</div>
                    </div>
                </div>
                <span style="background: #fff8ec; color: #d97706; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 9999px;">
                    Daily
                </span>
            </div>
            <div style="margin-top: 16px; background: #f8f9fa; border-radius: 10px; padding: 12px 14px; display: flex; align-items: center; justify-content: space-between; border: 1px solid #eef1f4;" class="dark:bg-slate-900 dark:border-slate-800">
                <span style="font-size: 11.5px; color: #64748b;" class="dark:text-slate-400">Today's Receipt</span>
                <span style="font-size: 16.5px; font-weight: 800; color: #d97706;">Rs. {{ number_format($todayTotal ?? 0, 2) }}</span>
            </div>
        </div>
    </div>
</div>

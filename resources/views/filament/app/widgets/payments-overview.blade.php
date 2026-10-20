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
        {{-- Card 1: Total Collected (This Month) --}}
        <div class="able-payment-stat-card">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: #eef4fe; color: #4680ff; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        💰
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 700; color: #1d2630;" class="dark:text-slate-100">Total Collected</div>
                        <div style="font-size: 11px; color: #64748b;" class="dark:text-slate-400">{{ date('F Y') }}</div>
                    </div>
                </div>
                <span style="background: {{ $trend >= 0 ? '#ecfdf5' : '#feeceb' }}; color: {{ $trend >= 0 ? '#059669' : '#dc2626' }}; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 9999px;">
                    {{ $trend >= 0 ? '↗ +' : '↘ ' }}{{ $trend }}%
                </span>
            </div>
            <div style="margin-top: 16px; background: #f8f9fa; border-radius: 10px; padding: 12px 14px; display: flex; align-items: center; justify-content: space-between; border: 1px solid #eef1f4;" class="dark:bg-slate-900 dark:border-slate-800">
                <span style="font-size: 11.5px; color: #64748b;" class="dark:text-slate-400">Total Inflow</span>
                <span style="font-size: 16.5px; font-weight: 800; color: #4680ff;">Rs. {{ number_format($thisMonthTotal, 2) }}</span>
            </div>
        </div>

        {{-- Card 2: Today's Collections --}}
        <div class="able-payment-stat-card">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: #e8fbf5; color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        📅
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 700; color: #1d2630;" class="dark:text-slate-100">Today's Collections</div>
                        <div style="font-size: 11px; color: #64748b;" class="dark:text-slate-400">{{ date('d M Y') }}</div>
                    </div>
                </div>
                <span style="background: #e8fbf5; color: #10b981; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 9999px;">
                    Daily
                </span>
            </div>
            <div style="margin-top: 16px; background: #f8f9fa; border-radius: 10px; padding: 12px 14px; display: flex; align-items: center; justify-content: space-between; border: 1px solid #eef1f4;" class="dark:bg-slate-900 dark:border-slate-800">
                <span style="font-size: 11.5px; color: #64748b;" class="dark:text-slate-400">Today's Receipt</span>
                <span style="font-size: 16.5px; font-weight: 800; color: #10b981;">Rs. {{ number_format($todayTotal, 2) }}</span>
            </div>
        </div>

        {{-- Card 3: Cash & POS Collections --}}
        <div class="able-payment-stat-card">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: #fff8ec; color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        💵
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 700; color: #1d2630;" class="dark:text-slate-100">Cash & Counter POS</div>
                        <div style="font-size: 11px; color: #64748b;" class="dark:text-slate-400">Direct In-hand</div>
                    </div>
                </div>
                <span style="background: #fff8ec; color: #d97706; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 9999px;">
                    Immediate
                </span>
            </div>
            <div style="margin-top: 16px; background: #f8f9fa; border-radius: 10px; padding: 12px 14px; display: flex; align-items: center; justify-content: space-between; border: 1px solid #eef1f4;" class="dark:bg-slate-900 dark:border-slate-800">
                <span style="font-size: 11.5px; color: #64748b;" class="dark:text-slate-400">Cash/POS Total</span>
                <span style="font-size: 16.5px; font-weight: 800; color: #d97706;">Rs. {{ number_format($cashPosTotal, 2) }}</span>
            </div>
        </div>

        {{-- Card 4: Bank Transfer & Card Collections --}}
        <div class="able-payment-stat-card">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: #f5f3ff; color: #8b5cf6; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        🏛️
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 700; color: #1d2630;" class="dark:text-slate-100">Bank & Online Cards</div>
                        <div style="font-size: 11px; color: #64748b;" class="dark:text-slate-400">Account Wire / Slip</div>
                    </div>
                </div>
                <span style="background: #f5f3ff; color: #7c3aed; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 9999px;">
                    Cleared
                </span>
            </div>
            <div style="margin-top: 16px; background: #f8f9fa; border-radius: 10px; padding: 12px 14px; display: flex; align-items: center; justify-content: space-between; border: 1px solid #eef1f4;" class="dark:bg-slate-900 dark:border-slate-800">
                <span style="font-size: 11.5px; color: #64748b;" class="dark:text-slate-400">Bank Total</span>
                <span style="font-size: 16.5px; font-weight: 800; color: #7c3aed;">Rs. {{ number_format($bankCardTotal, 2) }}</span>
            </div>
        </div>
    </div>
</div>

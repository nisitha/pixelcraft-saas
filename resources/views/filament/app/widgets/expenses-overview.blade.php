<div style="margin-bottom: 24px;">
    <style>
        .able-expense-stat-card {
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
        .dark .able-expense-stat-card {
            background: #182230;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
        }
        .able-expense-stat-card:hover {
            transform: translateY(-2px);
            border-color: #ef4444;
        }
    </style>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 18px;">
        {{-- Card 1: Total Expenses (This Month) --}}
        <div class="able-expense-stat-card">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: #feeceb; color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        📉
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 700; color: #1d2630;" class="dark:text-slate-100">Total Expenses</div>
                        <div style="font-size: 11px; color: #64748b;" class="dark:text-slate-400">{{ date('F Y') }}</div>
                    </div>
                </div>
                <span style="background: #feeceb; color: #dc2626; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 9999px;">
                    Outflow
                </span>
            </div>
            <div style="margin-top: 16px; background: #f8f9fa; border-radius: 10px; padding: 12px 14px; display: flex; align-items: center; justify-content: space-between; border: 1px solid #eef1f4;" class="dark:bg-slate-900 dark:border-slate-800">
                <span style="font-size: 11.5px; color: #64748b;" class="dark:text-slate-400">Monthly Burn</span>
                <span style="font-size: 16.5px; font-weight: 800; color: #ef4444;">Rs. {{ number_format($thisMonthExpenses, 2) }}</span>
            </div>
        </div>

        {{-- Card 2: Equipment & Supplier Bills --}}
        <div class="able-expense-stat-card">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: #fff8ec; color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        📦
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 700; color: #1d2630;" class="dark:text-slate-100">Equipment & Suppliers</div>
                        <div style="font-size: 11px; color: #64748b;" class="dark:text-slate-400">Hardware & Materials</div>
                    </div>
                </div>
                <span style="background: #fff8ec; color: #d97706; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 9999px;">
                    Inventory
                </span>
            </div>
            <div style="margin-top: 16px; background: #f8f9fa; border-radius: 10px; padding: 12px 14px; display: flex; align-items: center; justify-content: space-between; border: 1px solid #eef1f4;" class="dark:bg-slate-900 dark:border-slate-800">
                <span style="font-size: 11.5px; color: #64748b;" class="dark:text-slate-400">Supplier Bills</span>
                <span style="font-size: 16.5px; font-weight: 800; color: #d97706;">Rs. {{ number_format($equipmentSupplierTotal, 2) }}</span>
            </div>
        </div>

        {{-- Card 3: Installation, Labour & Site Food --}}
        <div class="able-expense-stat-card">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: #eef4fe; color: #4680ff; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        🔧
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 700; color: #1d2630;" class="dark:text-slate-100">Labour & Site Food</div>
                        <div style="font-size: 11px; color: #64748b;" class="dark:text-slate-400">Technicians & Travel</div>
                    </div>
                </div>
                <span style="background: #eef4fe; color: #2563eb; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 9999px;">
                    Field Work
                </span>
            </div>
            <div style="margin-top: 16px; background: #f8f9fa; border-radius: 10px; padding: 12px 14px; display: flex; align-items: center; justify-content: space-between; border: 1px solid #eef1f4;" class="dark:bg-slate-900 dark:border-slate-800">
                <span style="font-size: 11.5px; color: #64748b;" class="dark:text-slate-400">Field Expenses</span>
                <span style="font-size: 16.5px; font-weight: 800; color: #2563eb;">Rs. {{ number_format($labourInstallationTotal, 2) }}</span>
            </div>
        </div>

        {{-- Card 4: Office & Operational Overhead --}}
        <div class="able-expense-stat-card">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: #f5f3ff; color: #8b5cf6; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        🏢
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 700; color: #1d2630;" class="dark:text-slate-100">Office & Overheads</div>
                        <div style="font-size: 11px; color: #64748b;" class="dark:text-slate-400">Utilities & Admin</div>
                    </div>
                </div>
                <span style="background: #f5f3ff; color: #7c3aed; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 9999px;">
                    Overhead
                </span>
            </div>
            <div style="margin-top: 16px; background: #f8f9fa; border-radius: 10px; padding: 12px 14px; display: flex; align-items: center; justify-content: space-between; border: 1px solid #eef1f4;" class="dark:bg-slate-900 dark:border-slate-800">
                <span style="font-size: 11.5px; color: #64748b;" class="dark:text-slate-400">Operational Total</span>
                <span style="font-size: 16.5px; font-weight: 800; color: #7c3aed;">Rs. {{ number_format($operationalOverheadTotal, 2) }}</span>
            </div>
        </div>
    </div>
</div>

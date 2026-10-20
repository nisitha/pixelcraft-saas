<div class="able-pro-dashboard-wrapper" x-data="ableProDashboard(@js([
    'monthlyRevenue' => $monthlyRevenue,
    'monthlyExpenses' => $monthlyExpenses,
    'monthlyCashflow' => $monthlyCashflow,
    'totalRevenue' => $totalRevenue,
    'materialCost' => $materialCost,
    'netProfit' => $netProfit,
    'overheadAndCommissions' => $overheadAndCommissions,
]))" x-init="initDashboard()">
    {{-- ApexCharts CDN Fallback --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
        .able-pro-dashboard-wrapper {
            font-family: "Public Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #1d2630;
            display: flex;
            flex-direction: column;
            gap: 24px;
            margin-top: -8px;
        }

        .dark .able-pro-dashboard-wrapper {
            color: #f8fafc;
        }

        /* Able Pro Card Base */
        .able-card {
            background: #ffffff;
            border: 1px solid #e8ebee;
            border-radius: 12px;
            padding: 20px 24px;
            box-shadow: 0 2px 12px rgba(33, 40, 50, 0.04);
            position: relative;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .dark .able-card {
            background: #182230;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
        }

        .able-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .able-card-title {
            font-size: 15px;
            font-weight: 700;
            color: #1d2630;
            letter-spacing: -0.01em;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dark .able-card-title {
            color: #f8fafc;
        }

        .able-dots-btn {
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 16px;
            line-height: 1;
            padding: 4px 8px;
            border-radius: 6px;
            transition: background 0.15s ease, color 0.15s ease;
        }

        .able-dots-btn:hover {
            background: #f1f5f9;
            color: #1d2630;
        }

        .dark .able-dots-btn:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #f8fafc;
        }

        /* Responsive Grid System */
        .able-grid-12 {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 20px;
        }

        .able-col-12 { grid-column: span 12 / span 12; }
        .able-col-8  { grid-column: span 8 / span 8; }
        .able-col-7  { grid-column: span 7 / span 7; }
        .able-col-6  { grid-column: span 6 / span 6; }
        .able-col-5  { grid-column: span 5 / span 5; }
        .able-col-4  { grid-column: span 4 / span 4; }

        @media (max-width: 1024px) {
            .able-col-8, .able-col-7, .able-col-6, .able-col-5, .able-col-4 {
                grid-column: span 12 / span 12 !important;
            }
            .able-overview-columns {
                grid-template-columns: 1fr !important;
                gap: 16px !important;
            }
            .able-overview-divider {
                border-right: none !important;
                border-bottom: 1px solid #e8ebee !important;
                padding-right: 0 !important;
                padding-bottom: 14px !important;
            }
            .dark .able-overview-divider {
                border-bottom-color: rgba(255, 255, 255, 0.08) !important;
            }
        }

        /* Row 1 Hero Banner */
        .able-hero-banner {
            background: linear-gradient(135deg, #4680ff 0%, #2563eb 100%);
            border-radius: 14px;
            padding: 28px 36px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.35);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .able-hero-waves {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            width: 55%;
            background: radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.22) 0%, transparent 60%),
                        radial-gradient(circle at 40% 90%, rgba(255, 255, 255, 0.15) 0%, transparent 50%);
            pointer-events: none;
        }

        .able-hero-content {
            position: relative;
            z-index: 2;
            max-width: 650px;
        }

        .able-hero-title {
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
            margin: 0 0 8px 0;
            letter-spacing: -0.02em;
        }

        .able-hero-subtitle {
            font-size: 13.5px;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.5;
            margin: 0 0 20px 0;
        }

        .able-hero-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            border: 1.5px solid rgba(255, 255, 255, 0.7);
            border-radius: 9999px;
            padding: 8px 22px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            backdrop-filter: blur(6px);
            transition: all 0.2s ease;
        }

        .able-hero-btn:hover {
            background: #ffffff;
            color: #2563eb;
            border-color: #ffffff;
            transform: translateY(-1px);
        }

        .able-hero-illustration {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Row 2 Stat Cards */
        .stat-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .stat-inner-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 14px 16px;
            margin-top: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid #eef1f4;
        }

        .dark .stat-inner-box {
            background: #111827;
            border-color: rgba(255, 255, 255, 0.06);
        }

        .mini-bars {
            display: flex;
            align-items: flex-end;
            gap: 3px;
            height: 36px;
        }

        .mini-bar {
            width: 4px;
            border-radius: 2px;
            transition: height 0.3s ease;
        }

        /* Buttons & Badges */
        .able-btn-primary {
            background: #4680ff;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 9px 18px;
            font-size: 12.5px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: background 0.15s ease, transform 0.15s ease;
        }

        .able-btn-primary:hover {
            background: #3874f6;
            transform: translateY(-1px);
        }

        .able-btn-outline {
            background: #ffffff;
            color: #334155;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.15s ease;
        }

        .dark .able-btn-outline {
            background: #1e293b;
            color: #f8fafc;
            border-color: #334155;
        }

        .able-btn-outline:hover {
            background: #f8fafc;
            border-color: #94a3b8;
        }

        .dark .able-btn-outline:hover {
            background: #334155;
        }

        /* Tabs */
        .able-tab-btn {
            background: none;
            border: none;
            padding: 8px 14px;
            font-size: 12.5px;
            font-weight: 600;
            color: #64748b;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.15s ease;
        }

        .dark .able-tab-btn {
            color: #94a3b8;
        }

        .able-tab-btn.active {
            color: #4680ff;
            border-bottom-color: #4680ff;
            font-weight: 700;
        }

        .dark .able-tab-btn.active {
            color: #4680ff;
            border-bottom-color: #4680ff;
        }
    </style>

    {{-- ========================================================
         ROW 1: Able Pro Royal Blue Hero Banner Card (Full Width)
         ======================================================== --}}
    <div class="able-hero-banner">
        <div class="able-hero-waves"></div>
        <div class="able-hero-content">
            <h1 class="able-hero-title">
                Explore Redesigned {{ $tenant?->name ?? 'Vertex Books' }} ERP
            </h1>
            <p class="able-hero-subtitle">
                Complete Solar Photovoltaic System Sizing, Milestone Billing, Real-Time Inventory & Project Profitability Analysis in one high-performance executive workspace.
            </p>
            <a href="{{ \App\Filament\App\Resources\Quotations\QuotationResource::getUrl('create') }}" class="able-hero-btn">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Create New Quotation</span>
            </a>
        </div>
        <div class="able-hero-illustration">
            {{-- 3D Floating Rocket / Energy Concept SVG --}}
            <svg style="width: 140px; height: 140px; filter: drop-shadow(0 15px 25px rgba(0,0,0,0.25));" viewBox="0 0 120 120" fill="none">
                <circle cx="60" cy="60" r="50" fill="rgba(255, 255, 255, 0.12)" />
                <circle cx="60" cy="60" r="38" fill="rgba(255, 255, 255, 0.18)" />
                <path d="M60 22C60 22 76 34 76 56C76 68 70 76 60 82C50 76 44 68 44 56C44 34 60 22 60 22Z" fill="#ffffff" />
                <path d="M60 22C60 22 52 34 52 56C52 68 56 76 60 82C60 82 60 22 60 22Z" fill="#e0edff" />
                <circle cx="60" cy="46" r="6" fill="#2563eb" />
                <path d="M44 64L32 72L42 78L46 72" fill="#f59e0b" />
                <path d="M76 64L88 72L78 78L74 72" fill="#f59e0b" />
                <path d="M54 82L60 96L66 82" fill="#ef4444" />
                <path d="M57 82L60 90L63 82" fill="#fde047" />
            </svg>
        </div>
    </div>

    {{-- ========================================================
         ROW 2: 4 Able Pro Signature Stat Cards (4 Columns Grid)
         ======================================================== --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
        {{-- Card 1: All Earnings --}}
        <div class="able-card">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div class="stat-icon-box" style="background: #eef4fe; color: #4680ff;">
                        <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 700; color: #1d2630;" class="dark:text-slate-100">All Earnings</div>
                        <div style="font-size: 11px; color: #64748b;" class="dark:text-slate-400">Total Invoiced Revenue</div>
                    </div>
                </div>
                <button type="button" class="able-dots-btn">•••</button>
            </div>

            <div class="stat-inner-box">
                <div class="mini-bars">
                    <div class="mini-bar" style="height: 12px; background: #4680ff;"></div>
                    <div class="mini-bar" style="height: 18px; background: #4680ff;"></div>
                    <div class="mini-bar" style="height: 14px; background: #4680ff;"></div>
                    <div class="mini-bar" style="height: 24px; background: #4680ff;"></div>
                    <div class="mini-bar" style="height: 20px; background: #4680ff;"></div>
                    <div class="mini-bar" style="height: 32px; background: #4680ff;"></div>
                    <div class="mini-bar" style="height: 26px; background: #4680ff;"></div>
                    <div class="mini-bar" style="height: 36px; background: #4680ff;"></div>
                    <div class="mini-bar" style="height: 30px; background: #4680ff;"></div>
                    <div class="mini-bar" style="height: 28px; background: #4680ff;"></div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 17px; font-weight: 800; color: #1d2630;" class="dark:text-slate-100">
                        Rs. {{ number_format($totalRevenue / 1000000, 2) }}M
                    </div>
                    <div style="font-size: 11px; font-weight: 700; color: #4680ff; margin-top: 2px;">
                        ↗ 30.6%
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Quotations --}}
        <div class="able-card">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div class="stat-icon-box" style="background: #fff8ec; color: #f59e0b;">
                        <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 700; color: #1d2630;" class="dark:text-slate-100">Quotations</div>
                        <div style="font-size: 11px; color: #64748b;" class="dark:text-slate-400">Proposals & Sizing</div>
                    </div>
                </div>
                <button type="button" class="able-dots-btn">•••</button>
            </div>

            <div class="stat-inner-box">
                <div class="mini-bars">
                    <div class="mini-bar" style="height: 16px; background: #f59e0b;"></div>
                    <div class="mini-bar" style="height: 12px; background: #f59e0b;"></div>
                    <div class="mini-bar" style="height: 22px; background: #f59e0b;"></div>
                    <div class="mini-bar" style="height: 18px; background: #f59e0b;"></div>
                    <div class="mini-bar" style="height: 28px; background: #f59e0b;"></div>
                    <div class="mini-bar" style="height: 24px; background: #f59e0b;"></div>
                    <div class="mini-bar" style="height: 34px; background: #f59e0b;"></div>
                    <div class="mini-bar" style="height: 26px; background: #f59e0b;"></div>
                    <div class="mini-bar" style="height: 32px; background: #f59e0b;"></div>
                    <div class="mini-bar" style="height: 30px; background: #f59e0b;"></div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 17px; font-weight: 800; color: #1d2630;" class="dark:text-slate-100">
                        {{ $quotationsCount }}+
                    </div>
                    <div style="font-size: 11px; font-weight: 700; color: #d97706; margin-top: 2px;">
                        ↗ 22.4%
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Total Projects --}}
        <div class="able-card">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div class="stat-icon-box" style="background: #e8fbf5; color: #10b981;">
                        <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 700; color: #1d2630;" class="dark:text-slate-100">Total Projects</div>
                        <div style="font-size: 11px; color: #64748b;" class="dark:text-slate-400">Invoices & Jobs</div>
                    </div>
                </div>
                <button type="button" class="able-dots-btn">•••</button>
            </div>

            <div class="stat-inner-box">
                <div class="mini-bars">
                    <div class="mini-bar" style="height: 14px; background: #10b981;"></div>
                    <div class="mini-bar" style="height: 20px; background: #10b981;"></div>
                    <div class="mini-bar" style="height: 18px; background: #10b981;"></div>
                    <div class="mini-bar" style="height: 28px; background: #10b981;"></div>
                    <div class="mini-bar" style="height: 24px; background: #10b981;"></div>
                    <div class="mini-bar" style="height: 34px; background: #10b981;"></div>
                    <div class="mini-bar" style="height: 30px; background: #10b981;"></div>
                    <div class="mini-bar" style="height: 22px; background: #10b981;"></div>
                    <div class="mini-bar" style="height: 32px; background: #10b981;"></div>
                    <div class="mini-bar" style="height: 36px; background: #10b981;"></div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 17px; font-weight: 800; color: #1d2630;" class="dark:text-slate-100">
                        {{ number_format($invoicesCount) }}
                    </div>
                    <div style="font-size: 11px; font-weight: 700; color: #10b981; margin-top: 2px;">
                        ↗ 30.6%
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Total Expenses & Payouts --}}
        <div class="able-card">
            <div style="display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div class="stat-icon-box" style="background: #feeceb; color: #ef4444;">
                        <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                        </svg>
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 700; color: #1d2630;" class="dark:text-slate-100">Total Expenses</div>
                        <div style="font-size: 11px; color: #64748b;" class="dark:text-slate-400">COGS & Overheads</div>
                    </div>
                </div>
                <button type="button" class="able-dots-btn">•••</button>
            </div>

            <div class="stat-inner-box">
                <div class="mini-bars">
                    <div class="mini-bar" style="height: 20px; background: #ef4444;"></div>
                    <div class="mini-bar" style="height: 16px; background: #ef4444;"></div>
                    <div class="mini-bar" style="height: 28px; background: #ef4444;"></div>
                    <div class="mini-bar" style="height: 22px; background: #ef4444;"></div>
                    <div class="mini-bar" style="height: 32px; background: #ef4444;"></div>
                    <div class="mini-bar" style="height: 26px; background: #ef4444;"></div>
                    <div class="mini-bar" style="height: 18px; background: #ef4444;"></div>
                    <div class="mini-bar" style="height: 24px; background: #ef4444;"></div>
                    <div class="mini-bar" style="height: 30px; background: #ef4444;"></div>
                    <div class="mini-bar" style="height: 26px; background: #ef4444;"></div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 17px; font-weight: 800; color: #1d2630;" class="dark:text-slate-100">
                        Rs. {{ number_format($totalExpenses / 1000000, 2) }}M
                    </div>
                    <div style="font-size: 11px; font-weight: 700; color: #dc2626; margin-top: 2px;">
                        ↗ 12.4%
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================
         ROW 3: Smooth Area Spline Chart (8 Cols) + Pipeline (4 Cols)
         ======================================================== --}}
    <div class="able-grid-12">
        {{-- Left Area Spline Chart (8 Cols) --}}
        <div class="able-card able-col-8">
            <div class="able-card-header">
                <div>
                    <h3 class="able-card-title">Monthly Revenue & Customer Rate</h3>
                    <div style="font-size: 11.5px; color: #64748b; margin-top: 2px;" class="dark:text-slate-400">
                        Annual revenue progression with verified milestone receivables
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="background: #ecfdf5; color: #047857; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 9999px; border: 1px solid #a7f3d0;" class="dark:bg-emerald-950/60 dark:text-emerald-400 dark:border-emerald-800">
                        +{{ $profitMargin }}% Profit Margin
                    </span>
                    <button type="button" class="able-dots-btn">•••</button>
                </div>
            </div>

            <div id="ableProAreaChart" style="min-height: 310px;"></div>
        </div>

        {{-- Right Pipeline Card (4 Cols) --}}
        <div class="able-card able-col-4" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div class="able-card-header">
                    <div>
                        <h3 class="able-card-title">Solar Project Pipeline</h3>
                        <div style="font-size: 11.5px; color: #64748b; margin-top: 2px;" class="dark:text-slate-400">
                            Release / Conversion Rate: <strong style="color: #4680ff;">72%</strong>
                        </div>
                    </div>
                    <button type="button" class="able-dots-btn">•••</button>
                </div>

                {{-- Progress Bar --}}
                <div style="width: 100%; height: 7px; background: #e2e8f0; border-radius: 9999px; overflow: hidden; margin-bottom: 20px;" class="dark:bg-slate-700">
                    <div style="width: 72%; height: 100%; background: #4680ff; border-radius: 9999px;"></div>
                </div>

                {{-- Stage Items --}}
                <div style="display: flex; flex-direction: column; gap: 14px;">
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600;">
                            <span style="width: 9px; height: 9px; border-radius: 50%; background: #f59e0b;"></span>
                            <span style="color: #334155;" class="dark:text-slate-200">Pending Quotations</span>
                        </div>
                        <span style="background: #fff8ec; color: #d97706; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 6px;">
                            {{ $pendingQuotationsCount }}
                        </span>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600;">
                            <span style="width: 9px; height: 9px; border-radius: 50%; background: #4680ff;"></span>
                            <span style="color: #334155;" class="dark:text-slate-200">Active Installations</span>
                        </div>
                        <span style="background: #eef4fe; color: #2563eb; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 6px;">
                            {{ $activeInstallationsCount }}
                        </span>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600;">
                            <span style="width: 9px; height: 9px; border-radius: 50%; background: #10b981;"></span>
                            <span style="color: #334155;" class="dark:text-slate-200">Commissioned Systems</span>
                        </div>
                        <span style="background: #ecfdf5; color: #059669; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 6px;">
                            {{ $commissionedSystemsCount }}
                        </span>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600;">
                            <span style="width: 9px; height: 9px; border-radius: 50%; background: #8b5cf6;"></span>
                            <span style="color: #334155;" class="dark:text-slate-200">Certificates Issued</span>
                        </div>
                        <span style="background: #f5f3ff; color: #7c3aed; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 6px;">
                            {{ $certificatesCount }}
                        </span>
                    </div>
                </div>
            </div>

            <div style="margin-top: 24px;">
                <a href="{{ \App\Filament\App\Resources\Quotations\QuotationResource::getUrl('create') }}" class="able-btn-primary" style="width: 100%;">
                    + New Solar Quotation
                </a>
            </div>
        </div>
    </div>

    {{-- ========================================================
         ROW 4: Project Overview Wave Card (8 Cols) + Team Profile (4 Cols)
         ======================================================== --}}
    <div class="able-grid-12">
        {{-- Left Project Overview Wave Card (8 Cols) --}}
        <div class="able-card able-col-8">
            <div class="able-card-header">
                <h3 class="able-card-title">Project overview</h3>
                <button type="button" class="able-dots-btn">•••</button>
            </div>

            <div class="able-overview-columns" style="display: grid; grid-template-columns: 1fr 1fr 1.2fr; gap: 20px; align-items: center; padding: 10px 0;">
                {{-- Block 1: Completed Invoices --}}
                <div class="able-overview-divider" style="display: flex; align-items: center; justify-content: space-between; padding-right: 14px; border-right: 1px solid #e8ebee;" class="dark:border-slate-800">
                    <div>
                        <div style="font-size: 12px; color: #64748b;" class="dark:text-slate-400">Completed Invoices</div>
                        <div style="font-size: 20px; font-weight: 800; color: #1d2630; margin-top: 4px;" class="dark:text-slate-100">
                            {{ $commissionedSystemsCount > 0 ? $commissionedSystemsCount : '34,686' }}
                        </div>
                    </div>
                    <div id="ableWaveChart1" style="width: 90px; height: 42px;"></div>
                </div>

                {{-- Block 2: Pending Tasks / Quotes --}}
                <div class="able-overview-divider" style="display: flex; align-items: center; justify-content: space-between; padding-right: 14px; border-right: 1px solid #e8ebee;" class="dark:border-slate-800">
                    <div>
                        <div style="font-size: 12px; color: #64748b;" class="dark:text-slate-400">Pending Tasks</div>
                        <div style="font-size: 20px; font-weight: 800; color: #1d2630; margin-top: 4px;" class="dark:text-slate-100">
                            {{ $pendingQuotationsCount > 0 ? $pendingQuotationsCount : '3,678' }}
                        </div>
                    </div>
                    <div id="ableWaveChart2" style="width: 90px; height: 42px;"></div>
                </div>

                {{-- Block 3: Add Invoice Button --}}
                <div style="display: flex; justify-content: flex-end;">
                    <a href="{{ \App\Filament\App\Resources\Invoices\InvoiceResource::getUrl('create') }}" class="able-btn-primary" style="padding: 11px 24px;">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>+ Add Invoice</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Right Company & Team Profile Card (4 Cols) --}}
        <div class="able-card able-col-4">
            <div class="able-card-header" style="margin-bottom: 12px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: #eef4fe; color: #4680ff; display: flex; align-items: center; justify-content: center; font-weight: 800;">
                        @if ($tenant?->getFilamentAvatarUrl())
                            <img src="{{ $tenant->getFilamentAvatarUrl() }}" style="width: 100%; height: 100%; border-radius: 10px; object-fit: contain;" alt="Logo">
                        @else
                            <svg style="width: 22px; height: 22px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        @endif
                    </div>
                    <div>
                        <div style="font-size: 13.5px; font-weight: 800; color: #1d2630; line-height: 1.2;" class="dark:text-slate-100">
                            {{ $tenant?->name ?? 'VERTEX HOME SOLUTIONS' }}
                        </div>
                        <div style="font-size: 11px; color: #64748b; margin-top: 2px;" class="dark:text-slate-400">
                            {{ '@' . ($tenant?->slug ? str_replace('-', '', $tenant->slug) : 'vertexhomeslk') }}
                        </div>
                    </div>
                </div>
                <button type="button" class="able-dots-btn">•••</button>
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 18px; padding-top: 14px; border-top: 1px solid #f1f5f9;" class="dark:border-slate-800">
                {{-- Overlapping Team Avatars --}}
                <div style="display: flex; align-items: center;">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #bfdbfe; border: 2px solid #ffffff; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: #1e40af; margin-right: -8px; z-index: 4;">
                        OP
                    </div>
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #bbf7d0; border: 2px solid #ffffff; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: #065f46; margin-right: -8px; z-index: 3;">
                        KJ
                    </div>
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #fed7aa; border: 2px solid #ffffff; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: #9a3412; margin-right: -8px; z-index: 2;">
                        AF
                    </div>
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #e2e8f0; border: 2px solid #ffffff; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 700; color: #475569; z-index: 1;">
                        +2
                    </div>
                </div>

                {{-- Circular Add Member Button --}}
                <a href="/app/{{ $tenant?->slug ?? 'vertex-homes' }}/team-members" style="width: 34px; height: 34px; border-radius: 50%; background: #4680ff; color: #ffffff; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 18px; line-height: 1; transition: transform 0.15s ease;" title="Add Team Member">
                    +
                </a>
            </div>
        </div>
    </div>

    {{-- ========================================================
         ROW 5: Transactions Tabbed Card (6 Cols) + Donut Breakdown (6 Cols)
         ======================================================== --}}
    <div class="able-grid-12">
        {{-- Left Card: Transactions Tabbed List (6 Cols) --}}
        <div class="able-card able-col-6" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <div class="able-card-header" style="margin-bottom: 8px;">
                    <h3 class="able-card-title">Transactions</h3>
                    <button type="button" class="able-dots-btn">•••</button>
                </div>

                {{-- Tab Headers --}}
                <div style="display: flex; gap: 8px; border-bottom: 1px solid #e8ebee; margin-bottom: 14px;" class="dark:border-slate-800">
                    <button type="button" class="able-tab-btn" :class="{ 'active': activeTab === 'all' }" @click="activeTab = 'all'">
                        All Transaction
                    </button>
                    <button type="button" class="able-tab-btn" :class="{ 'active': activeTab === 'income' }" @click="activeTab = 'income'">
                        Payments Received
                    </button>
                    <button type="button" class="able-tab-btn" :class="{ 'active': activeTab === 'expense' }" @click="activeTab = 'expense'">
                        Supplier Expenses
                    </button>
                </div>

                {{-- Transactions List --}}
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @foreach ($allTransactions as $txn)
                        <div
                            style="display: flex; align-items: center; justify-content: space-between; padding: 6px 0;"
                            x-show="activeTab === 'all' || activeTab === '{{ $txn['type'] }}'"
                        >
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 40px; height: 40px; border-radius: 10px; background: {{ $txn['type'] === 'income' ? '#ecfdf5' : '#feeceb' }}; color: {{ $txn['type'] === 'income' ? '#059669' : '#dc2626' }}; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px;">
                                    {{ $txn['initials'] }}
                                </div>
                                <div>
                                    <div style="font-size: 13.5px; font-weight: 700; color: #1d2630;" class="dark:text-slate-100">
                                        {{ $txn['title'] }}
                                    </div>
                                    <div style="font-size: 11px; color: #64748b;" class="dark:text-slate-400">
                                        {{ $txn['subtitle'] }} • {{ $txn['date'] }}
                                    </div>
                                </div>
                            </div>

                            <div style="text-align: right;">
                                <div style="font-size: 13px; font-weight: 800; color: {{ $txn['type'] === 'income' ? '#059669' : '#1d2630' }};" class="dark:text-slate-100">
                                    {{ $txn['type'] === 'income' ? '+' : '-' }}Rs. {{ number_format($txn['amount'], 2) }}
                                </div>
                                <div style="font-size: 10.5px; font-weight: 700; color: {{ $txn['type'] === 'income' ? '#059669' : '#dc2626' }};">
                                    {{ $txn['type'] === 'income' ? '↗ Verified' : '↘ Settlement' }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Bottom Actions --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 20px; padding-top: 14px; border-top: 1px solid #f1f5f9;" class="dark:border-slate-800">
                <a href="{{ \App\Filament\App\Resources\Invoices\InvoiceResource::getUrl('index') }}" class="able-btn-outline" style="width: 100%;">
                    Transaction History
                </a>
                <a href="{{ \App\Filament\App\Resources\Invoices\InvoiceResource::getUrl('create') }}" class="able-btn-primary" style="width: 100%;">
                    Create Invoice
                </a>
            </div>
        </div>

        {{-- Right Card: Total Income Donut Breakdown (6 Cols) --}}
        <div class="able-card able-col-6">
            <div class="able-card-header">
                <div>
                    <h3 class="able-card-title">Total Income & Financial Breakdown</h3>
                    <div style="font-size: 11.5px; color: #64748b; margin-top: 2px;" class="dark:text-slate-400">
                        Revenue vs. Material & Operational Allocation
                    </div>
                </div>
                <button type="button" class="able-dots-btn">•••</button>
            </div>

            {{-- Donut Chart Container --}}
            <div id="ableProDonutChart" style="min-height: 270px; display: flex; align-items: center; justify-content: center;"></div>

            {{-- 2x2 Metric Cards Grid --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 16px;">
                <div style="background: #f8f9fa; border-radius: 8px; padding: 10px 14px; border: 1px solid #eef1f4;" class="dark:bg-slate-900 dark:border-slate-800">
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 11px; color: #64748b;" class="dark:text-slate-400">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #4680ff;"></span>
                        <span>Total Revenue</span>
                    </div>
                    <div style="font-size: 13.5px; font-weight: 800; color: #1d2630; margin-top: 2px;" class="dark:text-slate-100">
                        Rs. {{ number_format($totalRevenue, 2) }}
                    </div>
                </div>

                <div style="background: #f8f9fa; border-radius: 8px; padding: 10px 14px; border: 1px solid #eef1f4;" class="dark:bg-slate-900 dark:border-slate-800">
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 11px; color: #64748b;" class="dark:text-slate-400">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #93c5fd;"></span>
                        <span>Net Owner Profit</span>
                    </div>
                    <div style="font-size: 13.5px; font-weight: 800; color: #2563eb; margin-top: 2px;">
                        Rs. {{ number_format($netProfit, 2) }}
                    </div>
                </div>

                <div style="background: #f8f9fa; border-radius: 8px; padding: 10px 14px; border: 1px solid #eef1f4;" class="dark:bg-slate-900 dark:border-slate-800">
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 11px; color: #64748b;" class="dark:text-slate-400">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #d97706;"></span>
                        <span>Material & Supplier Cost</span>
                    </div>
                    <div style="font-size: 13.5px; font-weight: 800; color: #1d2630; margin-top: 2px;" class="dark:text-slate-100">
                        Rs. {{ number_format($materialCost, 2) }}
                    </div>
                </div>

                <div style="background: #f8f9fa; border-radius: 8px; padding: 10px 14px; border: 1px solid #eef1f4;" class="dark:bg-slate-900 dark:border-slate-800">
                    <div style="display: flex; align-items: center; gap: 6px; font-size: 11px; color: #64748b;" class="dark:text-slate-400">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span>
                        <span>Commissions & Labour</span>
                    </div>
                    <div style="font-size: 13.5px; font-weight: 800; color: #1d2630; margin-top: 2px;" class="dark:text-slate-100">
                        Rs. {{ number_format($overheadAndCommissions, 2) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================
         ROW 6: Mixed Chart (7 Cols) + Radial Bar Chart (5 Cols)
         ======================================================== --}}
    <div class="able-grid-12">
        {{-- Left Card: Mixed Chart (7 Cols) --}}
        <div class="able-card able-col-7">
            <div class="able-card-header">
                <div>
                    <h3 class="able-card-title">Income & Cashflow Dynamics</h3>
                    <div style="font-size: 11.5px; color: #64748b; margin-top: 2px;" class="dark:text-slate-400">
                        Monthly Income vs Expenses with Net Operating Line
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 11px; color: #64748b;" class="dark:text-slate-400">2026 Fiscal</span>
                    <button type="button" class="able-dots-btn">•••</button>
                </div>
            </div>

            <div id="ableProMixedChart" style="min-height: 320px;"></div>
        </div>

        {{-- Right Card: Radial Bar Chart (5 Cols) --}}
        <div class="able-card able-col-5">
            <div class="able-card-header">
                <div>
                    <h3 class="able-card-title">Category Performance</h3>
                    <div style="font-size: 11.5px; color: #64748b; margin-top: 2px;" class="dark:text-slate-400">
                        Solar inventory deployment & volume share
                    </div>
                </div>
                <button type="button" class="able-dots-btn">•••</button>
            </div>

            <div id="ableProRadialChart" style="min-height: 320px; display: flex; align-items: center; justify-content: center;"></div>
        </div>
    </div>

    {{-- ========================================================
         Alpine.js ApexCharts Initialization Logic
         ======================================================== --}}
    <script>
        function ableProDashboard(config = {}) {
            return {
                activeTab: 'all',
                config: config,
                initDashboard() {
                    this.waitForApex(() => {
                        this.renderAreaChart();
                        this.renderWaveCharts();
                        this.renderDonutChart();
                        this.renderMixedChart();
                        this.renderRadialChart();
                    });
                },
                waitForApex(callback) {
                    if (window.ApexCharts) {
                        callback();
                    } else {
                        const check = setInterval(() => {
                            if (window.ApexCharts) {
                                clearInterval(check);
                                callback();
                            }
                        }, 50);
                    }
                },
                renderAreaChart() {
                    const isDark = document.documentElement.classList.contains('dark');
                    const revenueData = this.config.monthlyRevenue || [45000, 60000, 1450000, 120000, 85000, 95000, 110000, 90000, 80000, 1250000, 115000, 130000];
                    const options = {
                        chart: {
                            type: 'area',
                            height: 310,
                            toolbar: { show: false },
                            fontFamily: 'inherit',
                            background: 'transparent'
                        },
                        colors: ['#4680ff'],
                        stroke: { curve: 'smooth', width: 2.5 },
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.35,
                                opacityTo: 0.05,
                                stops: [0, 90, 100],
                                colorStops: [
                                    { offset: 0, color: '#4680ff', opacity: 0.35 },
                                    { offset: 100, color: '#4680ff', opacity: 0.02 }
                                ]
                            }
                        },
                        dataLabels: { enabled: false },
                        series: [{
                            name: 'Revenue (LKR)',
                            data: revenueData
                        }],
                        xaxis: {
                            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                            axisBorder: { show: false },
                            axisTicks: { show: false },
                            labels: {
                                style: { colors: isDark ? '#94a3b8' : '#64748b', fontSize: '11px' }
                            }
                        },
                        yaxis: {
                            labels: {
                                formatter: (val) => val >= 1000000 ? (val / 1000000).toFixed(1) + 'M' : (val >= 1000 ? (val / 1000).toFixed(0) + 'K' : val),
                                style: { colors: isDark ? '#94a3b8' : '#64748b', fontSize: '11px' }
                            }
                        },
                        grid: {
                            borderColor: isDark ? 'rgba(255, 255, 255, 0.07)' : '#f1f5f9',
                            strokeDashArray: 4,
                            xaxis: { lines: { show: false } },
                            yaxis: { lines: { show: true } }
                        },
                        tooltip: {
                            theme: isDark ? 'dark' : 'light',
                            y: { formatter: (val) => 'Rs. ' + Number(val).toLocaleString() }
                        }
                    };

                    const chartEl = document.querySelector('#ableProAreaChart');
                    if (chartEl) {
                        chartEl.innerHTML = '';
                        new ApexCharts(chartEl, options).render();
                    }
                },
                renderWaveCharts() {
                    const wave1 = {
                        chart: { type: 'line', height: 42, width: 90, sparkline: { enabled: true } },
                        series: [{ data: [12, 18, 14, 28, 20, 34, 26, 32] }],
                        stroke: { curve: 'smooth', width: 2.2, colors: ['#4680ff'] },
                        tooltip: { enabled: false }
                    };
                    const el1 = document.querySelector('#ableWaveChart1');
                    if (el1) { el1.innerHTML = ''; new ApexCharts(el1, wave1).render(); }

                    const wave2 = {
                        chart: { type: 'line', height: 42, width: 90, sparkline: { enabled: true } },
                        series: [{ data: [22, 14, 26, 18, 30, 24, 38, 28] }],
                        stroke: { curve: 'smooth', width: 2.2, colors: ['#ef4444'] },
                        tooltip: { enabled: false }
                    };
                    const el2 = document.querySelector('#ableWaveChart2');
                    if (el2) { el2.innerHTML = ''; new ApexCharts(el2, wave2).render(); }
                },
                renderDonutChart() {
                    const isDark = document.documentElement.classList.contains('dark');
                    const rev = this.config.totalRevenue || 1250000;
                    const mat = this.config.materialCost || 853799;
                    const prof = this.config.netProfit || 209701;
                    const comm = this.config.overheadAndCommissions || 186500;

                    const options = {
                        chart: {
                            type: 'donut',
                            height: 270,
                            fontFamily: 'inherit',
                            background: 'transparent'
                        },
                        colors: ['#4680ff', '#d97706', '#93c5fd', '#10b981'],
                        series: [rev, mat, prof, comm],
                        labels: ['Total Revenue', 'Material & Supplier Cost', 'Net Owner Profit', 'Commissions & Labour'],
                        legend: { show: false },
                        dataLabels: { enabled: false },
                        stroke: { colors: [isDark ? '#182230' : '#ffffff'], width: 3 },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '72%',
                                    labels: {
                                        show: true,
                                        name: { show: true, fontSize: '12px', color: isDark ? '#94a3b8' : '#64748b' },
                                        value: {
                                            show: true,
                                            fontSize: '17px',
                                            fontWeight: 800,
                                            color: isDark ? '#f8fafc' : '#1d2630',
                                            formatter: (val) => 'Rs. ' + (val / 1000).toFixed(0) + 'K'
                                        },
                                        total: {
                                            show: true,
                                            label: 'Net Profit',
                                            color: isDark ? '#94a3b8' : '#64748b',
                                            fontSize: '11px',
                                            fontWeight: 600,
                                            formatter: () => 'Rs. ' + (prof / 1000).toFixed(1) + 'K'
                                        }
                                    }
                                }
                            }
                        },
                        tooltip: {
                            theme: isDark ? 'dark' : 'light',
                            y: { formatter: (val) => 'Rs. ' + Number(val).toLocaleString() }
                        }
                    };

                    const chartEl = document.querySelector('#ableProDonutChart');
                    if (chartEl) {
                        chartEl.innerHTML = '';
                        new ApexCharts(chartEl, options).render();
                    }
                },
                renderMixedChart() {
                    const isDark = document.documentElement.classList.contains('dark');
                    const rev = this.config.monthlyRevenue || [45000, 60000, 1450000, 120000, 85000, 95000, 110000, 90000, 80000, 1250000, 115000, 130000];
                    const exp = this.config.monthlyExpenses || [30000, 42000, 1080000, 85000, 60000, 70000, 78000, 65000, 58000, 1040299, 80000, 90000];
                    const cf = this.config.monthlyCashflow || [15000, 18000, 370000, 35000, 25000, 25000, 32000, 25000, 22000, 209701, 35000, 40000];

                    const options = {
                        chart: {
                            height: 320,
                            type: 'line',
                            toolbar: { show: true },
                            fontFamily: 'inherit',
                            background: 'transparent'
                        },
                        colors: ['#4680ff', '#f59e0b', '#10b981'],
                        stroke: { width: [0, 0, 2.5], curve: 'smooth' },
                        plotOptions: {
                            bar: {
                                columnWidth: '38%',
                                borderRadius: 4
                            }
                        },
                        series: [
                            {
                                name: 'Income (LKR)',
                                type: 'column',
                                data: rev
                            },
                            {
                                name: 'Expenses (LKR)',
                                type: 'column',
                                data: exp
                            },
                            {
                                name: 'Net Cashflow (LKR)',
                                type: 'line',
                                data: cf
                            }
                        ],
                        xaxis: {
                            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                            axisBorder: { show: false },
                            axisTicks: { show: false },
                            labels: {
                                style: { colors: isDark ? '#94a3b8' : '#64748b', fontSize: '11px' }
                            }
                        },
                        yaxis: {
                            labels: {
                                formatter: (val) => val >= 1000000 ? (val / 1000000).toFixed(1) + 'M' : (val >= 1000 ? (val / 1000).toFixed(0) + 'K' : val),
                                style: { colors: isDark ? '#94a3b8' : '#64748b', fontSize: '11px' }
                            }
                        },
                        grid: {
                            borderColor: isDark ? 'rgba(255, 255, 255, 0.07)' : '#f1f5f9',
                            strokeDashArray: 4
                        },
                        tooltip: {
                            theme: isDark ? 'dark' : 'light',
                            y: { formatter: (val) => 'Rs. ' + Number(val).toLocaleString() }
                        }
                    };

                    const chartEl = document.querySelector('#ableProMixedChart');
                    if (chartEl) {
                        chartEl.innerHTML = '';
                        new ApexCharts(chartEl, options).render();
                    }
                },
                renderRadialChart() {
                    const isDark = document.documentElement.classList.contains('dark');
                    const options = {
                        chart: {
                            height: 320,
                            type: 'radialBar',
                            fontFamily: 'inherit',
                            background: 'transparent'
                        },
                        plotOptions: {
                            radialBar: {
                                startAngle: -135,
                                endAngle: 225,
                                hollow: { size: '32%' },
                                track: {
                                    background: isDark ? 'rgba(255, 255, 255, 0.08)' : '#f1f5f9',
                                    strokeWidth: '100%'
                                },
                                dataLabels: {
                                    name: { fontSize: '13px', color: isDark ? '#94a3b8' : '#64748b', offsetY: -8 },
                                    value: { fontSize: '18px', fontWeight: 800, color: isDark ? '#f8fafc' : '#1d2630', offsetY: 4 },
                                    total: {
                                        show: true,
                                        label: 'Solar PV',
                                        color: isDark ? '#94a3b8' : '#64748b',
                                        formatter: () => '85%'
                                    }
                                }
                            }
                        },
                        colors: ['#4680ff', '#38bdf8', '#10b981', '#f59e0b'],
                        series: [85, 68, 74, 92],
                        labels: ['Solar Panels', 'Inverters', 'Batteries', 'Services'],
                        stroke: { lineCap: 'round' }
                    };

                    const chartEl = document.querySelector('#ableProRadialChart');
                    if (chartEl) {
                        chartEl.innerHTML = '';
                        new ApexCharts(chartEl, options).render();
                    }
                }
            };
        }
    </script>
</div>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vertex Books — Smart Cloud Accounting, POS & Solar ERP for Sri Lankan Businesses</title>
    <meta name="description" content="Vertex Books is Sri Lanka's leading cloud ERP for Solar PV installers, retail businesses, and trade distributors. Featuring CEB solar sizing, milestone invoicing, 80mm POS, and serial tracking.">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
                    },
                    colors: {
                        emerald: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        },
                        amber: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .gradient-mesh {
            background-image: radial-gradient(at 10% 20%, rgba(16, 185, 129, 0.12) 0px, transparent 50%),
                              radial-gradient(at 90% 10%, rgba(245, 158, 11, 0.10) 0px, transparent 50%),
                              radial-gradient(at 50% 80%, rgba(16, 185, 129, 0.08) 0px, transparent 50%);
        }
        .hero-glow {
            box-shadow: 0 0 60px -15px rgba(16, 185, 129, 0.25);
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 antialiased selection:bg-emerald-500 selection:text-white">

    <!-- Navbar -->
    <header class="sticky top-0 z-50 backdrop-blur-md bg-slate-950/80 border-b border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand Logo -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xl font-extrabold tracking-tight text-white">Vertex Books</span>
                            <span class="text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-full">SaaS Sri Lanka</span>
                        </div>
                        <span class="text-xs text-slate-400 hidden sm:block">Accounting, POS & Solar ERP</span>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-300">
                    <a href="#features" class="hover:text-emerald-400 transition-colors">Features</a>
                    <a href="#solar" class="hover:text-emerald-400 transition-colors">Solar PV ERP</a>
                    <a href="#pos" class="hover:text-emerald-400 transition-colors">Counter POS</a>
                    <a href="#pricing" class="hover:text-emerald-400 transition-colors">Pricing in LKR</a>
                </nav>

                <!-- Auth Buttons -->
                <div class="flex items-center gap-3">
                    <a href="/app/login" class="px-4 py-2 text-sm font-semibold text-slate-300 hover:text-white hover:bg-slate-900 border border-slate-800 rounded-lg transition-all">
                        Log in
                    </a>
                    <a href="/app/register" class="px-4 py-2 text-sm font-bold text-slate-950 bg-gradient-to-r from-emerald-400 to-teal-400 hover:from-emerald-300 hover:to-teal-300 rounded-lg shadow-md shadow-emerald-500/20 transition-all flex items-center gap-1.5">
                        <span>Start Free Trial</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative pt-12 pb-24 overflow-hidden gradient-mesh">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-6">
                <!-- Pill Tag -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-semibold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    14-Day Free Trial • No Credit Card Required
                </div>

                <!-- Main Heading -->
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-tight">
                    Smart Cloud Accounting, POS & <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-teal-300 to-amber-300">Solar ERP</span> for Sri Lanka
                </h1>

                <!-- Subtitle -->
                <p class="text-lg sm:text-xl text-slate-300 font-normal leading-relaxed">
                    Designed specifically for Sri Lankan business owners, solar EPC contractors, and retail hardware merchants. Complete with CEB Net-Metering sizing, 3-stage milestone billing, 80mm thermal POS, and serial number warranty tracking.
                </p>

                <!-- CTA Actions -->
                <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="/app/register" class="w-full sm:w-auto px-8 py-4 rounded-xl text-base font-bold text-slate-950 bg-gradient-to-r from-emerald-400 to-teal-400 hover:from-emerald-300 hover:to-teal-300 shadow-xl shadow-emerald-500/30 transition-all flex items-center justify-center gap-2">
                        <span>Get Started with 14 Days Free</span>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                    <a href="/app/login" class="w-full sm:w-auto px-8 py-4 rounded-xl text-base font-semibold text-slate-200 bg-slate-900/80 hover:bg-slate-800 border border-slate-700/80 transition-all">
                        Sign In to Workspace
                    </a>
                </div>

                <!-- Key Badges -->
                <div class="pt-6 flex flex-wrap items-center justify-center gap-6 text-xs text-slate-400 font-medium">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        <span>CEB & LECO Solar Ready</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        <span>80mm ESC/POS Thermal Printing</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        <span>LKR Native Currency & VAT Compliant</span>
                    </div>
                </div>
            </div>

            <!-- Hero Mockup Display -->
            <div class="mt-16 relative max-w-5xl mx-auto rounded-2xl bg-gradient-to-b from-slate-800/70 to-slate-900/90 p-3 sm:p-4 border border-slate-700/60 hero-glow">
                <div class="rounded-xl overflow-hidden bg-slate-950 border border-slate-800 shadow-2xl">
                    <!-- Top Window Bar -->
                    <div class="flex items-center justify-between px-4 py-3 bg-slate-900 border-b border-slate-800">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-red-500/80"></span>
                            <span class="w-3 h-3 rounded-full bg-yellow-500/80"></span>
                            <span class="w-3 h-3 rounded-full bg-emerald-500/80"></span>
                            <span class="ml-2 text-xs font-mono text-slate-400">app.vertexbooks.lk • Lanka Solar Solutions</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs bg-emerald-500/20 text-emerald-400 px-2 py-0.5 rounded border border-emerald-500/30 font-medium">14-Day Pro Trial Active</span>
                        </div>
                    </div>

                    <!-- App UI Snapshot -->
                    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6 bg-slate-950/60">
                        <!-- Stat 1 -->
                        <div class="bg-slate-900/90 p-5 rounded-xl border border-slate-800 space-y-2">
                            <div class="flex items-center justify-between text-xs text-slate-400">
                                <span>Solar PV Installed</span>
                                <span class="text-emerald-400 font-bold">+18.5 kWp</span>
                            </div>
                            <div class="text-2xl font-black text-white">42.8 kWp</div>
                            <div class="text-[11px] text-slate-500">Tier 1 Bifacial Modules & Hybrid Inverters</div>
                        </div>

                        <!-- Stat 2 -->
                        <div class="bg-slate-900/90 p-5 rounded-xl border border-slate-800 space-y-2">
                            <div class="flex items-center justify-between text-xs text-slate-400">
                                <span>Monthly Invoiced</span>
                                <span class="text-emerald-400 font-bold">LKR</span>
                            </div>
                            <div class="text-2xl font-black text-emerald-400">Rs. 4,850,000</div>
                            <div class="text-[11px] text-slate-500">Milestone Invoices & POS Receipts</div>
                        </div>

                        <!-- Stat 3 -->
                        <div class="bg-slate-900/90 p-5 rounded-xl border border-slate-800 space-y-2">
                            <div class="flex items-center justify-between text-xs text-slate-400">
                                <span>Active Warranties Tracked</span>
                                <span class="text-amber-400 font-bold">100% Up to Date</span>
                            </div>
                            <div class="text-2xl font-black text-white">128 Serials</div>
                            <div class="text-[11px] text-slate-500">Huawei, Deye & Jinko Serial Vault</div>
                        </div>
                    </div>

                    <!-- Mini Highlight Grid -->
                    <div class="px-6 pb-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-slate-900/50 p-4 rounded-xl border border-slate-800/80">
                            <div class="text-xs font-bold text-emerald-400 uppercase tracking-wider mb-2">Solar PV Quotation Generator</div>
                            <div class="text-xs text-slate-300">
                                Sizing: <strong>10.53 kWp DC Three-Phase</strong> • Panels: <strong>18x Jinko 585W</strong> • Inverter: <strong>Huawei SUN2000-10KTL</strong>
                            </div>
                            <div class="mt-2 text-[11px] text-slate-400 flex items-center justify-between">
                                <span>Automated Milestone: 50% Adv | 40% Del | 10% Grid</span>
                                <span class="font-bold text-white">Rs. 1,300,000</span>
                            </div>
                        </div>

                        <div class="bg-slate-900/50 p-4 rounded-xl border border-slate-800/80">
                            <div class="text-xs font-bold text-teal-400 uppercase tracking-wider mb-2">Counter POS Terminal</div>
                            <div class="text-xs text-slate-300">
                                Instant Barcode Lookup • 80mm ESC/POS Thermal Receipt • Cash/Card/Bank Tender
                            </div>
                            <div class="mt-2 text-[11px] text-slate-400 flex items-center justify-between">
                                <span>Auto-deducts physical stock in real time</span>
                                <span class="text-emerald-400 font-bold">Instant Print</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Grid -->
    <section id="features" class="py-24 bg-slate-900/50 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <h2 class="text-xs font-bold uppercase tracking-widest text-emerald-400">Engineered for Real Sri Lankan Workflows</h2>
                <h3 class="text-3xl sm:text-4xl font-extrabold text-white">Everything Your Business Needs in One Platform</h3>
                <p class="text-slate-400 text-base">Replace fragmented spreadsheets, clunky accounting tools, and manual receipts with an integrated cloud platform built for growth.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div id="solar" class="bg-slate-950 p-8 rounded-2xl border border-slate-800 hover:border-emerald-500/50 transition-all space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-white">Solar PV Sizing & Quotations</h4>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Calculate system capacity in kWp, configure Single or Three-Phase inverters, select tile or corrugated roof mounting, and generate branded PDF quotes with CEB account references in seconds.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-slate-950 p-8 rounded-2xl border border-slate-800 hover:border-emerald-500/50 transition-all space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-white">3-Stage Milestone Invoicing</h4>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Convert accepted quotes into milestone invoices with automatic 50% Advance, 40% Delivery, and 10% Commissioning schedules. Track partial bank transfers and outstanding balances effortlessly.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div id="pos" class="bg-slate-950 p-8 rounded-2xl border border-slate-800 hover:border-emerald-500/50 transition-all space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-teal-500/10 border border-teal-500/30 flex items-center justify-center text-teal-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-white">Fast 80mm Counter POS</h4>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Lightning-fast checkout for retail counters, electrical suppliers, and hardware stores. Full barcode scanning, cash/card change calculations, and 80mm ESC/POS thermal receipt printing.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-slate-950 p-8 rounded-2xl border border-slate-800 hover:border-emerald-500/50 transition-all space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/30 flex items-center justify-center text-blue-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-white">Serial & Warranty Vault</h4>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Track inverters, batteries, and hardware components by individual serial number. Instantly verify warranty status and expiration dates when a customer calls with an inquiry.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-slate-950 p-8 rounded-2xl border border-slate-800 hover:border-emerald-500/50 transition-all space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-purple-500/10 border border-purple-500/30 flex items-center justify-center text-purple-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-white">Live P&L & Cashflow Reporting</h4>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Monitor revenue, cost of goods sold, and operating overheads in real time. Know your exact net profit margin and project cashflow without waiting for month-end accounting.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-slate-950 p-8 rounded-2xl border border-slate-800 hover:border-emerald-500/50 transition-all space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-rose-500/10 border border-rose-500/30 flex items-center justify-center text-rose-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-white">Tenant Security & Roles</h4>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Granular roles for Business Owner, Administrator, and Cashier. Protect sensitive financial margins while allowing cashiers fast counter POS access, fully backed by immutable audit logs.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-24 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <h2 class="text-xs font-bold uppercase tracking-widest text-emerald-400">Simple, Transparent Pricing in Sri Lankan Rupees</h2>
                <h3 class="text-3xl sm:text-4xl font-extrabold text-white">Choose the Right Plan for Your Business</h3>
                <p class="text-slate-400 text-base">All plans include our 14-day full-featured free trial. Upgrade, downgrade, or cancel anytime.</p>

                <!-- Billing Cycle Toggle -->
                <div class="pt-6 flex items-center justify-center gap-3">
                    <span class="text-sm font-semibold text-slate-300" id="monthly-label">Monthly Billing</span>
                    <button id="cycle-toggle" type="button" class="relative inline-flex h-7 w-14 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-slate-800 transition-colors duration-200 ease-in-out focus:outline-none" role="switch" aria-checked="false">
                        <span id="cycle-knob" class="translate-x-0 pointer-events-none inline-block h-6 w-6 transform rounded-full bg-emerald-400 shadow ring-0 transition duration-200 ease-in-out"></span>
                    </button>
                    <span class="text-sm font-semibold text-slate-300 flex items-center gap-1.5" id="yearly-label">
                        <span>Annual Billing</span>
                        <span class="text-[10px] uppercase font-bold tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 px-2 py-0.5 rounded-full">Save ~17%</span>
                    </span>
                </div>
            </div>

            <!-- Dynamic Plans Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch max-w-6xl mx-auto">
                @forelse($plans as $plan)
                    @php
                        $isPopular = strtolower($plan->slug) === 'silver' || strtolower($plan->slug) === 'gold';
                    @endphp
                    <div class="relative bg-slate-900/90 rounded-2xl border {{ $isPopular ? 'border-emerald-500 shadow-2xl shadow-emerald-500/10' : 'border-slate-800' }} p-8 flex flex-col justify-between transition-all hover:border-emerald-500/70">
                        @if($isPopular)
                            <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-gradient-to-r from-emerald-500 to-teal-400 text-slate-950 font-extrabold text-xs uppercase tracking-wider px-3.5 py-1 rounded-full shadow-md">
                                Most Popular
                            </div>
                        @endif

                        <div class="space-y-6">
                            <div>
                                <h4 class="text-2xl font-bold text-white">{{ $plan->name }}</h4>
                                <p class="text-xs text-slate-400 mt-1">
                                    {{ $plan->max_users }} {{ Str::plural('Team Member', $plan->max_users) }} • Up to {{ number_format($plan->max_invoices) }} Invoices/mo
                                </p>
                            </div>

                            <!-- Price Display -->
                            <div class="border-y border-slate-800/80 py-4">
                                <div class="monthly-price block">
                                    <span class="text-3xl sm:text-4xl font-extrabold text-white">Rs. {{ number_format($plan->price_monthly, 0) }}</span>
                                    <span class="text-sm font-medium text-slate-400">/ month</span>
                                </div>
                                <div class="yearly-price hidden">
                                    <span class="text-3xl sm:text-4xl font-extrabold text-emerald-400">Rs. {{ number_format($plan->price_yearly, 0) }}</span>
                                    <span class="text-sm font-medium text-slate-400">/ year</span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-1">Includes 14-day free trial</p>
                            </div>

                            <!-- Features List -->
                            <div class="space-y-3 text-sm text-slate-300">
                                <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">What's Included:</div>
                                @if(!empty($plan->features) && is_array($plan->features))
                                    @foreach($plan->features as $feature)
                                        <div class="flex items-start gap-2.5">
                                            <svg class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                            <span class="text-xs capitalize">{{ str_replace('_', ' ', $feature) }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="flex items-center gap-2 text-xs">
                                        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        <span>Full ERP & Invoicing Engine</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- CTA -->
                        <div class="pt-8">
                            <a href="/app/register" class="w-full block text-center py-3 px-4 rounded-xl text-sm font-bold {{ $isPopular ? 'bg-gradient-to-r from-emerald-400 to-teal-400 hover:from-emerald-300 hover:to-teal-300 text-slate-950 shadow-lg shadow-emerald-500/20' : 'bg-slate-800 hover:bg-slate-700 text-white' }} transition-all">
                                Start 14-Day Free Trial
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12 text-slate-400">
                        <p>No active plans configured. Please check database seeders.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Sri Lanka Trust Banner -->
    <section class="py-16 bg-gradient-to-r from-slate-900 via-slate-950 to-slate-900 border-y border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
            <h3 class="text-2xl sm:text-3xl font-extrabold text-white">Built in Sri Lanka for Sri Lankan Businesses</h3>
            <p class="max-w-2xl mx-auto text-sm text-slate-400">
                Local bank transfer reconciliation (BOC, Commercial Bank, Sampath, HNB), CEB Net-Plus tariff calculations, and fast support from our Colombo engineering team.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-8 pt-4 text-xs font-semibold text-slate-400">
                <span class="flex items-center gap-2">🇱🇰 Colombo Headquarters</span>
                <span class="flex items-center gap-2">⚡ CEB & LECO Verified Workflows</span>
                <span class="flex items-center gap-2">🔒 Isolated Multi-Tenant Database</span>
                <span class="flex items-center gap-2">🧾 Inland Revenue TIN Ready</span>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-950 border-t border-slate-800/80 pt-16 pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
                <!-- Col 1 -->
                <div class="space-y-4 md:col-span-2">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center text-slate-950 font-black text-lg">
                            V
                        </div>
                        <span class="text-xl font-bold text-white tracking-tight">Vertex Books</span>
                    </div>
                    <p class="text-xs text-slate-400 max-w-md leading-relaxed">
                        A product of <strong>Vertex Innovation LK</strong> (vertexinnovationlk.com). Empowering solar engineers, hardware retailers, and commercial enterprises across Sri Lanka with modern cloud business management tools.
                    </p>
                    <div class="text-xs text-slate-400">
                        Email: <a href="mailto:info@vertexinnovationlk.com" class="text-emerald-400 hover:underline">info@vertexinnovationlk.com</a><br>
                        Web: <a href="https://vertexinnovationlk.com" target="_blank" class="text-emerald-400 hover:underline">vertexinnovationlk.com</a>
                    </div>
                </div>

                <!-- Col 2 -->
                <div class="space-y-3">
                    <h5 class="text-xs font-bold uppercase tracking-wider text-slate-300">Quick Links</h5>
                    <ul class="space-y-2 text-xs text-slate-400">
                        <li><a href="/app/login" class="hover:text-emerald-400 transition-colors">Workspace Login</a></li>
                        <li><a href="/app/register" class="hover:text-emerald-400 transition-colors">Register 14-Day Trial</a></li>
                        <li><a href="#features" class="hover:text-emerald-400 transition-colors">Core Features</a></li>
                        <li><a href="#pricing" class="hover:text-emerald-400 transition-colors">Pricing in LKR</a></li>
                    </ul>
                </div>

                <!-- Col 3 -->
                <div class="space-y-3">
                    <h5 class="text-xs font-bold uppercase tracking-wider text-slate-300">Modules</h5>
                    <ul class="space-y-2 text-xs text-slate-400">
                        <li>Solar PV Quotation Designer</li>
                        <li>Milestone Payment Billing</li>
                        <li>80mm Thermal Receipt POS</li>
                        <li>Product Serial & Warranty Vault</li>
                        <li>Real-time Financial P&L</li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-slate-900 pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 gap-4">
                <p>&copy; {{ date('Y') }} Vertex Innovation LK. All rights reserved.</p>
                <p>Designed and hosted for Sri Lankan businesses.</p>
            </div>
        </div>
    </footer>

    <!-- Interactive Pricing Cycle Script -->
    <script>
        const cycleToggle = document.getElementById('cycle-toggle');
        const cycleKnob = document.getElementById('cycle-knob');
        let isYearly = false;

        if (cycleToggle) {
            cycleToggle.addEventListener('click', () => {
                isYearly = !isYearly;
                cycleToggle.setAttribute('aria-checked', isYearly.toString());

                if (isYearly) {
                    cycleKnob.classList.remove('translate-x-0');
                    cycleKnob.classList.add('translate-x-7');
                    cycleToggle.classList.remove('bg-slate-800');
                    cycleToggle.classList.add('bg-emerald-600');
                    document.querySelectorAll('.monthly-price').forEach(el => el.classList.add('hidden'));
                    document.querySelectorAll('.yearly-price').forEach(el => el.classList.remove('hidden'));
                } else {
                    cycleKnob.classList.remove('translate-x-7');
                    cycleKnob.classList.add('translate-x-0');
                    cycleToggle.classList.remove('bg-emerald-600');
                    cycleToggle.classList.add('bg-slate-800');
                    document.querySelectorAll('.monthly-price').forEach(el => el.classList.remove('hidden'));
                    document.querySelectorAll('.yearly-price').forEach(el => el.classList.add('hidden'));
                }
            });
        }
    </script>
</body>
</html>

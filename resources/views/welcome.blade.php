@php
    $plans = \App\Models\Plan::where('is_active', true)->orderBy('price_monthly')->get();
    if ($plans->isEmpty()) {
        $plans = collect([
            (object) [
                'id' => 1,
                'name' => 'Starter Solar & POS',
                'slug' => 'starter',
                'price_monthly' => 3500.00,
                'price_yearly' => 35000.00,
                'max_users' => 2,
                'max_invoices' => 50,
                'features' => ['CEB & LECO Solar PV Sizing', 'Zoho-Style Milestone Invoicing', '80mm Thermal Receipt POS', 'PDF Agreements & Gate Passes', 'Email & WhatsApp Sharing'],
            ],
            (object) [
                'id' => 2,
                'name' => 'Professional EPC ERP',
                'slug' => 'professional',
                'price_monthly' => 7500.00,
                'price_yearly' => 75000.00,
                'max_users' => 5,
                'max_invoices' => 500,
                'features' => ['Everything in Starter', 'Multi-User Role Permissions', 'Real-Time Project Profitability', 'Warranty Serial Number Vault', 'Supplier COGS & Commission Payouts', 'Official Completion Certificates'],
            ],
            (object) [
                'id' => 3,
                'name' => 'Enterprise Unlimited',
                'slug' => 'enterprise',
                'price_monthly' => 14500.00,
                'price_yearly' => 145000.00,
                'max_users' => 999,
                'max_invoices' => 999999,
                'features' => ['Unlimited Staff & Branches', 'Custom Domain & Branding', 'Dedicated Account Manager', 'Multi-Warehouse Inventory', 'Automated Daily Cloud Backups', 'Priority SLA 24/7 Phone Support'],
            ],
        ]);
    }
@endphp
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vertex Books — Executive Cloud Accounting, POS & Solar ERP for Sri Lanka</title>
    <meta name="description" content="Vertex Books is Sri Lanka's leading cloud ERP for Solar EPC installers, hardware merchants, and trade distributors. Features CEB solar sizing, milestone invoicing, POS counter receipts, and project profitability.">

    <!-- Google Fonts: Public Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Public Sans"', 'Inter', '-apple-system', 'sans-serif'],
                    },
                    colors: {
                        able: {
                            blue: '#4680ff',
                            hover: '#3874f6',
                            canvas: '#f3f5f8',
                            dark: '#1d2630',
                            slate: '#5b6b79',
                            border: '#e8ebee',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Public Sans', sans-serif;
            background-color: #f3f5f8;
            color: #1d2630;
        }

        .able-card {
            background: #ffffff;
            border: 1px solid #e8ebee;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(33, 40, 50, 0.05);
            transition: all 0.2s ease;
        }

        .able-card:hover {
            transform: translateY(-2px);
            border-color: #4680ff;
            box-shadow: 0 8px 24px rgba(70, 128, 255, 0.12);
        }

        .hero-banner-gradient {
            background: linear-gradient(135deg, #4680ff 0%, #2563eb 100%);
        }

        .btn-primary {
            background-color: #4680ff;
            color: #ffffff;
            font-weight: 700;
            border-radius: 8px;
            transition: all 0.15s ease;
        }

        .btn-primary:hover {
            background-color: #3874f6;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(70, 128, 255, 0.35);
        }

        .btn-outline {
            background-color: #ffffff;
            color: #334155;
            border: 1px solid #cbd5e1;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.15s ease;
        }

        .btn-outline:hover {
            background-color: #f8fafc;
            border-color: #94a3b8;
        }

        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #e8ebee;
        }
    </style>
</head>
<body class="antialiased selection:bg-blue-500 selection:text-white">

    <!-- 1. Sleek Sticky Glass Navbar -->
    <header class="sticky top-0 z-50 glass-nav">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand Logo -->
                <a href="/" class="flex items-center gap-3 text-decoration-none">
                    <div class="w-10 h-10 rounded-xl bg-[#4680ff] flex items-center justify-center text-white font-black text-xl shadow-md shadow-blue-500/20">
                        VB
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xl font-extrabold tracking-tight text-[#1d2630]">Vertex Books</span>
                            <span class="text-[10.5px] font-bold uppercase tracking-wider bg-[#eef4fe] text-[#4680ff] border border-blue-200 px-2 py-0.5 rounded-full">ERP Sri Lanka</span>
                        </div>
                        <span class="text-xs text-[#5b6b79] hidden sm:block">Accounting, POS & Solar ERP</span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-[#5b6b79]">
                    <a href="#features" class="hover:text-[#4680ff] transition-colors">Features</a>
                    <a href="#solar-erp" class="hover:text-[#4680ff] transition-colors">Solar PV ERP</a>
                    <a href="#pos" class="hover:text-[#4680ff] transition-colors">Counter POS</a>
                    <a href="#pricing" class="hover:text-[#4680ff] transition-colors">Pricing in LKR</a>
                </nav>

                <!-- Auth Buttons -->
                <div class="flex items-center gap-3">
                    <a href="/app/login" class="px-4 py-2 text-sm font-semibold text-[#334155] hover:text-[#1d2630] transition-colors">
                        Sign In
                    </a>
                    <a href="/app/register" class="btn-primary px-5 py-2.5 text-sm flex items-center gap-2 shadow-sm">
                        <span>Start Free Trial</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- 2. Hero Section with Able Pro Royal Blue Mockup -->
    <section class="pt-12 pb-20 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-6">
                <!-- Pill Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#eef4fe] border border-blue-200 text-[#4680ff] text-xs font-bold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-[#4680ff] animate-ping"></span>
                    14-Day Free Trial • No Credit Card Required
                </div>

                <!-- Main Heading -->
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-[#1d2630] leading-tight">
                    Smart Cloud Accounting, POS & <span class="text-[#4680ff]">Solar ERP</span> for Sri Lanka
                </h1>

                <!-- Subtitle -->
                <p class="text-base sm:text-lg text-[#5b6b79] leading-relaxed max-w-2xl mx-auto">
                    Built specifically for Sri Lankan solar EPC contractors, electrical trade distributors, and retail hardware merchants. Featuring CEB solar sizing, Zoho-style milestone invoicing, 80mm POS receipts, and real-time project profitability.
                </p>

                <!-- CTA Actions -->
                <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="/app/register" class="w-full sm:w-auto btn-primary px-8 py-3.5 text-base flex items-center justify-center gap-2">
                        <span>Get Started with 14 Days Free</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                    <a href="/app/login" class="w-full sm:w-auto btn-outline px-7 py-3.5 text-base text-center">
                        Sign In to Workspace
                    </a>
                </div>

                <!-- Trust Badges -->
                <div class="pt-4 flex flex-wrap items-center justify-center gap-6 text-xs text-[#64748b] font-medium">
                    <div class="flex items-center gap-1.5">
                        <span class="text-[#10b981]">✓</span>
                        <span>CEB & LECO Solar Ready</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-[#10b981]">✓</span>
                        <span>80mm ESC/POS Thermal Printing</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-[#10b981]">✓</span>
                        <span>LKR Native Currency & VAT Compliant</span>
                    </div>
                </div>
            </div>

            <!-- Able Pro Interactive Dashboard Mockup Preview -->
            <div class="mt-14 max-w-5xl mx-auto rounded-2xl bg-white p-3 sm:p-5 border border-[#e8ebee] shadow-2xl shadow-blue-500/10">
                <!-- Mockup Window Bar -->
                <div class="flex items-center justify-between pb-4 border-b border-[#e8ebee] mb-5">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-red-400"></span>
                        <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                        <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                        <span class="ml-2 text-xs font-mono text-[#64748b]">erp.vertexhomeslk.com • Lanka Solar EPC Solutions</span>
                    </div>
                    <span class="text-xs bg-[#ecfdf5] text-[#059669] font-bold px-2.5 py-0.5 rounded-full border border-emerald-200">
                        ● Live Multi-Tenant ERP
                    </span>
                </div>

                <!-- Mockup Row 1: Able Pro Hero Banner -->
                <div class="hero-banner-gradient rounded-xl p-5 text-white flex items-center justify-between shadow-md mb-5">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-wider text-blue-100 mb-1">Executive Workspace</div>
                        <h3 class="text-xl font-bold">Explore Redesigned Vertex Books ERP</h3>
                        <p class="text-xs text-blue-100 mt-1 max-w-lg">Solar PV system sizing, milestone billing, inventory and project profitability in one unified workspace.</p>
                    </div>
                    <div class="hidden sm:block">
                        <span class="bg-white/20 border border-white/60 text-white text-xs font-bold px-3 py-1.5 rounded-full">+ Create Quotation</span>
                    </div>
                </div>

                <!-- Mockup Row 2: 4 Able Pro Stat Cards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-left">
                    <div class="bg-[#f8f9fa] border border-[#e8ebee] rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-[#5b6b79]">All Earnings</span>
                            <span class="w-6 h-6 rounded-md bg-[#eef4fe] text-[#4680ff] flex items-center justify-center text-xs">💰</span>
                        </div>
                        <div class="text-lg font-black text-[#1d2630]">Rs. 1.25M</div>
                        <div class="text-[11px] text-[#4680ff] font-bold mt-1">↗ 30.6% this month</div>
                    </div>

                    <div class="bg-[#f8f9fa] border border-[#e8ebee] rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-[#5b6b79]">Quotations</span>
                            <span class="w-6 h-6 rounded-md bg-[#fff8ec] text-[#f59e0b] flex items-center justify-center text-xs">📄</span>
                        </div>
                        <div class="text-lg font-black text-[#1d2630]">290+</div>
                        <div class="text-[11px] text-[#d97706] font-bold mt-1">↗ 22.4% proposals</div>
                    </div>

                    <div class="bg-[#f8f9fa] border border-[#e8ebee] rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-[#5b6b79]">Total Projects</span>
                            <span class="w-6 h-6 rounded-md bg-[#e8fbf5] text-[#10b981] flex items-center justify-center text-xs">📊</span>
                        </div>
                        <div class="text-lg font-black text-[#1d2630]">1,468</div>
                        <div class="text-[11px] text-[#10b981] font-bold mt-1">↗ 30.6% completed</div>
                    </div>

                    <div class="bg-[#f8f9fa] border border-[#e8ebee] rounded-xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-[#5b6b79]">Net Profit</span>
                            <span class="w-6 h-6 rounded-md bg-[#eef4fe] text-[#4680ff] flex items-center justify-center text-xs">📈</span>
                        </div>
                        <div class="text-lg font-black text-[#059669]">Rs. 209.7K</div>
                        <div class="text-[11px] text-[#059669] font-bold mt-1">+16.8% margin</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Modular Feature Cards (Able Pro Design Anatomy) -->
    <section id="features" class="py-20 bg-white border-y border-[#e8ebee]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-[#4680ff] bg-[#eef4fe] px-3 py-1 rounded-full border border-blue-200">
                    Engineered for Sri Lanka
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-[#1d2630] mt-3">
                    Everything Your Business Needs in One Cloud Workspace
                </h2>
                <p class="text-sm text-[#5b6b79] mt-2">
                    Eliminate fragmented spreadsheets and outdated accounting software with our specialized multi-tenant platform.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Feature 1 -->
                <div class="able-card p-6">
                    <div class="w-12 h-12 rounded-xl bg-[#fff8ec] text-[#f59e0b] flex items-center justify-center text-2xl mb-4">
                        ☀️
                    </div>
                    <h3 class="text-base font-bold text-[#1d2630]">Solar PV Sizing & Sizing Agreements</h3>
                    <p class="text-xs text-[#5b6b79] mt-2 leading-relaxed">
                        Automatic kWp sizing based on CEB electricity units, single/three-phase configuration, module counts, and 1-click legal solar agreement PDF generation.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="able-card p-6">
                    <div class="w-12 h-12 rounded-xl bg-[#eef4fe] text-[#4680ff] flex items-center justify-center text-2xl mb-4">
                        📑
                    </div>
                    <h3 class="text-base font-bold text-[#1d2630]">Zoho-Style Milestone Invoicing</h3>
                    <p class="text-xs text-[#5b6b79] mt-2 leading-relaxed">
                        Dual split-pane view with real-time A4 PDF preview. Bill projects in 3 custom milestones (Advance, Delivery, Commissioning) with WhatsApp and Email sharing.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="able-card p-6">
                    <div class="w-12 h-12 rounded-xl bg-[#e8fbf5] text-[#10b981] flex items-center justify-center text-2xl mb-4">
                        🖥️
                    </div>
                    <h3 class="text-base font-bold text-[#1d2630]">80mm Thermal Receipt POS</h3>
                    <p class="text-xs text-[#5b6b79] mt-2 leading-relaxed">
                        High-speed counter checkout terminal for retail hardware and counter sales. Supports ESC/POS 80mm thermal receipt printers and barcode barcode scanners.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="able-card p-6">
                    <div class="w-12 h-12 rounded-xl bg-[#f5f3ff] text-[#8b5cf6] flex items-center justify-center text-2xl mb-4">
                        📊
                    </div>
                    <h3 class="text-base font-bold text-[#1d2630]">Project Profitability & Costing</h3>
                    <p class="text-xs text-[#5b6b79] mt-2 leading-relaxed">
                        Real-time tracking of item cost prices (COGS), supplier equipment bills, technician installation charges, and sales agent commission vouchers.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Solar EPC Specialization & Security Section -->
    <section id="solar-erp" class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
            <!-- Row 1: Solar EPC Deep Dive -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-5">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#059669] bg-[#ecfdf5] px-3 py-1 rounded-full border border-emerald-200">
                        Solar EPC Workflow
                    </span>
                    <h2 class="text-3xl font-extrabold text-[#1d2630] leading-snug">
                        Official Commissioning Certificates & Warranty Serial Tracking
                    </h2>
                    <p class="text-sm text-[#5b6b79] leading-relaxed">
                        Never lose track of serial numbers or warranty periods for high-value solar components. Issue official, executive Certificate of Completion documents in A4 landscape/portrait format for CEB grid-connection approvals.
                    </p>
                    <ul class="space-y-2.5 text-xs text-[#334155] font-semibold">
                        <li class="flex items-center gap-2">
                            <span class="text-[#10b981]">✓</span>
                            <span>A4 & A5 Official Completion Certificate Generator with live canvas preview</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-[#10b981]">✓</span>
                            <span>Track panel and inverter serial numbers directly to customer invoices</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-[#10b981]">✓</span>
                            <span>1-Page A5 Commission Payout Vouchers for freelance sales promoters</span>
                        </li>
                    </ul>
                </div>
                <div class="able-card p-6">
                    <div class="border-2 border-dashed border-[#cbd5e1] rounded-xl p-6 text-center space-y-3 bg-[#f8f9fa]">
                        <div class="text-xs font-bold text-[#0d5c75] uppercase tracking-wider">CERTIFICATE OF COMPLETION</div>
                        <div class="text-lg font-serif font-black text-[#0d5c75]">SOLAR POWER SYSTEM COMMISSIONING</div>
                        <div class="text-xs text-[#64748b]">Issued to Customer: <strong>MR. M A N A M FERNANDO</strong></div>
                        <div class="inline-block bg-[#0d5c75] text-white text-[11px] font-bold px-3 py-1 rounded">
                            05 kW+05kW Battery Off-Grid Solar System
                        </div>
                        <div class="text-[11px] text-[#64748b] pt-2">CEB Net-Metering Grid Connection Specification Verified</div>
                    </div>
                </div>
            </div>

            <!-- Row 2: Role Permissions & Document Immutability -->
            <div id="pos" class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="able-card p-6 order-2 lg:order-1">
                    <div class="space-y-3 bg-[#f8f9fa] border border-[#e8ebee] rounded-xl p-5">
                        <div class="flex items-center justify-between text-xs pb-3 border-b border-[#e8ebee]">
                            <span class="font-bold text-[#1d2630]">Tenant Security & Role Hierarchy</span>
                            <span class="text-[#4680ff] font-bold">Strict Audit Log</span>
                        </div>
                        <div class="space-y-2 text-xs">
                            <div class="flex items-center justify-between p-2 rounded bg-white border border-[#e8ebee]">
                                <span class="font-semibold text-[#1d2630]">Company Owner & Admin</span>
                                <span class="bg-[#eef4fe] text-[#4680ff] font-bold px-2 py-0.5 rounded">Full P&L Reports & Edit</span>
                            </div>
                            <div class="flex items-center justify-between p-2 rounded bg-white border border-[#e8ebee]">
                                <span class="font-semibold text-[#1d2630]">Sales Executive</span>
                                <span class="bg-[#fff8ec] text-[#d97706] font-bold px-2 py-0.5 rounded">Create Quotes (Immutable)</span>
                            </div>
                            <div class="flex items-center justify-between p-2 rounded bg-white border border-[#e8ebee]">
                                <span class="font-semibold text-[#1d2630]">Cashier / Counter POS</span>
                                <span class="bg-[#e8fbf5] text-[#10b981] font-bold px-2 py-0.5 rounded">POS Receipts (No P&L)</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="space-y-5 order-1 lg:order-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#4680ff] bg-[#eef4fe] px-3 py-1 rounded-full border border-blue-200">
                        Enterprise Governance
                    </span>
                    <h2 class="text-3xl font-extrabold text-[#1d2630] leading-snug">
                        Immutable Documents & Staff Access Restrictions
                    </h2>
                    <p class="text-sm text-[#5b6b79] leading-relaxed">
                        Protect your company margins. Sensitive cost prices and profit margins are strictly hidden from non-admin staff. Staff members can create permitted documents, but cannot re-edit or delete saved records without owner authorization.
                    </p>
                    <ul class="space-y-2.5 text-xs text-[#334155] font-semibold">
                        <li class="flex items-center gap-2">
                            <span class="text-[#10b981]">✓</span>
                            <span>Dedicated Staff Workspace Launchpad with quick shortcuts and zero profit metrics</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-[#10b981]">✓</span>
                            <span>One-time record creation prevents tampering with invoice and quotation pricing</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. Dynamic Pricing Tier Cards (Plan Model) -->
    <section id="pricing" class="py-20 bg-white border-t border-[#e8ebee]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-xs font-bold uppercase tracking-wider text-[#4680ff] bg-[#eef4fe] px-3 py-1 rounded-full border border-blue-200">
                    Transparent Pricing in LKR
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-[#1d2630] mt-3">
                    Affordable Monthly & Annual Subscriptions
                </h2>
                <p class="text-sm text-[#5b6b79] mt-2">
                    Start with a 14-day full-access trial. Upgrade or renew seamlessly with Sri Lankan bank transfer or card.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                @foreach ($plans as $plan)
                    @php
                        $isPopular = $plan->slug === 'professional';
                    @endphp
                    <div class="able-card p-8 flex flex-col justify-between relative {{ $isPopular ? 'border-2 border-[#4680ff] ring-4 ring-blue-50' : '' }}">
                        @if ($isPopular)
                            <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-[#4680ff] text-white text-[11px] font-extrabold uppercase tracking-wider px-3.5 py-1 rounded-full shadow-md">
                                Most Popular for EPC
                            </div>
                        @endif

                        <div>
                            <h3 class="text-lg font-bold text-[#1d2630]">{{ $plan->name }}</h3>
                            <div class="mt-4 flex items-baseline gap-1">
                                <span class="text-3xl font-extrabold text-[#1d2630]">Rs. {{ number_format($plan->price_monthly, 0) }}</span>
                                <span class="text-xs text-[#5b6b79] font-medium">/ month</span>
                            </div>
                            <div class="text-[11.5px] text-[#059669] font-bold mt-1">
                                or Rs. {{ number_format($plan->price_yearly, 0) }} / year (Save 2 Months)
                            </div>

                            <div class="mt-6 pt-6 border-t border-[#e8ebee] space-y-3">
                                <div class="text-xs font-bold text-[#5b6b79] uppercase tracking-wider">Features Included:</div>
                                @if (is_array($plan->features))
                                    @foreach ($plan->features as $feature)
                                        <div class="flex items-center gap-2 text-xs text-[#334155]">
                                            <span class="text-[#10b981] font-bold">✓</span>
                                            <span>{{ $feature }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="flex items-center gap-2 text-xs text-[#334155]">
                                        <span class="text-[#10b981] font-bold">✓</span>
                                        <span>Max Users: {{ $plan->max_users }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs text-[#334155]">
                                        <span class="text-[#10b981] font-bold">✓</span>
                                        <span>Max Monthly Invoices: {{ $plan->max_invoices }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-[#e8ebee]">
                            <a
                                href="/app/register?plan={{ $plan->slug }}"
                                class="{{ $isPopular ? 'btn-primary' : 'btn-outline' }} w-full py-3 text-center block text-sm font-bold"
                            >
                                Start 14-Day Free Trial
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 6. Clean Enterprise Footer (Strictly without pixelcraftstudioslk email) -->
    <footer class="bg-white border-t border-[#e8ebee] py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Col 1: Brand -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-[#4680ff] flex items-center justify-center text-white font-bold text-base">
                            VB
                        </div>
                        <span class="text-lg font-bold text-[#1d2630]">Vertex Books</span>
                    </div>
                    <p class="text-xs text-[#5b6b79] leading-relaxed">
                        Sri Lanka's dedicated cloud ERP platform for solar engineering, retail point-of-sale, and wholesale distribution.
                    </p>
                </div>

                <!-- Col 2: Solutions -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#1d2630] mb-3">Solutions</h4>
                    <ul class="space-y-2 text-xs text-[#5b6b79]">
                        <li><a href="#solar-erp" class="hover:text-[#4680ff]">Solar Sizing & Agreements</a></li>
                        <li><a href="#features" class="hover:text-[#4680ff]">Zoho-Style Invoicing</a></li>
                        <li><a href="#pos" class="hover:text-[#4680ff]">Counter POS Receipts</a></li>
                        <li><a href="#solar-erp" class="hover:text-[#4680ff]">Completion Certificates</a></li>
                    </ul>
                </div>

                <!-- Col 3: Company -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#1d2630] mb-3">Access</h4>
                    <ul class="space-y-2 text-xs text-[#5b6b79]">
                        <li><a href="/app/login" class="hover:text-[#4680ff]">Tenant Sign In</a></li>
                        <li><a href="/app/register" class="hover:text-[#4680ff]">Register New Company</a></li>
                        <li><a href="#pricing" class="hover:text-[#4680ff]">Subscription Plans</a></li>
                    </ul>
                </div>

                <!-- Col 4: Platform Security -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#1d2630] mb-3">Compliance & Region</h4>
                    <p class="text-xs text-[#5b6b79] leading-relaxed">
                        Optimized for Sri Lanka Telecom & Dialog 4G networks. LKR native currency with bank transfer & card processing.
                    </p>
                    <div class="mt-2 text-xs text-[#5b6b79]">
                        Inquiries: <a href="mailto:support@vertexinnovationlk.com" class="text-[#4680ff] hover:underline font-semibold">support@vertexinnovationlk.com</a>
                    </div>
                    <div class="mt-3 text-[11px] text-[#059669] font-bold">
                        ● Official Cloud Infrastructure (vertexinnovationlk.com)
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-[#e8ebee] flex flex-col sm:flex-row items-center justify-between text-xs text-[#64748b] gap-4">
                <div>
                    © {{ date('Y') }} Vertex Books ERP. All rights reserved.
                </div>
                <div class="flex items-center gap-6">
                    <a href="/privacy" class="hover:text-[#4680ff]">Privacy Policy</a>
                    <a href="/terms" class="hover:text-[#4680ff]">Terms of Service</a>
                    <a href="/app/login" class="hover:text-[#4680ff]">Staff Portal</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>

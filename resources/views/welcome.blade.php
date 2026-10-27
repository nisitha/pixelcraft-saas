@php
    try {
        $plans = \App\Models\Plan::where('is_active', true)->orderBy('price_monthly')->get();
    } catch (\Throwable $e) {
        $plans = collect();
    }
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
                'features' => ['CEB & LECO Solar PV Sizing', 'Zoho-Style Milestone Invoicing', '80mm Thermal Receipt POS', 'PDF Agreements & Gate Passes', 'Email & WhatsApp Sharing', 'Single Branch & 2 Staff Logins'],
            ],
            (object) [
                'id' => 2,
                'name' => 'Professional EPC ERP',
                'slug' => 'professional',
                'price_monthly' => 7500.00,
                'price_yearly' => 75000.00,
                'max_users' => 5,
                'max_invoices' => 500,
                'features' => ['Everything in Starter', 'Multi-User Role Permissions', 'Real-Time Project Profitability', 'Warranty Serial Number Vault', 'Supplier COGS & Commission Payouts', 'Official Completion Certificates', 'Statutory EPF & ETF Payroll Engine'],
            ],
            (object) [
                'id' => 3,
                'name' => 'Enterprise Unlimited',
                'slug' => 'enterprise',
                'price_monthly' => 14500.00,
                'price_yearly' => 145000.00,
                'max_users' => 999,
                'max_invoices' => 999999,
                'features' => ['Unlimited Staff & Branches', 'Custom Domain & Branding', 'Dedicated Account Manager', 'Multi-Warehouse Inventory', 'Automated Daily Cloud Backups', 'Priority SLA 24/7 Phone Support', 'Custom Inland Revenue Export API'],
            ],
        ]);
    }
@endphp
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PixelCraft SaaS — Sri Lanka's Modern Cloud ERP, Counter POS & Solar PV Workspace</title>
    <meta name="description" content="PixelCraft SaaS is Sri Lanka's high-performance cloud platform designed for Solar EPC contractors, retail merchants, and business operations. Featuring CEB solar sizing, Zoho milestone invoicing, 80mm thermal POS, and EPF/ETF payroll.">

    <!-- Fonts: Urbanist (Headings/Display) & Inter (Body/UI) like marketinglab.framer.ai -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,600;1,700&family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        urbanist: ['Urbanist', 'sans-serif'],
                        sans: ['Inter', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        framer: {
                            canvas: '#f3efe8',
                            card: '#ffffff',
                            dark: '#0a0a0a',
                            darkSurface: '#171717',
                            darkCard: '#1c1c1c',
                            coral: '#ff4c39',
                            coralHover: '#e03b29',
                            coralDark: '#b82b18',
                            coralSubtle: '#fef2f0',
                            warmBorder: '#eae0cf',
                            subtleBorder: '#e8e4dc',
                            darkBorder: '#262626',
                            textDark: '#0a0a0a',
                            textMuted: '#737373',
                            textLight: '#a1a1a1',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            --canvas: #f3efe8;
            --coral: #ff4c39;
            --coral-hover: #e03b29;
            --text-dark: #0a0a0a;
            --text-muted: #737373;
            --warm-border: #eae0cf;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--canvas);
            color: var(--text-dark);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3, h4, .font-display {
            font-family: 'Urbanist', sans-serif;
            letter-spacing: -0.03em;
        }

        /* Glass Floating Navbar */
        .glass-nav {
            background: rgba(243, 239, 232, 0.82);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(234, 224, 207, 0.7);
        }

        /* Framer Pill Buttons */
        .btn-coral {
            background-color: #ff4c39;
            color: #ffffff;
            font-family: 'Urbanist', sans-serif;
            font-weight: 700;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }
        .btn-coral:hover {
            background-color: #e03b29;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(255, 76, 57, 0.4);
        }
        .btn-coral:hover .btn-arrow {
            transform: translate(2px, -2px);
        }

        .btn-ghost-pill {
            background-color: #ffffff;
            color: #0a0a0a;
            border: 1px solid #eae0cf;
            font-family: 'Urbanist', sans-serif;
            font-weight: 700;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-ghost-pill:hover {
            background-color: #fbfaf9;
            border-color: #ff4c39;
            color: #ff4c39;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.06);
        }
        .btn-ghost-pill:hover .btn-arrow {
            transform: translate(2px, -2px);
        }

        .btn-arrow {
            transition: transform 0.2s ease;
        }

        /* MarketingLab Giant Display Word with Flame/Portrait Cutout Mask */
        .hero-giant-word {
            font-family: 'Urbanist', sans-serif;
            font-weight: 900;
            font-size: clamp(3.8rem, 15vw, 15.5rem);
            line-height: 0.85;
            letter-spacing: -0.05em;
            text-transform: uppercase;
            text-align: center;
            user-select: none;
            background: linear-gradient(180deg, #1a0f0d 0%, #ff4c39 45%, #ff7a63 80%, #b82b18 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-top: 1rem;
            margin-bottom: 2rem;
            position: relative;
        }

        /* 4 Process Glowing Cards */
        .process-card {
            background: linear-gradient(155deg, #ff4c39 0%, #e03b29 45%, #9b1e0f 100%);
            border-radius: 1.75rem;
            position: relative;
            overflow: hidden;
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.35s ease;
            box-shadow: 0 15px 35px -10px rgba(255, 76, 57, 0.35);
        }
        .process-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(255, 76, 57, 0.55);
        }

        /* Continuous Infinite Marquee */
        @keyframes marquee {
            0% { transform: translate3d(0, 0, 0); }
            100% { transform: translate3d(-50%, 0, 0); }
        }
        .marquee-track {
            display: flex;
            width: max-content;
            animation: marquee 32s linear infinite;
        }
        .marquee-track:hover {
            animation-play-state: paused;
        }

        /* Accordion Smooth Reveal */
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.35s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
            opacity: 0;
        }
        .faq-item.active .faq-answer {
            max-height: 250px;
            opacity: 1;
        }
        .faq-item.active .faq-icon {
            transform: rotate(45deg);
        }

        /* Bento Grid Card */
        .bento-card {
            background: #ffffff;
            border: 1px solid #eae0cf;
            border-radius: 1.5rem;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .bento-card:hover {
            border-color: #ff4c39;
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -15px rgba(255, 76, 57, 0.15);
        }

        /* Services Row Accordion (marketinglab style) */
        .service-row {
            border-bottom: 1px solid #eae0cf;
            padding: 2.25rem 0;
            transition: all 0.25s ease;
        }
        .service-row:hover {
            padding-left: 0.75rem;
            background-color: rgba(255, 255, 255, 0.4);
        }

        /* Floating Pill Badges */
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        .badge-floating {
            animation: floatSlow 4s ease-in-out infinite;
        }
        .badge-floating-delayed {
            animation: floatSlow 5s ease-in-out 1.5s infinite;
        }
    </style>
</head>
<body class="selection:bg-[#ff4c39] selection:text-white">

    <!-- 1. Sleek Frosted Glass Floating Navbar -->
    <header class="sticky top-0 z-50 glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand Logo (Urbanist Bold with Coral Dot) -->
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-[#0a0a0a] flex items-center justify-center text-white font-black font-urbanist text-xl group-hover:bg-[#ff4c39] transition-colors duration-300">
                        PC
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="font-urbanist font-extrabold text-2xl tracking-tight text-[#0a0a0a]">PixelCraft</span>
                        <span class="w-2 h-2 rounded-full bg-[#ff4c39]"></span>
                        <span class="ml-1 text-[11px] font-urbanist font-bold uppercase tracking-widest text-[#737373] hidden sm:inline-block">SaaS</span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-urbanist font-bold text-[#737373]">
                    <a href="#services" class="hover:text-[#ff4c39] transition-colors">Services</a>
                    <a href="#process" class="hover:text-[#ff4c39] transition-colors">Our Process</a>
                    <a href="#solar-erp" class="hover:text-[#ff4c39] transition-colors">Solar PV ERP</a>
                    <a href="#pos" class="hover:text-[#ff4c39] transition-colors">Counter POS</a>
                    <a href="#pricing" class="hover:text-[#ff4c39] transition-colors">Pricing in LKR</a>
                    <a href="#faq" class="hover:text-[#ff4c39] transition-colors">FAQ</a>
                </nav>

                <!-- Auth Buttons -->
                <div class="flex items-center gap-4">
                    <a href="/app/login" class="text-sm font-urbanist font-bold text-[#0a0a0a] hover:text-[#ff4c39] transition-colors hidden sm:block">
                        Sign In
                    </a>
                    <a href="/app/register" class="btn-coral px-6 py-2.5 text-sm shadow-sm">
                        <span>Start Free Trial</span>
                        <span class="btn-arrow text-base font-bold">↗</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <!-- 2. The Iconic MarketingLab Hero Section -->
        <section class="pt-8 sm:pt-14 pb-16 relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Top Meta Row (©2026 left, SRI LANKA B2B right) -->
                <div class="flex items-center justify-between text-xs sm:text-sm font-urbanist font-black uppercase tracking-wider text-[#0a0a0a] border-b border-[#eae0cf] pb-4 mb-2">
                    <div class="flex items-center gap-2">
                        <span>©2026</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-[#ff4c39]"></span>
                        <span class="text-[#737373] font-medium hidden sm:inline">COLOMBO, SRI LANKA</span>
                    </div>
                    <div class="tracking-widest text-[#0a0a0a]">
                        MULTI-TENANT CLOUD ERP & POS
                    </div>
                </div>

                <!-- Massive Kinetic Display Headline -->
                <div class="hero-giant-word select-none" data-motion-hero>
                    PIXELCRAFT
                </div>

                <!-- Hero Interactive Visual Card / Crimson Banner (like marketinglab.framer.ai) -->
                <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-[#ff4c39] via-[#e03b29] to-[#8c1608] text-white p-6 sm:p-12 lg:p-16 shadow-2xl">
                    <!-- Subtle Geometric Glow Background Pattern -->
                    <div class="absolute inset-0 opacity-15 pointer-events-none" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 24px 24px;"></div>
                    <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-[#ff7a63] rounded-full blur-3xl opacity-30 pointer-events-none"></div>

                    <!-- Hero Content Grid -->
                    <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                        <div class="lg:col-span-7 space-y-6">
                            <!-- Pill Tagline Badge -->
                            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/15 backdrop-blur-md border border-white/25 text-white text-xs font-urbanist font-extrabold tracking-wider uppercase">
                                <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                                14-Day Free Full Access • No Credit Card Required
                            </div>

                            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-urbanist font-black text-white leading-[1.08] tracking-tight">
                                Smart Cloud Accounting, POS & <span class="underline decoration-white/40 decoration-wavy">Solar ERP</span> for Sri Lanka.
                            </h1>

                            <p class="text-base sm:text-lg text-white/90 font-normal leading-relaxed max-w-xl">
                                Engineered specifically for Sri Lankan solar EPC contractors, electrical trade distributors, and retail merchants. Featuring CEB solar sizing, Zoho-style milestone invoicing, 80mm POS receipts, and EPF/ETF statutory payroll.
                            </p>

                            <!-- CTAs -->
                            <div class="pt-2 flex flex-wrap items-center gap-4">
                                <a href="/app/register" class="btn-ghost-pill px-8 py-4 text-base font-bold shadow-lg">
                                    <span class="text-[#0a0a0a]">Start Free 14-Day Trial</span>
                                    <span class="btn-arrow text-[#ff4c39] font-black text-lg">↗</span>
                                </a>
                                <a href="/app/login" class="inline-flex items-center gap-2 px-7 py-4 rounded-full border border-white/40 bg-white/10 hover:bg-white/20 text-white font-urbanist font-bold text-base backdrop-blur-sm transition-all">
                                    <span>Sign In to Workspace</span>
                                    <span class="btn-arrow">↗</span>
                                </a>
                            </div>
                        </div>

                        <!-- Right Floating Interactive Badges & KPI Metric Cards -->
                        <div class="lg:col-span-5 flex flex-col gap-4">
                            <!-- Floating Pill Badges Stack -->
                            <div class="flex flex-wrap gap-2 justify-start lg:justify-end">
                                <span class="badge-floating px-4 py-2 rounded-full bg-white/15 backdrop-blur-md border border-white/30 text-white text-xs font-urbanist font-bold">
                                    ☀️ CEB & LECO Solar Sizing
                                </span>
                                <span class="badge-floating-delayed px-4 py-2 rounded-full bg-white/15 backdrop-blur-md border border-white/30 text-white text-xs font-urbanist font-bold">
                                    📑 Zoho-Style Milestone Invoicing
                                </span>
                                <span class="badge-floating px-4 py-2 rounded-full bg-white/15 backdrop-blur-md border border-white/30 text-white text-xs font-urbanist font-bold">
                                    🧾 80mm ESC/POS Printing
                                </span>
                                <span class="badge-floating-delayed px-4 py-2 rounded-full bg-white/15 backdrop-blur-md border border-white/30 text-white text-xs font-urbanist font-bold">
                                    👥 EPF 12% & ETF 3% Payroll
                                </span>
                            </div>

                            <!-- Live Stat Preview Box -->
                            <div class="bg-black/30 backdrop-blur-xl border border-white/20 rounded-2xl p-6 text-white space-y-4 shadow-xl">
                                <div class="flex items-center justify-between text-xs pb-3 border-b border-white/15 font-urbanist font-bold">
                                    <span class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                        app.pixelcraftlk.com
                                    </span>
                                    <span class="text-white/80 uppercase tracking-widest text-[10px]">Multi-Tenant v3.4</span>
                                </div>

                                <div class="grid grid-cols-2 gap-4 text-left">
                                    <div>
                                        <div class="text-xs text-white/70 font-medium">Monthly Invoiced</div>
                                        <div class="text-2xl font-urbanist font-black text-white mt-0.5">Rs. 1.25M</div>
                                        <div class="text-[11px] text-emerald-300 font-bold mt-0.5">↗ 30.6% this month</div>
                                    </div>
                                    <div>
                                        <div class="text-xs text-white/70 font-medium">Solar Capacity</div>
                                        <div class="text-2xl font-urbanist font-black text-white mt-0.5">485 kWp</div>
                                        <div class="text-[11px] text-amber-300 font-bold mt-0.5">CEB Net-Metering</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Metrics Bar (10+ Years, 500M+ Impressions style from marketinglab) -->
                    <div class="mt-12 pt-8 border-t border-white/20 grid grid-cols-2 md:grid-cols-4 gap-6 text-white font-urbanist">
                        <div>
                            <div class="text-3xl sm:text-4xl font-black">14 Days</div>
                            <div class="text-xs text-white/80 font-semibold tracking-wide uppercase mt-1">Full Free Trial</div>
                        </div>
                        <div>
                            <div class="text-3xl sm:text-4xl font-black">Rs. 500M+</div>
                            <div class="text-xs text-white/80 font-semibold tracking-wide uppercase mt-1">Invoices Generated</div>
                        </div>
                        <div>
                            <div class="text-3xl sm:text-4xl font-black">100%</div>
                            <div class="text-xs text-white/80 font-semibold tracking-wide uppercase mt-1">CEB & LECO Ready</div>
                        </div>
                        <div>
                            <div class="text-3xl sm:text-4xl font-black">0.2 sec</div>
                            <div class="text-xs text-white/80 font-semibold tracking-wide uppercase mt-1">Thermal POS Speed</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. Infinite Smooth Marquee Ticker -->
        <div class="border-y border-[#eae0cf] bg-[#fbfaf9] py-5 overflow-hidden select-none">
            <div class="marquee-track flex items-center text-xs sm:text-sm font-urbanist font-extrabold uppercase tracking-widest text-[#0a0a0a]">
                <div class="flex items-center gap-8 shrink-0 px-4">
                    <span><span class="text-[#ff4c39] font-black mr-2">/</span> SOLAR PV SIZING</span>
                    <span><span class="text-[#ff4c39] font-black mr-2">/</span> ZOHO MILESTONE INVOICING</span>
                    <span><span class="text-[#ff4c39] font-black mr-2">/</span> 80MM COUNTER POS</span>
                    <span><span class="text-[#ff4c39] font-black mr-2">/</span> EPF 8%/12% & ETF 3% PAYROLL</span>
                    <span><span class="text-[#ff4c39] font-black mr-2">/</span> CEB & LECO AGREEMENTS</span>
                    <span><span class="text-[#ff4c39] font-black mr-2">/</span> COMMISSIONING CERTIFICATES</span>
                    <span><span class="text-[#ff4c39] font-black mr-2">/</span> WARRANTY SERIAL TRACKING</span>
                    <span><span class="text-[#ff4c39] font-black mr-2">/</span> COGS & SUPPLIER BILLS</span>
                    <span><span class="text-[#ff4c39] font-black mr-2">/</span> AGENT COMMISSION PAYOUTS</span>
                </div>
                <div class="flex items-center gap-8 shrink-0 px-4">
                    <span><span class="text-[#ff4c39] font-black mr-2">/</span> SOLAR PV SIZING</span>
                    <span><span class="text-[#ff4c39] font-black mr-2">/</span> ZOHO MILESTONE INVOICING</span>
                    <span><span class="text-[#ff4c39] font-black mr-2">/</span> 80MM COUNTER POS</span>
                    <span><span class="text-[#ff4c39] font-black mr-2">/</span> EPF 8%/12% & ETF 3% PAYROLL</span>
                    <span><span class="text-[#ff4c39] font-black mr-2">/</span> CEB & LECO AGREEMENTS</span>
                    <span><span class="text-[#ff4c39] font-black mr-2">/</span> COMMISSIONING CERTIFICATES</span>
                    <span><span class="text-[#ff4c39] font-black mr-2">/</span> WARRANTY SERIAL TRACKING</span>
                    <span><span class="text-[#ff4c39] font-black mr-2">/</span> COGS & SUPPLIER BILLS</span>
                    <span><span class="text-[#ff4c39] font-black mr-2">/</span> AGENT COMMISSION PAYOUTS</span>
                </div>
            </div>
        </div>

        <!-- 4. The Manifesto Statement (marketinglab style) -->
        <section class="py-20 sm:py-28">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                    <!-- Left Meta Column -->
                    <div class="lg:col-span-3 space-y-3 font-urbanist text-xs font-bold text-[#737373] tracking-widest uppercase">
                        <div>SINCE 2024</div>
                        <div>SRI LANKA MARKET</div>
                        <div>MULTI-TENANT SAAS</div>
                        <div>FILAMENT 3.X DUAL PANELS</div>
                    </div>

                    <!-- Right Giant Manifesto -->
                    <div class="lg:col-span-9 space-y-8">
                        <h2 class="text-2xl sm:text-4xl lg:text-5xl font-urbanist font-extrabold text-[#0a0a0a] leading-[1.25]">
                            We don't do fragmented spreadsheets. Every Sri Lankan business gets a unified cloud workspace built from scratch, obsessed with engineering precision, and executed with local compliance — <span class="text-[#ff4c39]">until every rupee is accounted for.</span>
                        </h2>

                        <!-- Partner / Compliance Badge Cards (like marketinglab logo cards) -->
                        <div class="pt-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div class="bento-card p-5 text-center">
                                <div class="font-urbanist font-black text-lg text-[#0a0a0a]">CEB Ready</div>
                                <div class="text-[11px] text-[#737373] mt-1">Net-Metering & Net-Plus</div>
                            </div>
                            <div class="bento-card p-5 text-center">
                                <div class="font-urbanist font-black text-lg text-[#0a0a0a]">LECO Standards</div>
                                <div class="text-[11px] text-[#737373] mt-1">Grid Sizing Compatible</div>
                            </div>
                            <div class="bento-card p-5 text-center">
                                <div class="font-urbanist font-black text-lg text-[#0a0a0a]">ESC/POS 80mm</div>
                                <div class="text-[11px] text-[#737373] mt-1">Direct Thermal Printing</div>
                            </div>
                            <div class="bento-card p-5 text-center">
                                <div class="font-urbanist font-black text-lg text-[#0a0a0a]">EPF / ETF Compliant</div>
                                <div class="text-[11px] text-[#737373] mt-1">Sri Lanka Labor Laws</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. "Our Process" — Iconic 4-Step Framework in Deep Obsidian Black (marketinglab.framer.ai) -->
        <section id="process" class="py-24 sm:py-32 bg-[#0a0a0a] text-white relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
                    <div>
                        <span class="text-xs font-urbanist font-black uppercase tracking-widest text-[#ff4c39]">
                            Disciplined Execution
                        </span>
                        <h2 class="text-4xl sm:text-6xl font-urbanist font-black text-white mt-3 tracking-tight">
                            Our Process
                        </h2>
                    </div>
                    <p class="text-sm sm:text-base text-[#a1a1a1] max-w-md font-normal leading-relaxed">
                        A disciplined four-step operational framework that eliminates billing guesswork, secures margins, and delivers predictable business growth.
                    </p>
                </div>

                <!-- 4 Glowing Process Cards (01, 02, 03, 04) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Card 01 -->
                    <div class="process-card p-8 h-[440px] flex flex-col justify-between group">
                        <div class="flex items-start justify-between">
                            <span class="text-6xl sm:text-7xl font-urbanist font-black text-white leading-none">01</span>
                            <span class="w-8 h-8 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white text-xs font-bold">✓</span>
                        </div>
                        <div class="space-y-3">
                            <h3 class="text-2xl font-urbanist font-black text-white group-hover:translate-x-1 transition-transform">
                                Onboard & Brand
                            </h3>
                            <p class="text-xs sm:text-sm text-white/90 leading-relaxed font-normal">
                                Instant multi-tenant registration. Upload company logo, set branch locations, configure VAT numbers, and invite role-restricted staff in under 2 minutes.
                            </p>
                            <div class="pt-2">
                                <span class="text-[11px] font-urbanist font-bold uppercase tracking-wider text-white/70 bg-black/20 px-3 py-1 rounded-full">
                                    Step 01 • Configuration
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 02 -->
                    <div class="process-card p-8 h-[440px] flex flex-col justify-between group" style="background: linear-gradient(155deg, #ff5744 0%, #d4300e 45%, #8a1506 100%);">
                        <div class="flex items-start justify-between">
                            <span class="text-6xl sm:text-7xl font-urbanist font-black text-white leading-none">02</span>
                            <span class="w-8 h-8 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white text-xs font-bold">✓</span>
                        </div>
                        <div class="space-y-3">
                            <h3 class="text-2xl font-urbanist font-black text-white group-hover:translate-x-1 transition-transform">
                                Engineer & Quote
                            </h3>
                            <p class="text-xs sm:text-sm text-white/90 leading-relaxed font-normal">
                                Input customer CEB monthly electricity units for instant kWp solar sizing. Draft Zoho-style milestone proposals with exact inverter/panel bill of quantities.
                            </p>
                            <div class="pt-2">
                                <span class="text-[11px] font-urbanist font-bold uppercase tracking-wider text-white/70 bg-black/20 px-3 py-1 rounded-full">
                                    Step 02 • Estimation
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 03 -->
                    <div class="process-card p-8 h-[440px] flex flex-col justify-between group" style="background: linear-gradient(155deg, #ff4c39 0%, #b82b18 55%, #660e03 100%);">
                        <div class="flex items-start justify-between">
                            <span class="text-6xl sm:text-7xl font-urbanist font-black text-white leading-none">03</span>
                            <span class="w-8 h-8 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white text-xs font-bold">✓</span>
                        </div>
                        <div class="space-y-3">
                            <h3 class="text-2xl font-urbanist font-black text-white group-hover:translate-x-1 transition-transform">
                                Bill & Transact
                            </h3>
                            <p class="text-xs sm:text-sm text-white/90 leading-relaxed font-normal">
                                Convert approved quotations into milestone invoices (60% advance, 30% delivery, 10% commissioning) or print instant 80mm ESC/POS counter receipts.
                            </p>
                            <div class="pt-2">
                                <span class="text-[11px] font-urbanist font-bold uppercase tracking-wider text-white/70 bg-black/20 px-3 py-1 rounded-full">
                                    Step 03 • Transaction
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 04 -->
                    <div class="process-card p-8 h-[440px] flex flex-col justify-between group" style="background: linear-gradient(155deg, #ff6b57 0%, #d4300e 50%, #4a0901 100%);">
                        <div class="flex items-start justify-between">
                            <span class="text-6xl sm:text-7xl font-urbanist font-black text-white leading-none">04</span>
                            <span class="w-8 h-8 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white text-xs font-bold">✓</span>
                        </div>
                        <div class="space-y-3">
                            <h3 class="text-2xl font-urbanist font-black text-white group-hover:translate-x-1 transition-transform">
                                Settle & Profit
                            </h3>
                            <p class="text-xs sm:text-sm text-white/90 leading-relaxed font-normal">
                                Issue official A4 Commissioning Certificates, vault serial numbers for warranty claims, generate EPF/ETF pay slips, and calculate net project margins.
                            </p>
                            <div class="pt-2">
                                <span class="text-[11px] font-urbanist font-bold uppercase tracking-wider text-white/70 bg-black/20 px-3 py-1 rounded-full">
                                    Step 04 • Settlement
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. What We Offer / Services Rows (Iconic MarketingLab Layout) -->
        <section id="services" class="py-24 sm:py-32">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Heading -->
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
                    <div>
                        <span class="text-xs font-urbanist font-black uppercase tracking-widest text-[#ff4c39]">
                            Capabilities & Solutions
                        </span>
                        <h2 class="text-4xl sm:text-6xl font-urbanist font-black text-[#0a0a0a] mt-3 tracking-tight">
                            What we offer / Services
                        </h2>
                    </div>
                    <p class="text-sm sm:text-base text-[#737373] max-w-md font-normal leading-relaxed">
                        End-to-end cloud infrastructure engineered to build, run, and scale your business without relying on multiple disconnected apps.
                    </p>
                </div>

                <!-- Service Row 01 -->
                <div class="service-row group cursor-pointer">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                        <div class="md:col-span-1 text-xl font-urbanist font-black text-[#737373] group-hover:text-[#ff4c39] transition-colors">
                            01
                        </div>
                        <div class="md:col-span-3">
                            <span class="text-xs font-urbanist font-bold uppercase tracking-wider text-[#737373]">
                                Engineering Service
                            </span>
                            <h3 class="text-2xl sm:text-3xl font-urbanist font-extrabold text-[#0a0a0a] mt-1 group-hover:text-[#ff4c39] transition-colors">
                                Solar PV Sizing & BOQ
                            </h3>
                        </div>
                        <div class="md:col-span-5 text-xs sm:text-sm text-[#737373] leading-relaxed">
                            Algorithmic kWp capacity calculator based on Sri Lankan CEB/LECO electricity bills. Generates solar system agreements, bill of quantities, inverter specifications, and legal contract PDFs.
                        </div>
                        <div class="md:col-span-3 flex justify-start md:justify-end">
                            <a href="#solar-erp" class="btn-coral px-5 py-2.5 text-xs">
                                <span>Explore Solar ERP</span>
                                <span class="btn-arrow font-black">↗</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Service Row 02 -->
                <div class="service-row group cursor-pointer">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                        <div class="md:col-span-1 text-xl font-urbanist font-black text-[#737373] group-hover:text-[#ff4c39] transition-colors">
                            02
                        </div>
                        <div class="md:col-span-3">
                            <span class="text-xs font-urbanist font-bold uppercase tracking-wider text-[#737373]">
                                Billing Service
                            </span>
                            <h3 class="text-2xl sm:text-3xl font-urbanist font-extrabold text-[#0a0a0a] mt-1 group-hover:text-[#ff4c39] transition-colors">
                                Zoho Milestone Invoicing
                            </h3>
                        </div>
                        <div class="md:col-span-5 text-xs sm:text-sm text-[#737373] leading-relaxed">
                            Interactive dual split-pane editor with live A4 PDF preview. Milestone billing for EPC projects (Advance, Material Delivery, Testing & Commissioning) with 1-click WhatsApp and Email sharing.
                        </div>
                        <div class="md:col-span-3 flex justify-start md:justify-end">
                            <a href="/app/register" class="btn-coral px-5 py-2.5 text-xs">
                                <span>Try Milestone Invoicing</span>
                                <span class="btn-arrow font-black">↗</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Service Row 03 -->
                <div class="service-row group cursor-pointer">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                        <div class="md:col-span-1 text-xl font-urbanist font-black text-[#737373] group-hover:text-[#ff4c39] transition-colors">
                            03
                        </div>
                        <div class="md:col-span-3">
                            <span class="text-xs font-urbanist font-bold uppercase tracking-wider text-[#737373]">
                                Retail & Hardware
                            </span>
                            <h3 class="text-2xl sm:text-3xl font-urbanist font-extrabold text-[#0a0a0a] mt-1 group-hover:text-[#ff4c39] transition-colors">
                                80mm Counter POS
                            </h3>
                        </div>
                        <div class="md:col-span-5 text-xs sm:text-sm text-[#737373] leading-relaxed">
                            Sub-second cashier checkout terminal for trade counters and hardware stores. Barcode scanner support, cash tender change computation, and raw ESC/POS 80mm thermal receipt printing.
                        </div>
                        <div class="md:col-span-3 flex justify-start md:justify-end">
                            <a href="#pos" class="btn-coral px-5 py-2.5 text-xs">
                                <span>Explore Counter POS</span>
                                <span class="btn-arrow font-black">↗</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Service Row 04 -->
                <div class="service-row group cursor-pointer">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                        <div class="md:col-span-1 text-xl font-urbanist font-black text-[#737373] group-hover:text-[#ff4c39] transition-colors">
                            04
                        </div>
                        <div class="md:col-span-3">
                            <span class="text-xs font-urbanist font-bold uppercase tracking-wider text-[#737373]">
                                HR & Statutory
                            </span>
                            <h3 class="text-2xl sm:text-3xl font-urbanist font-extrabold text-[#0a0a0a] mt-1 group-hover:text-[#ff4c39] transition-colors">
                                EPF/ETF Payroll Engine
                            </h3>
                        </div>
                        <div class="md:col-span-5 text-xs sm:text-sm text-[#737373] leading-relaxed">
                            Automated payroll runs following Sri Lanka Department of Labour statutory rules. Calculates employee EPF 8%, employer EPF 12%, employer ETF 3%, and generates professional printable payslips.
                        </div>
                        <div class="md:col-span-3 flex justify-start md:justify-end">
                            <a href="/app/register" class="btn-coral px-5 py-2.5 text-xs">
                                <span>Try Payroll Module</span>
                                <span class="btn-arrow font-black">↗</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Service Row 05 -->
                <div class="service-row group cursor-pointer">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                        <div class="md:col-span-1 text-xl font-urbanist font-black text-[#737373] group-hover:text-[#ff4c39] transition-colors">
                            05
                        </div>
                        <div class="md:col-span-3">
                            <span class="text-xs font-urbanist font-bold uppercase tracking-wider text-[#737373]">
                                Financial Governance
                            </span>
                            <h3 class="text-2xl sm:text-3xl font-urbanist font-extrabold text-[#0a0a0a] mt-1 group-hover:text-[#ff4c39] transition-colors">
                                Project Profitability & COGS
                            </h3>
                        </div>
                        <div class="md:col-span-5 text-xs sm:text-sm text-[#737373] leading-relaxed">
                            Role-governed P&L reports. Sensitive equipment cost prices (COGS) and supplier bills are strictly concealed from cashier and sales staff, ensuring maximum margin confidentiality.
                        </div>
                        <div class="md:col-span-3 flex justify-start md:justify-end">
                            <a href="/app/register" class="btn-coral px-5 py-2.5 text-xs">
                                <span>See Governance</span>
                                <span class="btn-arrow font-black">↗</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 7. Solar EPC Deep Dive & Certificate Preview (solar-erp section) -->
        <section id="solar-erp" class="py-24 bg-[#ffffff] border-y border-[#eae0cf]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                    <div class="lg:col-span-6 space-y-6">
                        <span class="text-xs font-urbanist font-black uppercase tracking-widest text-[#ff4c39] bg-[#fef2f0] px-3.5 py-1.5 rounded-full border border-red-200">
                            Solar EPC Specialized Engine
                        </span>
                        <h2 class="text-3xl sm:text-5xl font-urbanist font-black text-[#0a0a0a] leading-tight">
                            Official Commissioning Certificates & Warranty Serial Tracking
                        </h2>
                        <p class="text-sm sm:text-base text-[#737373] leading-relaxed">
                            Never misplace serial numbers for high-value solar modules, inverters, or battery banks. PixelCraft SaaS links equipment barcodes directly to customer invoices and produces certified Commissioning Certificates ready for CEB grid approvals.
                        </p>

                        <div class="space-y-3 pt-2">
                            <div class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-[#ff4c39] text-white flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">✓</span>
                                <div class="text-xs sm:text-sm text-[#0a0a0a] font-medium">
                                    <strong class="font-urbanist font-bold">A4 & A5 Certificate Generator:</strong> Instant executive solar system commissioning certificates with verification QR codes.
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-[#ff4c39] text-white flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">✓</span>
                                <div class="text-xs sm:text-sm text-[#0a0a0a] font-medium">
                                    <strong class="font-urbanist font-bold">Warranty Serial Vault:</strong> Track individual panel & inverter serial numbers for rapid warranty claims without searching through filing cabinets.
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-[#ff4c39] text-white flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">✓</span>
                                <div class="text-xs sm:text-sm text-[#0a0a0a] font-medium">
                                    <strong class="font-urbanist font-bold">Freelance Promoter Vouchers:</strong> 1-click A5 commission settlement vouchers for external solar sales promoters and lead agents.
                                </div>
                            </div>
                        </div>

                        <div class="pt-4">
                            <a href="/app/register" class="btn-coral px-8 py-3.5 text-sm">
                                <span>Get Started with Solar EPC Module</span>
                                <span class="btn-arrow text-base font-bold">↗</span>
                            </a>
                        </div>
                    </div>

                    <!-- Interactive Certificate Preview Mockup -->
                    <div class="lg:col-span-6">
                        <div class="bento-card p-6 sm:p-8 relative bg-gradient-to-br from-[#fbfaf9] to-[#f3efe8] shadow-xl">
                            <div class="border-2 border-dashed border-[#eae0cf] rounded-2xl p-6 sm:p-8 bg-white text-center space-y-4">
                                <div class="flex items-center justify-between border-b border-[#eae0cf] pb-3 text-xs text-[#737373]">
                                    <span class="font-mono font-bold text-[#ff4c39]">CERTIFICATE #SL-SOL-2026-089</span>
                                    <span class="bg-emerald-50 text-emerald-700 font-bold px-2.5 py-0.5 rounded-full border border-emerald-200 text-[10px]">VERIFIED FOR CEB</span>
                                </div>

                                <div class="pt-2">
                                    <div class="text-[11px] font-urbanist font-bold text-[#737373] uppercase tracking-widest">OFFICIAL COMPLETION & COMMISSIONING</div>
                                    <h3 class="text-xl sm:text-2xl font-serif font-black text-[#0a0a0a] mt-1">SOLAR PV POWER SYSTEM</h3>
                                    <p class="text-xs text-[#737373] mt-1">Issued to Project Owner: <strong class="text-[#0a0a0a]">Vertex Luxury Residencies Ltd</strong></p>
                                </div>

                                <div class="inline-flex items-center gap-2 bg-[#0a0a0a] text-white px-4 py-2 rounded-xl text-xs font-urbanist font-bold">
                                    <span>☀️ 15.0 kWp On-Grid System</span>
                                    <span class="text-white/40">•</span>
                                    <span>28x 550W Tier-1 Mono PERC</span>
                                </div>

                                <div class="grid grid-cols-2 gap-3 text-left pt-2 text-xs">
                                    <div class="bg-[#fbfaf9] p-2.5 rounded-lg border border-[#eae0cf]">
                                        <div class="text-[10px] text-[#737373] font-bold">INVERTER S/N</div>
                                        <div class="font-mono font-bold text-[#0a0a0a] truncate">SUNGROW-2026-9812A</div>
                                    </div>
                                    <div class="bg-[#fbfaf9] p-2.5 rounded-lg border border-[#eae0cf]">
                                        <div class="text-[10px] text-[#737373] font-bold">GRID APPROVAL</div>
                                        <div class="font-mono font-bold text-emerald-600">CEB NET-METERING</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 8. Counter POS & Governance Section (pos section) -->
        <section id="pos" class="py-24 sm:py-32">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                    <!-- Left: Role & Document Immutability Matrix -->
                    <div class="lg:col-span-6 order-2 lg:order-1">
                        <div class="bento-card p-6 sm:p-8 bg-[#0a0a0a] text-white shadow-2xl">
                            <div class="flex items-center justify-between pb-4 border-b border-[#262626] text-xs">
                                <span class="font-urbanist font-bold text-white tracking-wider uppercase">Staff Role Hierarchy</span>
                                <span class="text-[#ff4c39] font-mono font-bold">Strict Audit Log</span>
                            </div>

                            <div class="space-y-3 pt-5">
                                <div class="p-3.5 rounded-xl bg-[#171717] border border-[#262626] flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-urbanist font-extrabold text-white">Company Owner / MD</div>
                                        <div class="text-xs text-[#a1a1a1]">Executive Admin Access</div>
                                    </div>
                                    <span class="bg-[#ff4c39]/20 text-[#ff4c39] border border-[#ff4c39]/40 text-xs font-bold px-3 py-1 rounded-full">
                                        Full P&L & COGS
                                    </span>
                                </div>

                                <div class="p-3.5 rounded-xl bg-[#171717] border border-[#262626] flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-urbanist font-extrabold text-white">Sales Engineer</div>
                                        <div class="text-xs text-[#a1a1a1]">Solar PV Quoting & Invoicing</div>
                                    </div>
                                    <span class="bg-amber-500/20 text-amber-400 border border-amber-500/40 text-xs font-bold px-3 py-1 rounded-full">
                                        Create Immutable Quotes
                                    </span>
                                </div>

                                <div class="p-3.5 rounded-xl bg-[#171717] border border-[#262626] flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-urbanist font-extrabold text-white">Counter Cashier</div>
                                        <div class="text-xs text-[#a1a1a1]">80mm POS Terminal</div>
                                    </div>
                                    <span class="bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 text-xs font-bold px-3 py-1 rounded-full">
                                        Receipts Only (Zero Profit View)
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Info -->
                    <div class="lg:col-span-6 order-1 lg:order-2 space-y-6">
                        <span class="text-xs font-urbanist font-black uppercase tracking-widest text-[#ff4c39] bg-[#fef2f0] px-3.5 py-1.5 rounded-full border border-red-200">
                            Enterprise Security & POS Speed
                        </span>
                        <h2 class="text-3xl sm:text-5xl font-urbanist font-black text-[#0a0a0a] leading-tight">
                            Immutable Records & Margin Confidentiality
                        </h2>
                        <p class="text-sm sm:text-base text-[#737373] leading-relaxed">
                            Protect your profit margins. Supplier equipment wholesale costs and company net margins are strictly invisible to non-admin staff. Once quotations and invoices are dispatched, tamper-resistant locking ensures figures cannot be altered without owner approval.
                        </p>
                        <div class="pt-2 flex flex-wrap gap-4">
                            <a href="/app/register" class="btn-coral px-8 py-3.5 text-sm">
                                <span>Create Your Secure Workspace</span>
                                <span class="btn-arrow font-bold">↗</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 9. Dynamic Pricing Section in LKR (with Monthly / Annual Switcher) -->
        <section id="pricing" class="py-24 sm:py-32 bg-[#ffffff] border-t border-[#eae0cf]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="text-center max-w-3xl mx-auto space-y-4 mb-14">
                    <span class="text-xs font-urbanist font-black uppercase tracking-widest text-[#ff4c39] bg-[#fef2f0] px-3.5 py-1.5 rounded-full border border-red-200">
                        Predictable Pricing in LKR
                    </span>
                    <h2 class="text-4xl sm:text-6xl font-urbanist font-black text-[#0a0a0a] tracking-tight">
                        Simple Plans for Sri Lankan Businesses
                    </h2>
                    <p class="text-sm sm:text-base text-[#737373]">
                        Every plan includes a 14-day risk-free trial. Upgrade or renew conveniently via local bank transfer or card.
                    </p>

                    <!-- Monthly vs Annual Interactive Switcher -->
                    <div class="pt-6 flex items-center justify-center">
                        <div class="bg-[#f3efe8] p-1.5 rounded-full inline-flex items-center gap-1 border border-[#eae0cf] shadow-inner" id="billingSwitcher">
                            <button
                                type="button"
                                id="btnMonthly"
                                class="px-6 py-2.5 rounded-full text-xs font-urbanist font-extrabold transition-all bg-[#0a0a0a] text-white shadow-md"
                                onclick="switchBilling('monthly')"
                            >
                                Monthly Billing
                            </button>
                            <button
                                type="button"
                                id="btnYearly"
                                class="px-6 py-2.5 rounded-full text-xs font-urbanist font-extrabold transition-all text-[#737373] hover:text-[#0a0a0a]"
                                onclick="switchBilling('yearly')"
                            >
                                <span>Annual Billing</span>
                                <span class="ml-1 text-[10px] bg-[#ff4c39] text-white font-bold px-2 py-0.5 rounded-full">Save 2 Mo.</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 3 Pricing Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto items-stretch">
                    @foreach ($plans as $plan)
                        @php
                            $isPopular = $plan->slug === 'professional';
                        @endphp
                        <div class="bento-card p-8 flex flex-col justify-between relative transition-all duration-300 {{ $isPopular ? 'border-2 border-[#ff4c39] ring-8 ring-red-50/50 shadow-2xl scale-[1.03] z-10' : '' }}">
                            @if ($isPopular)
                                <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-[#ff4c39] text-white text-[11px] font-urbanist font-black uppercase tracking-wider px-4 py-1 rounded-full shadow-md">
                                    ★ Most Popular for EPC
                                </div>
                            @endif

                            <div>
                                <h3 class="text-xl font-urbanist font-black text-[#0a0a0a]">{{ $plan->name }}</h3>
                                <p class="text-xs text-[#737373] mt-1">
                                    {{ $plan->slug === 'starter' ? 'Ideal for solo installers & retail counters' : ($plan->slug === 'professional' ? 'Complete EPC ERP for growing contractors' : 'Unlimited multi-branch corporate governance') }}
                                </p>

                                <!-- Dynamic Price Display -->
                                <div class="mt-6 flex items-baseline gap-1">
                                    <span class="text-4xl font-urbanist font-black text-[#0a0a0a] plan-price" data-monthly="{{ $plan->price_monthly }}" data-yearly="{{ $plan->price_yearly }}">
                                        Rs. {{ number_format($plan->price_monthly, 0) }}
                                    </span>
                                    <span class="text-xs text-[#737373] font-semibold period-label">/ month</span>
                                </div>

                                <div class="text-[11px] text-[#ff4c39] font-urbanist font-bold mt-1 period-subtext">
                                    or Rs. {{ number_format($plan->price_yearly, 0) }} paid annually
                                </div>

                                <!-- Features List -->
                                <div class="mt-8 pt-6 border-t border-[#eae0cf] space-y-3">
                                    <div class="text-xs font-urbanist font-extrabold uppercase tracking-wider text-[#737373]">Features Included:</div>
                                    @if (is_array($plan->features))
                                        @foreach ($plan->features as $feature)
                                            <div class="flex items-center gap-2.5 text-xs text-[#0a0a0a]">
                                                <span class="text-[#ff4c39] font-black text-sm">✓</span>
                                                <span>{{ $feature }}</span>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="flex items-center gap-2.5 text-xs text-[#0a0a0a]">
                                            <span class="text-[#ff4c39] font-black text-sm">✓</span>
                                            <span>Max Users: {{ $plan->max_users }}</span>
                                        </div>
                                        <div class="flex items-center gap-2.5 text-xs text-[#0a0a0a]">
                                            <span class="text-[#ff4c39] font-black text-sm">✓</span>
                                            <span>Max Invoices: {{ $plan->max_invoices }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- CTA Button -->
                            <div class="mt-8 pt-6 border-t border-[#eae0cf]">
                                <a
                                    href="/app/register"
                                    class="{{ $isPopular ? 'btn-coral' : 'btn-ghost-pill' }} w-full py-3 text-center text-sm font-urbanist font-bold block"
                                >
                                    <span>Start 14-Day Free Trial</span>
                                    <span class="btn-arrow font-black">↗</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- 10. Frequently Asked Questions (Accordion) -->
        <section id="faq" class="py-24 sm:py-32">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center space-y-3 mb-16">
                    <span class="text-xs font-urbanist font-black uppercase tracking-widest text-[#ff4c39]">
                        Common Questions
                    </span>
                    <h2 class="text-4xl sm:text-5xl font-urbanist font-black text-[#0a0a0a] tracking-tight">
                        Frequently Asked Questions
                    </h2>
                </div>

                <div class="space-y-4">
                    <!-- FAQ 1 -->
                    <div class="bento-card p-6 faq-item active cursor-pointer" onclick="toggleFaq(this)">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base sm:text-lg font-urbanist font-bold text-[#0a0a0a]">
                                How does the 14-day free trial work?
                            </h3>
                            <span class="faq-icon text-2xl font-light text-[#ff4c39] transition-transform duration-300">✕</span>
                        </div>
                        <div class="faq-answer text-xs sm:text-sm text-[#737373] leading-relaxed pt-3">
                            You get immediate, unrestricted access to the complete PixelCraft SaaS platform including Solar PV sizing, milestone invoicing, and POS. No credit card is required to register. After 14 days, you can choose any LKR subscription plan.
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="bento-card p-6 faq-item cursor-pointer" onclick="toggleFaq(this)">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base sm:text-lg font-urbanist font-bold text-[#0a0a0a]">
                                Is the Solar PV sizing tool compliant with CEB & LECO?
                            </h3>
                            <span class="faq-icon text-2xl font-light text-[#ff4c39] transition-transform duration-300">+</span>
                        </div>
                        <div class="faq-answer text-xs sm:text-sm text-[#737373] leading-relaxed pt-3">
                            Yes. Our sizing algorithms calculate required system kilowatt-peak (kWp) against Sri Lankan solar irradiance factors, CEB single/three-phase thresholds, and Net-Metering/Net-Plus policies.
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="bento-card p-6 faq-item cursor-pointer" onclick="toggleFaq(this)">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base sm:text-lg font-urbanist font-bold text-[#0a0a0a]">
                                Can I use standard 80mm ESC/POS thermal printers?
                            </h3>
                            <span class="faq-icon text-2xl font-light text-[#ff4c39] transition-transform duration-300">+</span>
                        </div>
                        <div class="faq-answer text-xs sm:text-sm text-[#737373] leading-relaxed pt-3">
                            Absolutely. The counter POS module formats receipts specifically for 80mm thermal paper widths, supporting USB, Bluetooth, and network thermal printers commonly used across Sri Lankan retail stores.
                        </div>
                    </div>

                    <!-- FAQ 4 -->
                    <div class="bento-card p-6 faq-item cursor-pointer" onclick="toggleFaq(this)">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base sm:text-lg font-urbanist font-bold text-[#0a0a0a]">
                                Can staff members see our profit margins or cost prices?
                            </h3>
                            <span class="faq-icon text-2xl font-light text-[#ff4c39] transition-transform duration-300">+</span>
                        </div>
                        <div class="faq-answer text-xs sm:text-sm text-[#737373] leading-relaxed pt-3">
                            No. Role-based tenant isolation strictly hides internal cost of goods sold (COGS), supplier bills, and profit metrics from sales staff and cashiers. Only verified Company Owners and Admins have access to profitability reports.
                        </div>
                    </div>

                    <!-- FAQ 5 -->
                    <div class="bento-card p-6 faq-item cursor-pointer" onclick="toggleFaq(this)">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base sm:text-lg font-urbanist font-bold text-[#0a0a0a]">
                                What payment methods are supported in Sri Lanka?
                            </h3>
                            <span class="faq-icon text-2xl font-light text-[#ff4c39] transition-transform duration-300">+</span>
                        </div>
                        <div class="faq-answer text-xs sm:text-sm text-[#737373] leading-relaxed pt-3">
                            We support Sri Lankan direct bank transfers (Sampath, Commercial, HNB, BOC), corporate cheques, and Visa/Mastercard payments. Subscriptions are billed natively in LKR with zero foreign exchange fees.
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 11. High-Impact Call to Action Banner (marketinglab style) -->
        <section class="py-16 sm:py-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="rounded-3xl bg-[#0a0a0a] text-white p-8 sm:p-16 lg:p-20 relative overflow-hidden shadow-2xl text-center space-y-8">
                    <div class="max-w-2xl mx-auto space-y-4">
                        <span class="text-xs font-urbanist font-black uppercase tracking-widest text-[#ff4c39]">
                            Get Started Today
                        </span>
                        <h2 class="text-3xl sm:text-5xl lg:text-6xl font-urbanist font-black text-white leading-tight">
                            Ready to upgrade your business operations?
                        </h2>
                        <p class="text-sm sm:text-base text-[#a1a1a1] max-w-lg mx-auto">
                            Join leading Sri Lankan solar EPC contractors and retail distributors running on PixelCraft SaaS.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-2">
                        <a href="/app/register" class="btn-coral px-9 py-4 text-base font-bold shadow-xl w-full sm:w-auto">
                            <span>Start 14-Day Free Trial</span>
                            <span class="btn-arrow text-lg font-black">↗</span>
                        </a>
                        <a href="/app/login" class="px-8 py-4 rounded-full border border-white/30 hover:border-white text-white font-urbanist font-bold text-base transition-colors w-full sm:w-auto">
                            <span>Sign In to Workspace</span>
                            <span class="btn-arrow">↗</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- 12. Clean Enterprise Footer -->
    <footer class="border-t border-[#eae0cf] bg-[#fbfaf9] py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
                <!-- Col 1: Brand -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-[#0a0a0a] flex items-center justify-center text-white font-urbanist font-black text-lg">
                            PC
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="font-urbanist font-extrabold text-xl text-[#0a0a0a]">PixelCraft</span>
                            <span class="w-2 h-2 rounded-full bg-[#ff4c39]"></span>
                        </div>
                    </div>
                    <p class="text-xs text-[#737373] leading-relaxed">
                        Sri Lanka's dedicated multi-tenant cloud platform for engineering, retail point-of-sale, and wholesale milestone invoicing.
                    </p>
                    <div class="text-[11px] font-mono text-[#0a0a0a] font-bold">
                        app.pixelcraftlk.com
                    </div>
                </div>

                <!-- Col 2: Solutions -->
                <div class="space-y-3 font-urbanist">
                    <h4 class="text-xs font-black uppercase tracking-wider text-[#0a0a0a]">Solutions</h4>
                    <ul class="space-y-2 text-xs text-[#737373]">
                        <li><a href="#solar-erp" class="hover:text-[#ff4c39] transition-colors">CEB Solar PV Sizing</a></li>
                        <li><a href="#services" class="hover:text-[#ff4c39] transition-colors">Zoho Milestone Invoicing</a></li>
                        <li><a href="#pos" class="hover:text-[#ff4c39] transition-colors">80mm Counter Thermal POS</a></li>
                        <li><a href="#services" class="hover:text-[#ff4c39] transition-colors">Statutory EPF/ETF Payroll</a></li>
                    </ul>
                </div>

                <!-- Col 3: Access -->
                <div class="space-y-3 font-urbanist">
                    <h4 class="text-xs font-black uppercase tracking-wider text-[#0a0a0a]">Access & Company</h4>
                    <ul class="space-y-2 text-xs text-[#737373]">
                        <li><a href="/app/login" class="hover:text-[#ff4c39] transition-colors">Workspace Sign In</a></li>
                        <li><a href="/app/register" class="hover:text-[#ff4c39] transition-colors">Register Company</a></li>
                        <li><a href="#pricing" class="hover:text-[#ff4c39] transition-colors">Subscription Plans in LKR</a></li>
                        <li><a href="#process" class="hover:text-[#ff4c39] transition-colors">Operational Framework</a></li>
                    </ul>
                </div>

                <!-- Col 4: Regional Compliance -->
                <div class="space-y-3 font-urbanist">
                    <h4 class="text-xs font-black uppercase tracking-wider text-[#0a0a0a]">Compliance & Support</h4>
                    <p class="text-xs text-[#737373] leading-relaxed font-sans">
                        Multi-tenant database isolation. Native Sri Lankan LKR currency, VAT invoice compliance, and bank transfer support.
                    </p>
                    <div class="text-xs text-[#737373] font-sans">
                        Support: <a href="mailto:support@pixelcraftlk.com" class="text-[#ff4c39] hover:underline font-bold">support@pixelcraftlk.com</a>
                    </div>
                    <div class="text-[11px] text-emerald-600 font-bold flex items-center gap-1.5 pt-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Official Cloud Production System
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-[#eae0cf] flex flex-col sm:flex-row items-center justify-between text-xs text-[#737373] gap-4 font-urbanist">
                <div>
                    © {{ date('Y') }} PixelCraft SaaS. All rights reserved.
                </div>
                <div class="flex items-center gap-6">
                    <a href="/privacy" class="hover:text-[#ff4c39] transition-colors">Privacy Policy</a>
                    <a href="/terms" class="hover:text-[#ff4c39] transition-colors">Terms of Service</a>
                    <a href="/app/login" class="hover:text-[#ff4c39] transition-colors">Staff Launchpad</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Interactive Scripts & Framer Motion Integration -->
    <script type="module">
        // Import official Framer Motion engine for native ESM
        import { animate, inView, stagger, spring } from 'https://cdn.jsdelivr.net/npm/motion@12.4.2/+esm';

        // 1. Framer Motion Scroll Reveal for Hero Elements
        try {
            animate('[data-motion-hero]', 
                { opacity: [0, 1], y: [40, 0], scale: [0.96, 1] }, 
                { duration: 0.9, easing: spring({ stiffness: 120, damping: 20 }) }
            );

            // Animate Process Cards with Stagger
            inView('#process', () => {
                animate('.process-card', 
                    { opacity: [0, 1], y: [60, 0] }, 
                    { delay: stagger(0.12), duration: 0.8, easing: spring({ stiffness: 100, damping: 18 }) }
                );
            });

            // Animate Services Rows with Stagger
            inView('#services', () => {
                animate('.service-row', 
                    { opacity: [0, 1], x: [-30, 0] }, 
                    { delay: stagger(0.08), duration: 0.7, easing: spring({ stiffness: 120, damping: 22 }) }
                );
            });

            // Animate Bento Cards
            inView('.bento-card', (info) => {
                animate(info.target, 
                    { opacity: [0, 1], y: [30, 0] }, 
                    { duration: 0.6, easing: spring({ stiffness: 110, damping: 20 }) }
                );
            });
        } catch (e) {
            console.log('Framer Motion initialized with CSS fallback:', e);
        }

        // 2. Billing Period Interactive Switcher
        window.switchBilling = function(period) {
            const btnMonthly = document.getElementById('btnMonthly');
            const btnYearly = document.getElementById('btnYearly');
            const priceElements = document.querySelectorAll('.plan-price');
            const periodLabels = document.querySelectorAll('.period-label');
            const periodSubtexts = document.querySelectorAll('.period-subtext');

            if (period === 'yearly') {
                btnYearly.classList.add('bg-[#0a0a0a]', 'text-white', 'shadow-md');
                btnYearly.classList.remove('text-[#737373]');
                btnMonthly.classList.remove('bg-[#0a0a0a]', 'text-white', 'shadow-md');
                btnMonthly.classList.add('text-[#737373]');

                priceElements.forEach(el => {
                    const yearly = parseFloat(el.getAttribute('data-yearly'));
                    const monthlyEquiv = Math.round(yearly / 12);
                    el.innerText = 'Rs. ' + monthlyEquiv.toLocaleString();
                });
                periodLabels.forEach(el => el.innerText = '/ month, billed yearly');
                periodSubtexts.forEach(el => el.innerText = 'Save ~17% (2 Months Free)');
            } else {
                btnMonthly.classList.add('bg-[#0a0a0a]', 'text-white', 'shadow-md');
                btnMonthly.classList.remove('text-[#737373]');
                btnYearly.classList.remove('bg-[#0a0a0a]', 'text-white', 'shadow-md');
                btnYearly.classList.add('text-[#737373]');

                priceElements.forEach(el => {
                    const monthly = parseFloat(el.getAttribute('data-monthly'));
                    el.innerText = 'Rs. ' + monthly.toLocaleString();
                });
                periodLabels.forEach(el => el.innerText = '/ month');
                periodSubtexts.forEach(el => el.innerText = 'Billed monthly');
            }
        };

        // 3. FAQ Accordion Toggle
        window.toggleFaq = function(element) {
            const allItems = document.querySelectorAll('.faq-item');
            const isAlreadyActive = element.classList.contains('active');

            allItems.forEach(item => {
                item.classList.remove('active');
                const icon = item.querySelector('.faq-icon');
                if (icon) icon.innerText = '+';
            });

            if (!isAlreadyActive) {
                element.classList.add('active');
                const icon = element.querySelector('.faq-icon');
                if (icon) icon.innerText = '✕';
            }
        };
    </script>
</body>
</html>

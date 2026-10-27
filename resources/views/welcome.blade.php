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
                'name' => 'Growth EPC ERP',
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
    <title>PixelCraft SaaS — Sri Lanka's Modern Cloud ERP, Milestone Invoicing & Solar PV Workspace</title>
    <meta name="description" content="PixelCraft SaaS is Sri Lanka's high-performance cloud platform designed for Solar EPC contractors, retail merchants, and business operations. Featuring CEB solar sizing, Zoho milestone invoicing, 80mm thermal POS, and EPF/ETF payroll.">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="PixelCraft SaaS — Cloud ERP, Counter POS & Solar PV Workspace">
    <meta property="og:description" content="Sri Lanka's dedicated cloud ERP for Solar EPC contractors and retail distributors. CEB Net-Metering sizing, milestone invoicing, and 80mm POS.">
    <meta property="og:image" content="{{ asset('images/landing/hero-dashboard.webp') }}">

    <!-- Typography: Anton (Display / Hero), Plus Jakarta Sans & Inter (Body/UI), JetBrains Mono (Badges) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ['Anton', 'sans-serif'],
                        sans: ['"Plus Jakarta Sans"', 'Inter', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        cream: '#F5F4F0',
                        creamLight: '#FAF8F5',
                        warmBorder: '#EAE6DF',
                        pitch: '#0A0A0A',
                        pitchSurface: '#141414',
                        pitchCard: '#1C1C1C',
                        pitchBorder: '#262626',
                        crimson: '#FF3838',
                        crimsonHover: '#E53935',
                        crimsonDark: '#991B1B',
                        crimsonSubtle: '#FEF2F2',
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js Core for State & Accordions -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        :root {
            --cream: #F5F4F0;
            --cream-light: #FAF8F5;
            --warm-border: #EAE6DF;
            --pitch: #0A0A0A;
            --crimson: #FF3838;
            --crimson-hover: #E53935;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            background-color: var(--cream);
            color: #111111;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        .font-display {
            font-family: 'Anton', 'Oswald', sans-serif;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        /* Glass Floating Navbar */
        .glass-nav {
            background: rgba(245, 244, 240, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(234, 230, 223, 0.9);
        }

        /* Pill Buttons */
        .btn-crimson {
            background-color: #FF3838;
            color: #FFFFFF;
            font-weight: 700;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-crimson:hover {
            background-color: #E53935;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(255, 56, 56, 0.45);
        }
        .btn-crimson:hover .btn-arrow {
            transform: translate(2px, -2px);
        }

        .btn-ghost-pill {
            background-color: #FFFFFF;
            color: #0A0A0A;
            border: 1px solid #EAE6DF;
            font-weight: 700;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-ghost-pill:hover {
            background-color: #FAF8F5;
            border-color: #FF3838;
            color: #FF3838;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.06);
        }
        .btn-ghost-pill:hover .btn-arrow {
            transform: translate(2px, -2px);
        }

        .btn-arrow {
            transition: transform 0.2s ease;
        }

        /* Giant Display Word */
        .hero-giant-word {
            font-family: 'Anton', sans-serif;
            font-size: clamp(4rem, 15vw, 15.5rem);
            line-height: 0.88;
            letter-spacing: -0.01em;
            text-transform: uppercase;
            text-align: center;
            user-select: none;
            color: #0A0A0A;
            margin-top: 0.5rem;
            margin-bottom: 2rem;
        }

        /* 4 Process Cards (01-04) */
        .process-card {
            background: linear-gradient(155deg, #FF3838 0%, #D32F2F 50%, #880E0E 100%);
            border-radius: 1.75rem;
            position: relative;
            overflow: hidden;
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.35s ease;
            box-shadow: 0 15px 35px -10px rgba(255, 56, 56, 0.35);
        }
        .process-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(255, 56, 56, 0.55);
        }

        /* Infinite Marquee */
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

        /* Bento / Showcase Cards */
        .bento-card {
            background: #FFFFFF;
            border: 1px solid #EAE6DF;
            border-radius: 1.5rem;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .bento-card:hover {
            border-color: #FF3838;
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -15px rgba(255, 56, 56, 0.15);
        }

        /* Floating Pill Badges */
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        .floating-pill {
            animation: floatSlow 4s ease-in-out infinite;
        }
        .floating-pill-delayed {
            animation: floatSlow 5s ease-in-out 1.5s infinite;
        }

        /* ==========================================================================
           LIQUID EFFECTS & MORPHING BLOBS
           ========================================================================== */
        @keyframes liquidMorph {
            0%, 100% {
                border-radius: 42% 58% 70% 30% / 45% 45% 55% 55%;
                transform: translate(0, 0) rotate(0deg);
            }
            33% {
                border-radius: 70% 30% 46% 54% / 30% 29% 71% 70%;
                transform: translate(25px, -20px) rotate(120deg);
            }
            66% {
                border-radius: 38% 62% 35% 65% / 65% 58% 42% 35%;
                transform: translate(-20px, 25px) rotate(240deg);
            }
        }

        .liquid-blob {
            position: absolute;
            filter: blur(50px);
            opacity: 0.45;
            pointer-events: none;
            animation: liquidMorph 16s ease-in-out infinite alternate;
        }

        .liquid-blob-crimson {
            background: radial-gradient(circle, #FF3838 0%, #D32F2F 50%, transparent 75%);
        }

        .liquid-blob-coral {
            background: radial-gradient(circle, #FF6B6B 0%, #E53935 45%, transparent 75%);
            animation: liquidMorph 20s ease-in-out 3s infinite alternate-reverse;
        }

        /* Liquid Wave Reflection on Cards */
        .liquid-reflection {
            position: relative;
            overflow: hidden;
        }
        .liquid-reflection::after {
            content: '';
            position: absolute;
            top: -100%;
            left: -100%;
            width: 300%;
            height: 300%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0) 50%);
            transform: rotate(25deg);
            transition: transform 0.8s ease;
            pointer-events: none;
        }
        .liquid-reflection:hover::after {
            transform: rotate(25deg) translate(50%, 50%);
        }

        /* Footer Watermark */
        .footer-watermark {
            font-family: 'Anton', sans-serif;
            font-size: clamp(6rem, 24vw, 26rem);
            line-height: 0.8;
            color: rgba(255, 255, 255, 0.04);
            user-select: none;
            pointer-events: none;
            text-align: center;
            letter-spacing: -0.02em;
        }
    </style>
</head>
<body
    class="selection:bg-crimson selection:text-white relative"
    x-data="{
        billing: 'monthly',
        activeService: 0,
        faqTab: 'all',
        activeFaq: 0,
        mobileMenuOpen: false
    }"
    x-init="lucide.createIcons()"
>

    <!-- Liquid Background Ambient Blobs -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <div class="liquid-blob liquid-blob-crimson w-[550px] h-[550px] -top-32 -left-20"></div>
        <div class="liquid-blob liquid-blob-coral w-[600px] h-[600px] top-[35%] -right-32"></div>
        <div class="liquid-blob liquid-blob-crimson w-[500px] h-[500px] top-[70%] left-[10%]"></div>
    </div>

    <!-- 1. Sleek Frosted Glass Floating Navbar -->
    <header class="sticky top-0 z-50 glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand Logo (Editorial Style) -->
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-pitch flex items-center justify-center text-white font-display text-2xl group-hover:bg-crimson transition-colors duration-300 shadow-md">
                        PC
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="font-display text-2xl tracking-wide text-pitch">PIXELCRAFT</span>
                        <span class="w-2 h-2 rounded-full bg-crimson animate-pulse"></span>
                        <span class="ml-1 text-[11px] font-mono font-bold uppercase tracking-widest text-slate-500 hidden sm:inline-block">SaaS</span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-8 text-xs font-bold uppercase tracking-wider text-slate-600">
                    <a href="#services" class="hover:text-crimson transition-colors">Services</a>
                    <a href="#process" class="hover:text-crimson transition-colors">Our Process</a>
                    <a href="#cases" class="hover:text-crimson transition-colors">Case Studies</a>
                    <a href="#solar-erp" class="hover:text-crimson transition-colors">Solar PV ERP</a>
                    <a href="#pricing" class="hover:text-crimson transition-colors">Pricing in LKR</a>
                    <a href="#faq" class="hover:text-crimson transition-colors">FAQ</a>
                </nav>

                <!-- Auth Buttons -->
                <div class="flex items-center gap-4">
                    <a href="/app/login" class="text-xs font-bold uppercase tracking-wider text-pitch hover:text-crimson transition-colors hidden sm:block">
                        Sign In
                    </a>
                    <a href="/app/register" class="btn-crimson px-6 py-2.5 text-xs uppercase tracking-wider shadow-sm">
                        <span>Start Free Trial</span>
                        <span class="btn-arrow text-sm font-bold">↗</span>
                    </a>
                    <!-- Mobile Hamburger -->
                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="md:hidden text-pitch p-2 rounded-lg hover:bg-black/5"
                        aria-label="Toggle menu"
                    >
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Nav Dropdown -->
            <div
                x-show="mobileMenuOpen"
                x-transition
                @click.away="mobileMenuOpen = false"
                class="md:hidden py-4 border-t border-warmBorder space-y-3"
            >
                <a href="#services" @click="mobileMenuOpen = false" class="block text-sm font-bold text-slate-700 py-1">Services</a>
                <a href="#process" @click="mobileMenuOpen = false" class="block text-sm font-bold text-slate-700 py-1">Our Process</a>
                <a href="#cases" @click="mobileMenuOpen = false" class="block text-sm font-bold text-slate-700 py-1">Case Studies</a>
                <a href="#solar-erp" @click="mobileMenuOpen = false" class="block text-sm font-bold text-slate-700 py-1">Solar PV ERP</a>
                <a href="#pricing" @click="mobileMenuOpen = false" class="block text-sm font-bold text-slate-700 py-1">Pricing in LKR</a>
                <a href="#faq" @click="mobileMenuOpen = false" class="block text-sm font-bold text-slate-700 py-1">FAQ</a>
                <div class="pt-2 flex flex-col gap-2">
                    <a href="/app/login" class="text-sm font-bold text-pitch py-1">Sign In</a>
                    <a href="/app/register" class="btn-crimson py-2.5 text-xs uppercase tracking-wider text-center">Start Free Trial ↗</a>
                </div>
            </div>
        </div>
    </header>

    <main class="relative z-10">
        <!-- 2. The MarketingLab / GrowthLab Hero Section with Motion Reveal -->
        <section class="pt-8 sm:pt-14 pb-16 relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Top Meta Row (©2026 left, Sri Lanka right) -->
                <div class="flex items-center justify-between text-xs sm:text-sm font-mono font-bold uppercase tracking-wider text-pitch border-b border-warmBorder pb-4 mb-2">
                    <div class="flex items-center gap-2">
                        <span>©2026</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-crimson"></span>
                        <span class="text-slate-500 font-medium hidden sm:inline">COLOMBO, SRI LANKA</span>
                    </div>
                    <div class="tracking-widest text-pitch">
                        MULTI-TENANT CLOUD ERP & POS
                    </div>
                </div>

                <!-- Massive Tall Condensed Display Headline -->
                <div class="hero-giant-word select-none" data-motion-hero>
                    PIXELCRAFT
                </div>

                <!-- Hero Crimson Ambient Visual Card with Liquid Glow -->
                <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-[#FF3838] via-[#E53935] to-[#7F1D1D] text-white p-6 sm:p-12 lg:p-16 shadow-2xl" data-motion-card>
                    <!-- Subtle Geometric Dot Grid -->
                    <div class="absolute inset-0 opacity-15 pointer-events-none" style="background-image: radial-gradient(#FFFFFF 1px, transparent 1px); background-size: 24px 24px;"></div>
                    <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-red-400 rounded-full blur-3xl opacity-35 pointer-events-none"></div>

                    <!-- Hero Content Grid -->
                    <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                        <div class="lg:col-span-6 space-y-6">
                            <!-- Pill Tagline Badge -->
                            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/15 backdrop-blur-md border border-white/25 text-white text-xs font-bold tracking-wider uppercase">
                                <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                                14-Day Free Full Access • No Credit Card Required
                            </div>

                            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-display text-white leading-[1.05] tracking-wide">
                                Smart Cloud Accounting, POS & <span class="underline decoration-white/40 decoration-wavy">Solar ERP</span> for Sri Lanka.
                            </h1>

                            <p class="text-base sm:text-lg text-white/90 font-normal leading-relaxed max-w-xl">
                                Engineered specifically for Sri Lankan solar EPC contractors, electrical trade distributors, and retail merchants. Featuring CEB solar sizing, Zoho-style milestone invoicing, 80mm POS receipts, and EPF/ETF statutory payroll.
                            </p>

                            <!-- CTAs -->
                            <div class="pt-2 flex flex-wrap items-center gap-4">
                                <a href="/app/register" class="btn-ghost-pill px-8 py-4 text-base font-bold shadow-lg">
                                    <span class="text-pitch">Start Free 14-Day Trial</span>
                                    <span class="btn-arrow text-crimson font-black text-lg">↗</span>
                                </a>
                                <a href="/app/login" class="inline-flex items-center gap-2 px-7 py-4 rounded-full border border-white/40 bg-white/10 hover:bg-white/20 text-white font-bold text-base backdrop-blur-sm transition-all">
                                    <span>Sign In to Workspace</span>
                                    <span class="btn-arrow">↗</span>
                                </a>
                            </div>
                        </div>

                        <!-- Right: Interactive Hero Dashboard Mockup Image with Floating Badges -->
                        <div class="lg:col-span-6 relative">
                            <!-- Main Hero Image (Suitable Asset Added) -->
                            <div class="relative rounded-2xl overflow-hidden border border-white/25 shadow-2xl bg-black/40 group transform transition-transform duration-500 hover:scale-[1.02]">
                                <img
                                    src="{{ asset('images/landing/hero-dashboard.webp') }}"
                                    alt="PixelCraft Cloud ERP & Solar PV Analytics Dashboard"
                                    class="w-full h-auto object-cover"
                                    loading="eager"
                                    width="1280"
                                    height="720"
                                >
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-60 pointer-events-none"></div>

                                <!-- Real-Time KPI Bar on Image -->
                                <div class="absolute bottom-3 left-3 right-3 p-3.5 rounded-xl bg-black/70 backdrop-blur-md border border-white/20 text-white flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                        <div>
                                            <div class="font-bold text-white text-xs">485 kWp Managed</div>
                                            <div class="text-[10px] text-emerald-300 font-mono">CEB Net-Metering Sync</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-display text-sm tracking-wider text-white">Rs. 1.25M</div>
                                        <div class="text-[10px] text-red-300 font-bold uppercase">This Month</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Floating Badges Stack -->
                            <div class="absolute -top-4 -left-3 floating-pill hidden sm:inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-black/80 backdrop-blur-md border border-white/25 text-white text-xs font-bold shadow-xl">
                                <span>☀️ CEB & LECO Solar Sizing</span>
                            </div>
                            <div class="absolute -bottom-4 -right-3 floating-pill-delayed hidden sm:inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-black/80 backdrop-blur-md border border-white/25 text-white text-xs font-bold shadow-xl">
                                <span>🧾 80mm ESC/POS Direct Print</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Metrics Bar -->
                    <div class="mt-12 pt-8 border-t border-white/20 grid grid-cols-2 md:grid-cols-4 gap-6 text-white font-display">
                        <div>
                            <div class="text-4xl sm:text-5xl tracking-wide">14 Days</div>
                            <div class="text-xs font-sans text-white/80 font-bold tracking-wider uppercase mt-1">Full Free Trial</div>
                        </div>
                        <div>
                            <div class="text-4xl sm:text-5xl tracking-wide">Rs. 500M+</div>
                            <div class="text-xs font-sans text-white/80 font-bold tracking-wider uppercase mt-1">Invoices Generated</div>
                        </div>
                        <div>
                            <div class="text-4xl sm:text-5xl tracking-wide">220+</div>
                            <div class="text-xs font-sans text-white/80 font-bold tracking-wider uppercase mt-1">Active Workspaces</div>
                        </div>
                        <div>
                            <div class="text-4xl sm:text-5xl tracking-wide">0.2 sec</div>
                            <div class="text-xs font-sans text-white/80 font-bold tracking-wider uppercase mt-1">Thermal POS Speed</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. Infinite Smooth Marquee Ticker -->
        <div class="border-y border-warmBorder bg-creamLight py-5 overflow-hidden select-none">
            <div class="marquee-track flex items-center text-xs sm:text-sm font-mono font-bold uppercase tracking-widest text-pitch">
                <div class="flex items-center gap-8 shrink-0 px-4">
                    <span><span class="text-crimson font-black mr-2">/</span> SOLAR PV SIZING</span>
                    <span><span class="text-crimson font-black mr-2">/</span> ZOHO MILESTONE INVOICING</span>
                    <span><span class="text-crimson font-black mr-2">/</span> 80MM COUNTER POS</span>
                    <span><span class="text-crimson font-black mr-2">/</span> EPF 8%/12% & ETF 3% PAYROLL</span>
                    <span><span class="text-crimson font-black mr-2">/</span> CEB & LECO AGREEMENTS</span>
                    <span><span class="text-crimson font-black mr-2">/</span> COMMISSIONING CERTIFICATES</span>
                    <span><span class="text-crimson font-black mr-2">/</span> WARRANTY SERIAL TRACKING</span>
                    <span><span class="text-crimson font-black mr-2">/</span> COGS & SUPPLIER BILLS</span>
                    <span><span class="text-crimson font-black mr-2">/</span> AGENT COMMISSION PAYOUTS</span>
                </div>
                <div class="flex items-center gap-8 shrink-0 px-4">
                    <span><span class="text-crimson font-black mr-2">/</span> SOLAR PV SIZING</span>
                    <span><span class="text-crimson font-black mr-2">/</span> ZOHO MILESTONE INVOICING</span>
                    <span><span class="text-crimson font-black mr-2">/</span> 80MM COUNTER POS</span>
                    <span><span class="text-crimson font-black mr-2">/</span> EPF 8%/12% & ETF 3% PAYROLL</span>
                    <span><span class="text-crimson font-black mr-2">/</span> CEB & LECO AGREEMENTS</span>
                    <span><span class="text-crimson font-black mr-2">/</span> COMMISSIONING CERTIFICATES</span>
                    <span><span class="text-crimson font-black mr-2">/</span> WARRANTY SERIAL TRACKING</span>
                    <span><span class="text-crimson font-black mr-2">/</span> COGS & SUPPLIER BILLS</span>
                    <span><span class="text-crimson font-black mr-2">/</span> AGENT COMMISSION PAYOUTS</span>
                </div>
            </div>
        </div>

        <!-- 4. Client Logos / Trust Bar -->
        <section class="py-12 border-b border-warmBorder bg-cream">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center text-xs font-mono font-bold uppercase tracking-widest text-slate-500 mb-8">
                    COMPLIANT WITH SRI LANKAN NATIONAL STANDARDS & HARDWARE
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-6 items-center justify-items-center opacity-80">
                    <div class="font-display text-lg tracking-wider text-pitch flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-crimson"></span> CEB GRID COMPLIANT
                    </div>
                    <div class="font-display text-lg tracking-wider text-pitch flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-crimson"></span> LECO STANDARDS
                    </div>
                    <div class="font-display text-lg tracking-wider text-pitch flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-crimson"></span> ESC/POS 80MM
                    </div>
                    <div class="font-display text-lg tracking-wider text-pitch flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-crimson"></span> EPF & ETF DEPT
                    </div>
                    <div class="font-display text-lg tracking-wider text-pitch flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-crimson"></span> LKR NATIVE CURRENCY
                    </div>
                    <div class="font-display text-lg tracking-wider text-pitch flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-crimson"></span> MULTI-TENANT SAAS
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. "Our Process" — 4-Card Sequence in Deep Pitch Black #0A0A0A -->
        <section id="process" class="py-24 sm:py-32 bg-pitch text-white relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
                    <div>
                        <span class="text-xs font-mono font-bold uppercase tracking-widest text-crimson">
                            Disciplined Execution
                        </span>
                        <h2 class="text-4xl sm:text-6xl font-display text-white mt-3 tracking-wide">
                            OUR PROCESS
                        </h2>
                    </div>
                    <p class="text-sm sm:text-base text-slate-400 max-w-md font-normal leading-relaxed">
                        A disciplined four-step operational framework that eliminates billing guesswork, secures margins, and delivers predictable business growth.
                    </p>
                </div>

                <!-- 4 Glowing Process Cards (01, 02, 03, 04) with Stagger Motion -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Card 01 -->
                    <div class="process-card p-8 h-[440px] flex flex-col justify-between group" data-motion-process>
                        <div class="flex items-start justify-between">
                            <span class="text-6xl sm:text-7xl font-display text-white leading-none">01</span>
                            <span class="w-8 h-8 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white text-xs font-bold">✓</span>
                        </div>
                        <div class="space-y-3">
                            <h3 class="text-2xl font-display text-white tracking-wide group-hover:translate-x-1 transition-transform">
                                ONBOARD & BRAND
                            </h3>
                            <p class="text-xs sm:text-sm text-white/90 leading-relaxed font-normal">
                                Instant multi-tenant registration. Upload company logo, set branch locations, configure VAT numbers, and invite role-restricted staff in under 2 minutes.
                            </p>
                            <div class="pt-2">
                                <span class="text-[11px] font-mono font-bold uppercase tracking-wider text-white/80 bg-black/30 px-3 py-1 rounded-full">
                                    Step 01 • Configuration
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 02 -->
                    <div class="process-card p-8 h-[440px] flex flex-col justify-between group" style="background: linear-gradient(155deg, #FF4545 0%, #D32F2F 50%, #7F1D1D 100%);" data-motion-process>
                        <div class="flex items-start justify-between">
                            <span class="text-6xl sm:text-7xl font-display text-white leading-none">02</span>
                            <span class="w-8 h-8 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white text-xs font-bold">✓</span>
                        </div>
                        <div class="space-y-3">
                            <h3 class="text-2xl font-display text-white tracking-wide group-hover:translate-x-1 transition-transform">
                                ENGINEER & QUOTE
                            </h3>
                            <p class="text-xs sm:text-sm text-white/90 leading-relaxed font-normal">
                                Input customer CEB monthly electricity units for instant kWp solar sizing. Draft Zoho-style milestone proposals with exact inverter/panel bill of quantities.
                            </p>
                            <div class="pt-2">
                                <span class="text-[11px] font-mono font-bold uppercase tracking-wider text-white/80 bg-black/30 px-3 py-1 rounded-full">
                                    Step 02 • Estimation
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 03 -->
                    <div class="process-card p-8 h-[440px] flex flex-col justify-between group" style="background: linear-gradient(155deg, #FF3838 0%, #B91C1C 55%, #5C0E0E 100%);" data-motion-process>
                        <div class="flex items-start justify-between">
                            <span class="text-6xl sm:text-7xl font-display text-white leading-none">03</span>
                            <span class="w-8 h-8 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white text-xs font-bold">✓</span>
                        </div>
                        <div class="space-y-3">
                            <h3 class="text-2xl font-display text-white tracking-wide group-hover:translate-x-1 transition-transform">
                                BILL & TRANSACT
                            </h3>
                            <p class="text-xs sm:text-sm text-white/90 leading-relaxed font-normal">
                                Convert approved quotations into milestone invoices (60% advance, 30% delivery, 10% commissioning) or print instant 80mm ESC/POS counter receipts.
                            </p>
                            <div class="pt-2">
                                <span class="text-[11px] font-mono font-bold uppercase tracking-wider text-white/80 bg-black/30 px-3 py-1 rounded-full">
                                    Step 03 • Transaction
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 04 -->
                    <div class="process-card p-8 h-[440px] flex flex-col justify-between group" style="background: linear-gradient(155deg, #FF5C5C 0%, #D32F2F 50%, #3D0909 100%);" data-motion-process>
                        <div class="flex items-start justify-between">
                            <span class="text-6xl sm:text-7xl font-display text-white leading-none">04</span>
                            <span class="w-8 h-8 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white text-xs font-bold">✓</span>
                        </div>
                        <div class="space-y-3">
                            <h3 class="text-2xl font-display text-white tracking-wide group-hover:translate-x-1 transition-transform">
                                SETTLE & PROFIT
                            </h3>
                            <p class="text-xs sm:text-sm text-white/90 leading-relaxed font-normal">
                                Issue official A4 Commissioning Certificates, vault serial numbers for warranty claims, generate EPF/ETF pay slips, and calculate net project margins.
                            </p>
                            <div class="pt-2">
                                <span class="text-[11px] font-mono font-bold uppercase tracking-wider text-white/80 bg-black/30 px-3 py-1 rounded-full">
                                    Step 04 • Settlement
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. Interactive Services Section (GrowthLab Expandable Style) -->
        <section id="services" class="py-24 sm:py-32 bg-cream">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Heading -->
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
                    <div>
                        <span class="text-xs font-mono font-bold uppercase tracking-widest text-crimson">
                            CAPABILITIES & SOLUTIONS
                        </span>
                        <h2 class="text-4xl sm:text-6xl font-display text-pitch mt-3 tracking-wide">
                            WHAT WE OFFER / SERVICES
                        </h2>
                    </div>
                    <p class="text-sm sm:text-base text-slate-600 max-w-md font-normal leading-relaxed">
                        End-to-end cloud infrastructure engineered to build, run, and scale your business without relying on multiple disconnected apps.
                    </p>
                </div>

                <!-- Interactive Services Accordion Rows -->
                <div class="space-y-4">
                    <!-- Service 01 -->
                    <div
                        class="bento-card p-6 sm:p-8 cursor-pointer transition-all duration-300 liquid-reflection"
                        :class="activeService === 0 ? 'border-crimson shadow-lg ring-2 ring-crimson/20' : 'hover:border-slate-400'"
                        @click="activeService = activeService === 0 ? -1 : 0"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-6">
                                <span class="font-display text-2xl sm:text-3xl text-crimson">01</span>
                                <div>
                                    <span class="text-[11px] font-mono font-bold uppercase tracking-wider text-slate-500">Engineering Service</span>
                                    <h3 class="text-xl sm:text-3xl font-display text-pitch tracking-wide">
                                        SOLAR PV SIZING & BOQ GENERATOR
                                    </h3>
                                </div>
                            </div>
                            <span class="text-2xl font-light text-crimson transition-transform duration-300" :class="activeService === 0 ? 'rotate-45' : ''">+</span>
                        </div>
                        <div x-show="activeService === 0" x-collapse class="pt-6 mt-6 border-t border-warmBorder grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                            <div class="md:col-span-8 text-sm text-slate-600 leading-relaxed space-y-3">
                                <p>
                                    Algorithmic kWp capacity calculator based on Sri Lankan CEB/LECO electricity bills. Automatically determines Tier-1 panel wattage, inverter capacities, and single or three-phase connections.
                                </p>
                                <p class="text-xs font-mono text-slate-500">
                                    Generates customer agreements, inverter technical specifications, bill of quantities, and gate passes in A4 PDF format.
                                </p>
                            </div>
                            <div class="md:col-span-4 flex justify-start md:justify-end">
                                <a href="/app/register" class="btn-crimson px-6 py-3 text-xs uppercase tracking-wider">
                                    <span>Try Solar Sizing</span>
                                    <span class="btn-arrow font-bold">↗</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Service 02 -->
                    <div
                        class="bento-card p-6 sm:p-8 cursor-pointer transition-all duration-300 liquid-reflection"
                        :class="activeService === 1 ? 'border-crimson shadow-lg ring-2 ring-crimson/20' : 'hover:border-slate-400'"
                        @click="activeService = activeService === 1 ? -1 : 1"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-6">
                                <span class="font-display text-2xl sm:text-3xl text-crimson">02</span>
                                <div>
                                    <span class="text-[11px] font-mono font-bold uppercase tracking-wider text-slate-500">Billing Engine</span>
                                    <h3 class="text-xl sm:text-3xl font-display text-pitch tracking-wide">
                                        ZOHO-STYLE MILESTONE INVOICING
                                    </h3>
                                </div>
                            </div>
                            <span class="text-2xl font-light text-crimson transition-transform duration-300" :class="activeService === 1 ? 'rotate-45' : ''">+</span>
                        </div>
                        <div x-show="activeService === 1" x-collapse class="pt-6 mt-6 border-t border-warmBorder grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                            <div class="md:col-span-8 text-sm text-slate-600 leading-relaxed space-y-3">
                                <p>
                                    Split-pane live invoice editor with real-time A4 PDF preview. Seamlessly handle commercial EPC milestone billing: 60% Advance, 30% Material Delivery, and 10% Testing & Grid Commissioning.
                                </p>
                                <p class="text-xs font-mono text-slate-500">
                                    Includes instant WhatsApp dispatch, email delivery, and multi-tenant custom logo branding.
                                </p>
                            </div>
                            <div class="md:col-span-4 flex justify-start md:justify-end">
                                <a href="/app/register" class="btn-crimson px-6 py-3 text-xs uppercase tracking-wider">
                                    <span>Try Milestone Invoicing</span>
                                    <span class="btn-arrow font-bold">↗</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Service 03 -->
                    <div
                        class="bento-card p-6 sm:p-8 cursor-pointer transition-all duration-300 liquid-reflection"
                        :class="activeService === 2 ? 'border-crimson shadow-lg ring-2 ring-crimson/20' : 'hover:border-slate-400'"
                        @click="activeService = activeService === 2 ? -1 : 2"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-6">
                                <span class="font-display text-2xl sm:text-3xl text-crimson">03</span>
                                <div>
                                    <span class="text-[11px] font-mono font-bold uppercase tracking-wider text-slate-500">Retail & Hardware</span>
                                    <h3 class="text-xl sm:text-3xl font-display text-pitch tracking-wide">
                                        80MM COUNTER THERMAL POS
                                    </h3>
                                </div>
                            </div>
                            <span class="text-2xl font-light text-crimson transition-transform duration-300" :class="activeService === 2 ? 'rotate-45' : ''">+</span>
                        </div>
                        <div x-show="activeService === 2" x-collapse class="pt-6 mt-6 border-t border-warmBorder grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                            <div class="md:col-span-8 text-sm text-slate-600 leading-relaxed space-y-3">
                                <p>
                                    0.2-second cashier checkout terminal built for Sri Lankan hardware counters and trade shops. Rapid barcode scanner input, cash tender change computation, and raw ESC/POS thermal printing.
                                </p>
                                <p class="text-xs font-mono text-slate-500">
                                    Supports USB, Bluetooth, and network thermal printers commonly used across Sri Lanka.
                                </p>
                            </div>
                            <div class="md:col-span-4 flex justify-start md:justify-end">
                                <a href="/app/register" class="btn-crimson px-6 py-3 text-xs uppercase tracking-wider">
                                    <span>Try Counter POS</span>
                                    <span class="btn-arrow font-bold">↗</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Service 04 -->
                    <div
                        class="bento-card p-6 sm:p-8 cursor-pointer transition-all duration-300 liquid-reflection"
                        :class="activeService === 3 ? 'border-crimson shadow-lg ring-2 ring-crimson/20' : 'hover:border-slate-400'"
                        @click="activeService = activeService === 3 ? -1 : 3"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-6">
                                <span class="font-display text-2xl sm:text-3xl text-crimson">04</span>
                                <div>
                                    <span class="text-[11px] font-mono font-bold uppercase tracking-wider text-slate-500">Statutory HR</span>
                                    <h3 class="text-xl sm:text-3xl font-display text-pitch tracking-wide">
                                        EPF & ETF STATUTORY PAYROLL
                                    </h3>
                                </div>
                            </div>
                            <span class="text-2xl font-light text-crimson transition-transform duration-300" :class="activeService === 3 ? 'rotate-45' : ''">+</span>
                        </div>
                        <div x-show="activeService === 3" x-collapse class="pt-6 mt-6 border-t border-warmBorder grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                            <div class="md:col-span-8 text-sm text-slate-600 leading-relaxed space-y-3">
                                <p>
                                    Automated monthly payroll runs strictly adhering to Sri Lanka Department of Labour statutory rules. Computes employee EPF 8%, employer EPF 12%, employer ETF 3%, and generates professional printable payslips.
                                </p>
                                <p class="text-xs font-mono text-slate-500">
                                    Eliminate human calculation errors and keep your business 100% compliant with labor audits.
                                </p>
                            </div>
                            <div class="md:col-span-4 flex justify-start md:justify-end">
                                <a href="/app/register" class="btn-crimson px-6 py-3 text-xs uppercase tracking-wider">
                                    <span>Try Payroll Module</span>
                                    <span class="btn-arrow font-bold">↗</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Service 05 -->
                    <div
                        class="bento-card p-6 sm:p-8 cursor-pointer transition-all duration-300 liquid-reflection"
                        :class="activeService === 4 ? 'border-crimson shadow-lg ring-2 ring-crimson/20' : 'hover:border-slate-400'"
                        @click="activeService = activeService === 4 ? -1 : 4"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-6">
                                <span class="font-display text-2xl sm:text-3xl text-crimson">05</span>
                                <div>
                                    <span class="text-[11px] font-mono font-bold uppercase tracking-wider text-slate-500">Financial Governance</span>
                                    <h3 class="text-xl sm:text-3xl font-display text-pitch tracking-wide">
                                        MARGIN CONFIDENTIALITY & COGS VAULT
                                    </h3>
                                </div>
                            </div>
                            <span class="text-2xl font-light text-crimson transition-transform duration-300" :class="activeService === 4 ? 'rotate-45' : ''">+</span>
                        </div>
                        <div x-show="activeService === 4" x-collapse class="pt-6 mt-6 border-t border-warmBorder grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                            <div class="md:col-span-8 text-sm text-slate-600 leading-relaxed space-y-3">
                                <p>
                                    Role-based security isolation. Protect sensitive equipment cost prices (COGS), supplier bills, and profit metrics from cashier and sales staff. Only verified company owners have administrative visibility.
                                </p>
                                <p class="text-xs font-mono text-slate-500">
                                    Includes tamper-resistant document immutability upon customer dispatch.
                                </p>
                            </div>
                            <div class="md:col-span-4 flex justify-start md:justify-end">
                                <a href="/app/register" class="btn-crimson px-6 py-3 text-xs uppercase tracking-wider">
                                    <span>Explore Governance</span>
                                    <span class="btn-arrow font-bold">↗</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 7. Case Studies Grid (Suitable Visual Assets Added) -->
        <section id="cases" class="py-24 sm:py-32 bg-creamLight border-y border-warmBorder">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
                    <div>
                        <span class="text-xs font-mono font-bold uppercase tracking-widest text-crimson">
                            PROVEN IMPACT
                        </span>
                        <h2 class="text-4xl sm:text-6xl font-display text-pitch mt-3 tracking-wide">
                            SELECTED CASE STUDIES
                        </h2>
                    </div>
                    <p class="text-sm sm:text-base text-slate-600 max-w-md font-normal leading-relaxed">
                        How leading Sri Lankan solar EPC contractors and retail stores streamlined billing and scaled profitability.
                    </p>
                </div>

                <!-- 2-Column Showcase Cards with Real Photography -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Case Study 01: Solar EPC (Real Image Embedded) -->
                    <div class="bento-card overflow-hidden group shadow-lg" data-motion-case>
                        <!-- Top Case Study Photographic Mockup -->
                        <div class="relative h-64 sm:h-72 overflow-hidden bg-pitch">
                            <img
                                src="{{ asset('images/landing/case-solar-epc.webp') }}"
                                alt="Vertex Solar Commercial Rooftop PV Installation"
                                class="w-full h-full object-cover transform transition-transform duration-700 group-hover:scale-105"
                                loading="lazy"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-pitch via-pitch/40 to-transparent"></div>
                            <div class="absolute top-4 left-4 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-black/70 backdrop-blur-md border border-white/20 text-white text-xs font-mono">
                                <span class="w-2 h-2 rounded-full bg-crimson"></span>
                                SOLAR EPC SECTOR • COLOMBO 07
                            </div>
                        </div>

                        <!-- Content Body -->
                        <div class="p-8 bg-pitch text-white">
                            <h3 class="text-3xl font-display tracking-wide text-white group-hover:text-crimson transition-colors">
                                VERTEX SOLAR RESIDENCIES
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed mt-2">
                                Transitioned from fragmented Excel spreadsheets to automated CEB solar PV sizing and Zoho milestone billing. Reduced invoice cycle time by 45%.
                            </p>
                            <div class="mt-6 pt-6 border-t border-white/10 grid grid-cols-3 gap-4 font-display">
                                <div>
                                    <div class="text-2xl sm:text-3xl text-crimson">485 kWp</div>
                                    <div class="text-[10px] font-sans text-slate-400 uppercase">Installed</div>
                                </div>
                                <div>
                                    <div class="text-2xl sm:text-3xl text-white">45%</div>
                                    <div class="text-[10px] font-sans text-slate-400 uppercase">Faster Billing</div>
                                </div>
                                <div>
                                    <div class="text-2xl sm:text-3xl text-emerald-400">100%</div>
                                    <div class="text-[10px] font-sans text-slate-400 uppercase">CEB Approval</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Case Study 02: Retail POS (Real Image Embedded) -->
                    <div class="bento-card overflow-hidden group shadow-lg" data-motion-case>
                        <!-- Top Case Study Photographic Mockup -->
                        <div class="relative h-64 sm:h-72 overflow-hidden bg-pitch">
                            <img
                                src="{{ asset('images/landing/case-pos-trade.webp') }}"
                                alt="Electra Wholesale Counter POS Terminal and Thermal Printer"
                                class="w-full h-full object-cover transform transition-transform duration-700 group-hover:scale-105"
                                loading="lazy"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-pitch via-pitch/40 to-transparent"></div>
                            <div class="absolute top-4 left-4 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-black/70 backdrop-blur-md border border-white/20 text-white text-xs font-mono">
                                <span class="w-2 h-2 rounded-full bg-crimson"></span>
                                ELECTRICAL & TRADE RETAIL • KANDY
                            </div>
                        </div>

                        <!-- Content Body -->
                        <div class="p-8 bg-pitch text-white">
                            <h3 class="text-3xl font-display tracking-wide text-white group-hover:text-crimson transition-colors">
                                ELECTRA WHOLESALE DISTRIBUTORS
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed mt-2">
                                Implemented 80mm ESC/POS counter terminal across 3 trade branches. Handled 12,000+ monthly retail transactions with raw thermal print speeds under 0.2s.
                            </p>
                            <div class="mt-6 pt-6 border-t border-white/10 grid grid-cols-3 gap-4 font-display">
                                <div>
                                    <div class="text-2xl sm:text-3xl text-crimson">12K+</div>
                                    <div class="text-[10px] font-sans text-slate-400 uppercase">Monthly Bills</div>
                                </div>
                                <div>
                                    <div class="text-2xl sm:text-3xl text-white">0.2s</div>
                                    <div class="text-[10px] font-sans text-slate-400 uppercase">Receipt Latency</div>
                                </div>
                                <div>
                                    <div class="text-2xl sm:text-3xl text-emerald-400">0 Err</div>
                                    <div class="text-[10px] font-sans text-slate-400 uppercase">Cash Tender</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 8. Solar EPC Deep Dive & Certificate Showcase -->
        <section id="solar-erp" class="py-24 sm:py-32 bg-cream">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                    <div class="lg:col-span-6 space-y-6">
                        <span class="text-xs font-mono font-bold uppercase tracking-widest text-crimson bg-crimsonSubtle px-3.5 py-1.5 rounded-full border border-red-200">
                            SOLAR EPC SPECIALIZED ENGINE
                        </span>
                        <h2 class="text-3xl sm:text-5xl font-display text-pitch leading-tight tracking-wide">
                            OFFICIAL COMMISSIONING CERTIFICATES & SERIAL TRACKING
                        </h2>
                        <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                            Never misplace serial numbers for high-value solar modules, inverters, or battery banks. PixelCraft SaaS links equipment barcodes directly to customer invoices and produces certified Commissioning Certificates ready for CEB grid approvals.
                        </p>

                        <div class="space-y-3 pt-2">
                            <div class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-crimson text-white flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">✓</span>
                                <div class="text-xs sm:text-sm text-pitch font-medium">
                                    <strong class="font-bold">A4 & A5 Certificate Generator:</strong> Instant executive solar system commissioning certificates with verification QR codes.
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-crimson text-white flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">✓</span>
                                <div class="text-xs sm:text-sm text-pitch font-medium">
                                    <strong class="font-bold">Warranty Serial Vault:</strong> Track individual panel & inverter serial numbers for rapid warranty claims without searching through filing cabinets.
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-crimson text-white flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">✓</span>
                                <div class="text-xs sm:text-sm text-pitch font-medium">
                                    <strong class="font-bold">Freelance Promoter Vouchers:</strong> 1-click A5 commission settlement vouchers for external solar sales promoters and lead agents.
                                </div>
                            </div>
                        </div>

                        <div class="pt-4">
                            <a href="/app/register" class="btn-crimson px-8 py-3.5 text-xs uppercase tracking-wider">
                                <span>Get Started with Solar EPC Module</span>
                                <span class="btn-arrow text-base font-bold">↗</span>
                            </a>
                        </div>
                    </div>

                    <!-- Certificate Mockup Visual Card -->
                    <div class="lg:col-span-6">
                        <div class="bento-card p-6 sm:p-8 relative bg-gradient-to-br from-creamLight to-cream shadow-xl liquid-reflection">
                            <div class="border-2 border-dashed border-warmBorder rounded-2xl p-6 sm:p-8 bg-white text-center space-y-4">
                                <div class="flex items-center justify-between border-b border-warmBorder pb-3 text-xs text-slate-500">
                                    <span class="font-mono font-bold text-crimson">CERTIFICATE #SL-SOL-2026-089</span>
                                    <span class="bg-emerald-50 text-emerald-700 font-bold px-2.5 py-0.5 rounded-full border border-emerald-200 text-[10px]">VERIFIED FOR CEB</span>
                                </div>

                                <div class="pt-2">
                                    <div class="text-[11px] font-mono font-bold text-slate-500 uppercase tracking-widest">OFFICIAL COMPLETION & COMMISSIONING</div>
                                    <h3 class="text-2xl sm:text-3xl font-display text-pitch mt-1 tracking-wide">SOLAR PV POWER SYSTEM</h3>
                                    <p class="text-xs text-slate-600 mt-1">Issued to Project Owner: <strong class="text-pitch">Vertex Luxury Residencies Ltd</strong></p>
                                </div>

                                <div class="inline-flex items-center gap-2 bg-pitch text-white px-4 py-2 rounded-xl text-xs font-mono font-bold">
                                    <span>☀️ 15.0 kWp On-Grid System</span>
                                    <span class="text-white/40">•</span>
                                    <span>28x 550W Tier-1 Mono PERC</span>
                                </div>

                                <div class="grid grid-cols-2 gap-3 text-left pt-2 text-xs font-mono">
                                    <div class="bg-creamLight p-2.5 rounded-lg border border-warmBorder">
                                        <div class="text-[10px] text-slate-500 font-bold">INVERTER S/N</div>
                                        <div class="font-bold text-pitch truncate">SUNGROW-2026-9812A</div>
                                    </div>
                                    <div class="bg-creamLight p-2.5 rounded-lg border border-warmBorder">
                                        <div class="text-[10px] text-slate-500 font-bold">GRID APPROVAL</div>
                                        <div class="font-bold text-emerald-600">CEB NET-METERING</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 9. Testimonials Section (Suitable Portrait & Floating Reviews Added) -->
        <section class="py-24 sm:py-32 bg-pitch text-white relative overflow-hidden">
            <!-- Liquid Ambient Mesh in Testimonials -->
            <div class="liquid-blob liquid-blob-crimson w-[500px] h-[500px] top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-30"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <!-- Section Header -->
                <div class="text-center max-w-3xl mx-auto space-y-3 mb-16">
                    <span class="text-xs font-mono font-bold uppercase tracking-widest text-crimson">
                        CUSTOMER STORIES
                    </span>
                    <h2 class="text-4xl sm:text-6xl font-display text-white tracking-wide">
                        TRUSTED BY SRI LANKAN FOUNDERS
                    </h2>
                </div>

                <!-- Centerpiece Layout: Portrait with Floating Reviews -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center mb-12">
                    <!-- Left Floating Review Card -->
                    <div class="lg:col-span-4 space-y-6">
                        <div class="bg-pitchSurface border border-pitchBorder p-6 sm:p-8 rounded-3xl relative backdrop-blur-xl shadow-2xl" data-motion-review>
                            <div class="flex items-center gap-1 text-crimson text-sm mb-3">
                                <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-200 leading-relaxed font-normal">
                                "The CEB solar PV sizing algorithm saved our engineering team dozens of hours every week. Generating milestone proposals and A4 Commissioning Certificates directly from one platform is revolutionary for EPC contractors in Sri Lanka."
                            </p>
                            <div class="pt-4 mt-4 border-t border-pitchBorder flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-crimson flex items-center justify-center font-display text-white text-lg">
                                    RK
                                </div>
                                <div>
                                    <div class="font-bold text-sm text-white">Rohan Kariyawasam</div>
                                    <div class="text-xs text-slate-400">Managing Director, SunPro Solar Ltd</div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-pitchSurface border border-pitchBorder p-6 sm:p-8 rounded-3xl relative backdrop-blur-xl shadow-2xl" data-motion-review>
                            <div class="flex items-center gap-1 text-crimson text-sm mb-3">
                                <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-200 leading-relaxed font-normal">
                                "The EPF/ETF payroll module is 100% compliant with Sri Lanka Department of Labour rules. We ran our monthly salaries, generated printable slips, and settled agent vouchers in under 15 minutes."
                            </p>
                            <div class="pt-4 mt-4 border-t border-pitchBorder flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-crimson flex items-center justify-center font-display text-white text-lg">
                                    MS
                                </div>
                                <div>
                                    <div class="font-bold text-sm text-white">Malith Samarasinghe</div>
                                    <div class="text-xs text-slate-400">Head of Operations, Apex Engineering</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Center Executive Portrait (Suitable Image Added) -->
                    <div class="lg:col-span-4 flex justify-center">
                        <div class="relative rounded-3xl overflow-hidden border-2 border-crimson/50 shadow-[0_0_50px_rgba(255,56,56,0.3)] max-w-sm w-full group">
                            <img
                                src="{{ asset('images/landing/testimonial-founder.webp') }}"
                                alt="Sri Lankan Enterprise Founder using PixelCraft SaaS"
                                class="w-full h-auto object-cover transform transition-transform duration-700 group-hover:scale-105"
                                loading="lazy"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-70"></div>
                            <div class="absolute bottom-5 left-5 right-5 text-center">
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-crimson text-white text-[11px] font-mono font-bold uppercase tracking-wider mb-2">
                                    ★ Featured Customer
                                </div>
                                <h4 class="font-display text-xl tracking-wide text-white">Rohan Kariyawasam</h4>
                                <p class="text-xs text-slate-300">Installed 485+ kWp CEB Grid Systems</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right Floating Review Card -->
                    <div class="lg:col-span-4 space-y-6">
                        <div class="bg-pitchSurface border-2 border-crimson/60 p-6 sm:p-8 rounded-3xl relative backdrop-blur-xl shadow-[0_0_35px_rgba(255,56,56,0.25)]" data-motion-review>
                            <div class="flex items-center gap-1 text-crimson text-sm mb-3">
                                <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-100 leading-relaxed font-normal">
                                "Our trade counter processes over 400 customer walk-ins every Saturday. PixelCraft's 80mm thermal POS prints instantaneously without browser dialog lag. Margin confidentiality keeps our wholesale costs safe."
                            </p>
                            <div class="pt-4 mt-4 border-t border-pitchBorder flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-crimson to-red-800 flex items-center justify-center font-display text-white text-lg">
                                    DN
                                </div>
                                <div>
                                    <div class="font-bold text-sm text-white">Dinesh Nanayakkara</div>
                                    <div class="text-xs text-slate-400">Owner, Colombo Electrical Center</div>
                                </div>
                            </div>
                        </div>

                        <!-- Mini Trust Box -->
                        <div class="bg-pitchSurface/60 border border-pitchBorder p-6 rounded-3xl text-center">
                            <div class="font-display text-3xl text-crimson">99.9% UPTIME</div>
                            <p class="text-xs text-slate-400 mt-1">Multi-Tenant Database Isolation</p>
                            <div class="mt-3 flex items-center justify-center gap-1 text-emerald-400 text-xs font-mono">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                Sri Lanka Cloud Node Live
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 10. Dynamic Pricing Section in LKR (with Monthly / Annual Switcher) -->
        <section id="pricing" class="py-24 sm:py-32 bg-cream border-t border-warmBorder">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="text-center max-w-3xl mx-auto space-y-4 mb-14">
                    <span class="text-xs font-mono font-bold uppercase tracking-widest text-crimson bg-crimsonSubtle px-3.5 py-1.5 rounded-full border border-red-200">
                        PREDICTABLE PRICING IN LKR
                    </span>
                    <h2 class="text-4xl sm:text-6xl font-display text-pitch tracking-wide">
                        SIMPLE PLANS FOR SRI LANKAN BUSINESSES
                    </h2>
                    <p class="text-sm sm:text-base text-slate-600">
                        Every plan includes a 14-day risk-free trial. Upgrade or renew conveniently via local bank transfer or card.
                    </p>

                    <!-- Monthly vs Annual Interactive Switcher -->
                    <div class="pt-6 flex items-center justify-center">
                        <div class="bg-creamLight p-1.5 rounded-full inline-flex items-center gap-1 border border-warmBorder shadow-inner">
                            <button
                                type="button"
                                class="px-6 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider transition-all"
                                :class="billing === 'monthly' ? 'bg-pitch text-white shadow-md' : 'text-slate-600 hover:text-pitch'"
                                @click="billing = 'monthly'"
                            >
                                Monthly Billing
                            </button>
                            <button
                                type="button"
                                class="px-6 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider transition-all"
                                :class="billing === 'yearly' ? 'bg-pitch text-white shadow-md' : 'text-slate-600 hover:text-pitch'"
                                @click="billing = 'yearly'"
                            >
                                <span>Annual Billing</span>
                                <span class="ml-1 text-[10px] bg-crimson text-white font-bold px-2 py-0.5 rounded-full">Save 2 Mo.</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 3 Pricing Cards with Framer Motion Entrance -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto items-stretch">
                    @foreach ($plans as $plan)
                        @php
                            $isPopular = $plan->slug === 'professional';
                        @endphp
                        <div
                            class="p-8 flex flex-col justify-between relative transition-all duration-300 rounded-3xl {{ $isPopular ? 'bg-pitch text-white border-2 border-crimson ring-8 ring-red-500/10 shadow-2xl scale-[1.03] z-10' : 'bento-card text-pitch' }}"
                            data-motion-pricing
                        >
                            @if ($isPopular)
                                <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-crimson text-white text-[11px] font-display uppercase tracking-wider px-4 py-1 rounded-full shadow-md">
                                    ★ Most Popular for EPC
                                </div>
                            @endif

                            <div>
                                <h3 class="text-2xl font-display tracking-wide {{ $isPopular ? 'text-white' : 'text-pitch' }}">
                                    {{ $plan->name }}
                                </h3>
                                <p class="text-xs mt-1 {{ $isPopular ? 'text-slate-400' : 'text-slate-500' }}">
                                    {{ $plan->slug === 'starter' ? 'Ideal for solo installers & retail counters' : ($plan->slug === 'professional' ? 'Complete EPC ERP for growing contractors' : 'Unlimited multi-branch corporate governance') }}
                                </p>

                                <!-- Dynamic Price Display -->
                                <div class="mt-6 flex items-baseline gap-1">
                                    <span class="text-4xl sm:text-5xl font-display tracking-wide {{ $isPopular ? 'text-white' : 'text-pitch' }}">
                                        <template x-if="billing === 'monthly'">
                                            <span>Rs. {{ number_format($plan->price_monthly, 0) }}</span>
                                        </template>
                                        <template x-if="billing === 'yearly'">
                                            <span>Rs. {{ number_format(round($plan->price_yearly / 12), 0) }}</span>
                                        </template>
                                    </span>
                                    <span class="text-xs font-semibold {{ $isPopular ? 'text-slate-400' : 'text-slate-500' }}" x-text="billing === 'monthly' ? '/ month' : '/ mo (billed yearly)'"></span>
                                </div>

                                <div class="text-[11px] font-mono font-bold mt-1 text-crimson">
                                    <span x-show="billing === 'yearly'">Billed annually: Rs. {{ number_format($plan->price_yearly, 0) }} (Save ~17%)</span>
                                    <span x-show="billing === 'monthly'">or Rs. {{ number_format($plan->price_yearly, 0) }} paid annually</span>
                                </div>

                                <!-- Features List -->
                                <div class="mt-8 pt-6 border-t {{ $isPopular ? 'border-white/10' : 'border-warmBorder' }} space-y-3">
                                    <div class="text-xs font-mono font-bold uppercase tracking-wider {{ $isPopular ? 'text-slate-400' : 'text-slate-500' }}">
                                        Features Included:
                                    </div>
                                    @if (is_array($plan->features))
                                        @foreach ($plan->features as $feature)
                                            <div class="flex items-center gap-2.5 text-xs {{ $isPopular ? 'text-slate-200' : 'text-pitch' }}">
                                                <span class="text-crimson font-black text-sm">✓</span>
                                                <span>{{ $feature }}</span>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="flex items-center gap-2.5 text-xs {{ $isPopular ? 'text-slate-200' : 'text-pitch' }}">
                                            <span class="text-crimson font-black text-sm">✓</span>
                                            <span>Max Users: {{ $plan->max_users }}</span>
                                        </div>
                                        <div class="flex items-center gap-2.5 text-xs {{ $isPopular ? 'text-slate-200' : 'text-pitch' }}">
                                            <span class="text-crimson font-black text-sm">✓</span>
                                            <span>Max Invoices: {{ $plan->max_invoices }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- CTA Button -->
                            <div class="mt-8 pt-6 border-t {{ $isPopular ? 'border-white/10' : 'border-warmBorder' }}">
                                <a
                                    href="/app/register"
                                    class="{{ $isPopular ? 'btn-crimson' : 'btn-ghost-pill' }} w-full py-3.5 text-center text-xs uppercase tracking-wider font-bold block"
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

        <!-- 11. Interactive FAQ with Filter Tabs -->
        <section id="faq" class="py-24 sm:py-32 bg-cream">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center space-y-3 mb-12">
                    <span class="text-xs font-mono font-bold uppercase tracking-widest text-crimson">
                        COMMON QUESTIONS
                    </span>
                    <h2 class="text-4xl sm:text-6xl font-display text-pitch tracking-wide">
                        FREQUENTLY ASKED QUESTIONS
                    </h2>
                </div>

                <!-- FAQ Filter Tabs -->
                <div class="flex flex-wrap items-center justify-center gap-2 mb-10">
                    <button
                        type="button"
                        class="px-5 py-2 rounded-full text-xs font-mono font-bold uppercase transition-all"
                        :class="faqTab === 'all' ? 'bg-pitch text-white' : 'bg-white border border-warmBorder text-slate-600 hover:border-pitch'"
                        @click="faqTab = 'all'"
                    >
                        All Questions
                    </button>
                    <button
                        type="button"
                        class="px-5 py-2 rounded-full text-xs font-mono font-bold uppercase transition-all"
                        :class="faqTab === 'general' ? 'bg-pitch text-white' : 'bg-white border border-warmBorder text-slate-600 hover:border-pitch'"
                        @click="faqTab = 'general'"
                    >
                        Trial & General
                    </button>
                    <button
                        type="button"
                        class="px-5 py-2 rounded-full text-xs font-mono font-bold uppercase transition-all"
                        :class="faqTab === 'solar' ? 'bg-pitch text-white' : 'bg-white border border-warmBorder text-slate-600 hover:border-pitch'"
                        @click="faqTab = 'solar'"
                    >
                        Solar EPC
                    </button>
                    <button
                        type="button"
                        class="px-5 py-2 rounded-full text-xs font-mono font-bold uppercase transition-all"
                        :class="faqTab === 'pos' ? 'bg-pitch text-white' : 'bg-white border border-warmBorder text-slate-600 hover:border-pitch'"
                        @click="faqTab = 'pos'"
                    >
                        POS & Security
                    </button>
                </div>

                <!-- FAQ Items -->
                <div class="space-y-4">
                    <!-- FAQ 1 -->
                    <div
                        class="bento-card p-6 cursor-pointer"
                        x-show="faqTab === 'all' || faqTab === 'general'"
                        @click="activeFaq = activeFaq === 0 ? -1 : 0"
                    >
                        <div class="flex items-center justify-between">
                            <h3 class="text-base sm:text-lg font-bold text-pitch">
                                How does the 14-day free trial work?
                            </h3>
                            <span class="text-2xl font-light text-crimson transition-transform duration-300" :class="activeFaq === 0 ? 'rotate-45' : ''">+</span>
                        </div>
                        <div x-show="activeFaq === 0" x-collapse class="text-xs sm:text-sm text-slate-600 leading-relaxed pt-3">
                            You get immediate, unrestricted access to the complete PixelCraft SaaS platform including Solar PV sizing, milestone invoicing, and POS. No credit card is required to register. After 14 days, you can choose any LKR subscription plan.
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div
                        class="bento-card p-6 cursor-pointer"
                        x-show="faqTab === 'all' || faqTab === 'solar'"
                        @click="activeFaq = activeFaq === 1 ? -1 : 1"
                    >
                        <div class="flex items-center justify-between">
                            <h3 class="text-base sm:text-lg font-bold text-pitch">
                                Is the Solar PV sizing tool compliant with CEB & LECO?
                            </h3>
                            <span class="text-2xl font-light text-crimson transition-transform duration-300" :class="activeFaq === 1 ? 'rotate-45' : ''">+</span>
                        </div>
                        <div x-show="activeFaq === 1" x-collapse class="text-xs sm:text-sm text-slate-600 leading-relaxed pt-3">
                            Yes. Our sizing algorithms calculate required system kilowatt-peak (kWp) against Sri Lankan solar irradiance factors, CEB single/three-phase thresholds, and Net-Metering/Net-Plus policies.
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div
                        class="bento-card p-6 cursor-pointer"
                        x-show="faqTab === 'all' || faqTab === 'pos'"
                        @click="activeFaq = activeFaq === 2 ? -1 : 2"
                    >
                        <div class="flex items-center justify-between">
                            <h3 class="text-base sm:text-lg font-bold text-pitch">
                                Can I use standard 80mm ESC/POS thermal printers?
                            </h3>
                            <span class="text-2xl font-light text-crimson transition-transform duration-300" :class="activeFaq === 2 ? 'rotate-45' : ''">+</span>
                        </div>
                        <div x-show="activeFaq === 2" x-collapse class="text-xs sm:text-sm text-slate-600 leading-relaxed pt-3">
                            Absolutely. The counter POS module formats receipts specifically for 80mm thermal paper widths, supporting USB, Bluetooth, and network thermal printers commonly used across Sri Lankan retail stores.
                        </div>
                    </div>

                    <!-- FAQ 4 -->
                    <div
                        class="bento-card p-6 cursor-pointer"
                        x-show="faqTab === 'all' || faqTab === 'pos'"
                        @click="activeFaq = activeFaq === 3 ? -1 : 3"
                    >
                        <div class="flex items-center justify-between">
                            <h3 class="text-base sm:text-lg font-bold text-pitch">
                                Can staff members see our profit margins or cost prices?
                            </h3>
                            <span class="text-2xl font-light text-crimson transition-transform duration-300" :class="activeFaq === 3 ? 'rotate-45' : ''">+</span>
                        </div>
                        <div x-show="activeFaq === 3" x-collapse class="text-xs sm:text-sm text-slate-600 leading-relaxed pt-3">
                            No. Role-based tenant isolation strictly hides internal cost of goods sold (COGS), supplier bills, and profit metrics from sales staff and cashiers. Only verified Company Owners and Admins have access to profitability reports.
                        </div>
                    </div>

                    <!-- FAQ 5 -->
                    <div
                        class="bento-card p-6 cursor-pointer"
                        x-show="faqTab === 'all' || faqTab === 'general'"
                        @click="activeFaq = activeFaq === 4 ? -1 : 4"
                    >
                        <div class="flex items-center justify-between">
                            <h3 class="text-base sm:text-lg font-bold text-pitch">
                                What payment methods are supported in Sri Lanka?
                            </h3>
                            <span class="text-2xl font-light text-crimson transition-transform duration-300" :class="activeFaq === 4 ? 'rotate-45' : ''">+</span>
                        </div>
                        <div x-show="activeFaq === 4" x-collapse class="text-xs sm:text-sm text-slate-600 leading-relaxed pt-3">
                            We support Sri Lankan direct bank transfers (Sampath, Commercial, HNB, BOC), corporate cheques, and Visa/Mastercard payments. Subscriptions are billed natively in LKR with zero foreign exchange fees.
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 12. Final High-Impact CTA Banner with 3D Liquid Accent Graphic -->
        <section class="py-16 sm:py-24 bg-cream">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="rounded-3xl bg-pitch text-white p-8 sm:p-16 lg:p-20 relative overflow-hidden shadow-2xl space-y-8" data-motion-cta>
                    <!-- Red Ambient Radial Glow -->
                    <div class="absolute -right-20 -top-20 w-96 h-96 bg-crimson/30 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute -left-20 -bottom-20 w-96 h-96 bg-red-800/25 rounded-full blur-3xl pointer-events-none"></div>

                    <!-- Floating 3D Liquid Sphere Graphic (Suitable Asset Added) -->
                    <div class="absolute top-6 right-6 sm:top-12 sm:right-12 w-28 h-28 sm:w-40 sm:h-40 pointer-events-none opacity-85 floating-pill">
                        <img
                            src="{{ asset('images/landing/liquid-cta-blob.webp') }}"
                            alt="3D Liquid Chrome & Crimson Accent Sphere"
                            class="w-full h-full object-contain drop-shadow-[0_0_35px_rgba(255,56,56,0.5)]"
                            loading="lazy"
                        >
                    </div>

                    <div class="relative z-10 max-w-2xl mx-auto text-center space-y-4">
                        <span class="text-xs font-mono font-bold uppercase tracking-widest text-crimson">
                            GET STARTED TODAY
                        </span>
                        <h2 class="text-4xl sm:text-6xl font-display text-white leading-tight tracking-wide">
                            READY TO UPGRADE YOUR BUSINESS OPERATIONS?
                        </h2>
                        <p class="text-sm sm:text-base text-slate-300 max-w-lg mx-auto">
                            Join leading Sri Lankan solar EPC contractors and retail distributors running on PixelCraft SaaS.
                        </p>
                    </div>

                    <div class="relative z-10 flex flex-col sm:flex-row items-center justify-center gap-4 pt-2">
                        <a href="/app/register" class="btn-crimson px-9 py-4 text-xs uppercase tracking-wider font-bold shadow-xl w-full sm:w-auto">
                            <span>Start 14-Day Free Trial</span>
                            <span class="btn-arrow text-base font-bold">↗</span>
                        </a>
                        <a href="/app/login" class="px-8 py-4 rounded-full border border-white/30 hover:border-white text-white text-xs uppercase tracking-wider font-bold transition-colors w-full sm:w-auto">
                            <span>Sign In to Workspace</span>
                            <span class="btn-arrow">↗</span>
                        </a>
                    </div>

                    <!-- Newsletter Input Strip -->
                    <div class="relative z-10 max-w-md mx-auto pt-8 border-t border-white/10">
                        <div class="text-center text-xs text-slate-400 mb-3">Stay updated with Sri Lankan regulatory updates & releases</div>
                        <form onsubmit="event.preventDefault(); alert('Thank you for subscribing to PixelCraft updates!');" class="flex gap-2">
                            <input
                                type="email"
                                required
                                placeholder="Enter your business email"
                                class="bg-white/10 border border-white/20 rounded-full px-5 py-2.5 text-xs text-white placeholder-slate-400 flex-1 focus:outline-none focus:border-crimson"
                            >
                            <button type="submit" class="btn-crimson px-5 py-2.5 text-xs uppercase tracking-wider">
                                Subscribe
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- 13. Clean Editorial Dark Footer with Giant Watermark -->
    <footer class="border-t border-pitchBorder bg-pitch text-white pt-20 pb-12 relative overflow-hidden">
        <!-- Giant Faint Footer Watermark (MarketingLab Signature) -->
        <div class="footer-watermark">
            PIXELCRAFT
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
                <!-- Col 1: Brand -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-crimson flex items-center justify-center text-white font-display text-xl shadow-md">
                            PC
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="font-display text-2xl text-white tracking-wide">PIXELCRAFT</span>
                            <span class="w-2 h-2 rounded-full bg-crimson"></span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed font-sans">
                        Sri Lanka's dedicated multi-tenant cloud platform for engineering, retail point-of-sale, and wholesale milestone invoicing.
                    </p>
                    <div class="text-[11px] font-mono text-crimson font-bold">
                        app.pixelcraftlk.com
                    </div>
                </div>

                <!-- Col 2: Solutions -->
                <div class="space-y-3">
                    <h4 class="text-xs font-mono font-bold uppercase tracking-wider text-white">Solutions</h4>
                    <ul class="space-y-2 text-xs text-slate-400">
                        <li><a href="#solar-erp" class="hover:text-crimson transition-colors">CEB Solar PV Sizing</a></li>
                        <li><a href="#services" class="hover:text-crimson transition-colors">Zoho Milestone Invoicing</a></li>
                        <li><a href="#services" class="hover:text-crimson transition-colors">80mm Counter Thermal POS</a></li>
                        <li><a href="#services" class="hover:text-crimson transition-colors">Statutory EPF/ETF Payroll</a></li>
                    </ul>
                </div>

                <!-- Col 3: Access -->
                <div class="space-y-3">
                    <h4 class="text-xs font-mono font-bold uppercase tracking-wider text-white">Access & Company</h4>
                    <ul class="space-y-2 text-xs text-slate-400">
                        <li><a href="/app/login" class="hover:text-crimson transition-colors">Workspace Sign In</a></li>
                        <li><a href="/app/register" class="hover:text-crimson transition-colors">Register Company</a></li>
                        <li><a href="#pricing" class="hover:text-crimson transition-colors">Subscription Plans in LKR</a></li>
                        <li><a href="#process" class="hover:text-crimson transition-colors">Operational Framework</a></li>
                    </ul>
                </div>

                <!-- Col 4: Regional Compliance -->
                <div class="space-y-3">
                    <h4 class="text-xs font-mono font-bold uppercase tracking-wider text-white">Compliance & Support</h4>
                    <p class="text-xs text-slate-400 leading-relaxed font-sans">
                        Multi-tenant database isolation. Native Sri Lankan LKR currency, VAT invoice compliance, and bank transfer support.
                    </p>
                    <div class="text-xs text-slate-400 font-sans">
                        Support: <a href="mailto:support@pixelcraftlk.com" class="text-crimson hover:underline font-bold">support@pixelcraftlk.com</a>
                    </div>
                    <div class="text-[11px] text-emerald-400 font-bold flex items-center gap-1.5 pt-1 font-mono">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Official Cloud Production System
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-pitchBorder flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 gap-4">
                <div>
                    © {{ date('Y') }} PixelCraft SaaS. All rights reserved.
                </div>
                <div class="flex items-center gap-6">
                    <a href="/privacy" class="hover:text-crimson transition-colors">Privacy Policy</a>
                    <a href="/terms" class="hover:text-crimson transition-colors">Terms of Service</a>
                    <a href="/app/login" class="hover:text-crimson transition-colors">Staff Launchpad</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Framer Motion Engine ESM Integration -->
    <script type="module">
        import { animate, inView, stagger, spring } from 'https://cdn.jsdelivr.net/npm/motion@12.4.2/+esm';

        // 1. Kinetic Hero Entrance Animation
        try {
            animate('[data-motion-hero]', 
                { opacity: [0, 1], y: [45, 0], scale: [0.95, 1] }, 
                { duration: 0.9, easing: spring({ stiffness: 120, damping: 20 }) }
            );

            animate('[data-motion-card]', 
                { opacity: [0, 1], y: [35, 0] }, 
                { duration: 0.8, delay: 0.2, easing: spring({ stiffness: 100, damping: 18 }) }
            );

            // 2. Scroll-Triggered Process Cards Stagger Reveal
            inView('#process', () => {
                animate('[data-motion-process]', 
                    { opacity: [0, 1], y: [50, 0] }, 
                    { delay: stagger(0.12), duration: 0.7, easing: spring({ stiffness: 110, damping: 20 }) }
                );
            });

            // 3. Case Studies Reveal
            inView('#cases', () => {
                animate('[data-motion-case]', 
                    { opacity: [0, 1], y: [40, 0] }, 
                    { delay: stagger(0.15), duration: 0.8, easing: spring({ stiffness: 100, damping: 18 }) }
                );
            });

            // 4. Testimonial Reviews Stagger Reveal
            inView('[data-motion-review]', (info) => {
                animate(info.target, 
                    { opacity: [0, 1], y: [30, 0], scale: [0.97, 1] }, 
                    { duration: 0.6, easing: spring({ stiffness: 120, damping: 20 }) }
                );
            });

            // 5. Pricing Cards Reveal
            inView('#pricing', () => {
                animate('[data-motion-pricing]', 
                    { opacity: [0, 1], y: [40, 0] }, 
                    { delay: stagger(0.12), duration: 0.7, easing: spring({ stiffness: 110, damping: 20 }) }
                );
            });

            // 6. Final CTA Banner Entrance
            inView('[data-motion-cta]', (info) => {
                animate(info.target, 
                    { opacity: [0, 1], scale: [0.96, 1] }, 
                    { duration: 0.8, easing: spring({ stiffness: 100, damping: 18 }) }
                );
            });
        } catch (e) {
            console.log('Framer Motion initialized with CSS fallback:', e);
        }
    </script>
</body>
</html>

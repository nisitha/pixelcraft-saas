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
<html lang="en" class="scroll-smooth dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PixelCraft SaaS — Sri Lanka's Intelligent Cloud ERP, Counter POS & Solar PV Workspace</title>
    <meta name="description" content="PixelCraft SaaS is Sri Lanka's high-performance cloud platform designed for Solar EPC contractors, retail merchants, and business operations. Featuring CEB solar sizing, Zoho milestone invoicing, 80mm thermal POS, and EPF/ETF payroll.">
    <meta name="theme-color" content="#070709">

    <!-- Open Graph / Meta -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="PixelCraft SaaS — Cloud ERP, Counter POS & Solar PV Workspace">
    <meta property="og:description" content="Engineered for Sri Lankan Solar EPCs, electrical distributors, and retail businesses. CEB Net-Metering sizing, milestone invoicing, and 80mm POS.">
    <meta property="og:image" content="{{ asset('images/landing/hero-dashboard.webp') }}">

    <!-- Typography: Urbanist (Display / Headings) & Inter (UI / Body) & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,600;1,700&family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN with Custom Dark Config -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        urbanist: ['Urbanist', 'sans-serif'],
                        sans: ['Inter', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        obsidian: {
                            base: '#070709',
                            surface: '#0d0e12',
                            card: '#121318',
                            border: 'rgba(255, 255, 255, 0.08)',
                            borderHover: 'rgba(255, 255, 255, 0.18)',
                        },
                        brand: {
                            cyan: '#06b6d4',
                            indigo: '#6366f1',
                            violet: '#8b5cf6',
                            purple: '#a855f7',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            --bg-base: #070709;
            --bg-surface: #0c0d12;
            --card-glass: rgba(255, 255, 255, 0.025);
            --card-glass-hover: rgba(255, 255, 255, 0.045);
            --border-subtle: rgba(255, 255, 255, 0.08);
            --border-hover: rgba(255, 255, 255, 0.2);
            --glow-cyan: rgba(6, 182, 212, 0.15);
            --glow-violet: rgba(139, 92, 246, 0.15);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-base);
            color: #e2e8f0;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        h1, h2, h3, h4, .font-display {
            font-family: 'Urbanist', sans-serif;
            letter-spacing: -0.035em;
        }

        /* Ambient Background Mesh Glows */
        .ambient-mesh {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }
        .mesh-blob-1 {
            position: absolute;
            top: -15%;
            left: 50%;
            transform: translateX(-50%);
            width: 900px;
            height: 550px;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.14) 0%, rgba(99, 102, 241, 0.08) 45%, transparent 70%);
            filter: blur(80px);
        }
        .mesh-blob-2 {
            position: absolute;
            top: 25%;
            left: -10%;
            width: 700px;
            height: 600px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.1) 0%, rgba(14, 165, 233, 0.05) 50%, transparent 70%);
            filter: blur(90px);
        }
        .mesh-blob-3 {
            position: absolute;
            top: 55%;
            right: -10%;
            width: 750px;
            height: 650px;
            background: radial-gradient(circle, rgba(168, 85, 247, 0.12) 0%, rgba(99, 102, 241, 0.06) 50%, transparent 75%);
            filter: blur(95px);
        }

        /* Subtle Geometric Dot Grid */
        .bg-grid-dots {
            background-image: radial-gradient(rgba(255, 255, 255, 0.07) 1px, transparent 1px);
            background-size: 28px 28px;
            mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 0%, rgba(0,0,0,0.7) 60%, rgba(0,0,0,0) 100%);
            -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 0%, rgba(0,0,0,0.7) 60%, rgba(0,0,0,0) 100%);
        }

        /* Glassmorphism Floating Navbar */
        .glass-nav {
            background: rgba(7, 7, 9, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
        }

        /* Glass Cards */
        .glass-card {
            background: var(--card-glass);
            border: 1px solid var(--border-subtle);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 1.5rem;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }
        .glass-card:hover {
            background: var(--card-glass-hover);
            border-color: var(--border-hover);
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.7), 0 0 35px -5px rgba(139, 92, 246, 0.12);
        }

        /* Radiant Pill Button (Framer style) */
        .btn-radiant {
            background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 50%, #3b82f6 100%);
            color: #ffffff;
            font-family: 'Urbanist', sans-serif;
            font-weight: 700;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            position: relative;
            box-shadow: 0 0 25px -4px rgba(139, 92, 246, 0.5);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-radiant:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 35px 2px rgba(139, 92, 246, 0.7), 0 10px 25px -5px rgba(99, 102, 241, 0.5);
        }
        .btn-radiant:hover .btn-arrow {
            transform: translate(2px, -2px);
        }

        .btn-ghost-glass {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #ffffff;
            font-family: 'Urbanist', sans-serif;
            font-weight: 700;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            backdrop-filter: blur(12px);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-ghost-glass:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.3);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px -5px rgba(0, 0, 0, 0.5);
        }
        .btn-ghost-glass:hover .btn-arrow {
            transform: translate(2px, -2px);
        }

        .btn-arrow {
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Kinetic Giant Headline (MarketingLab Display) */
        .hero-kinetic-title {
            font-family: 'Urbanist', sans-serif;
            font-weight: 900;
            font-size: clamp(3.5rem, 13vw, 13rem);
            line-height: 0.88;
            letter-spacing: -0.05em;
            text-transform: uppercase;
            text-align: center;
            user-select: none;
            background: linear-gradient(180deg, #ffffff 0%, #a1a1aa 40%, rgba(255, 255, 255, 0.2) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-top: 1rem;
            margin-bottom: 2rem;
            position: relative;
        }

        /* Infinite Marquee Strip */
        @keyframes marquee {
            0% { transform: translate3d(0, 0, 0); }
            100% { transform: translate3d(-50%, 0, 0); }
        }
        .marquee-track {
            display: flex;
            width: max-content;
            animation: marquee 35s linear infinite;
        }
        .marquee-track:hover {
            animation-play-state: paused;
        }

        /* Floating Badge Animations */
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        .floating-pill {
            animation: floatSlow 4.5s ease-in-out infinite;
        }
        .floating-pill-delayed {
            animation: floatSlow 5.5s ease-in-out 1.8s infinite;
        }

        /* Process Card Glowing Border */
        .process-glass-card {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.04) 0%, rgba(255, 255, 255, 0.01) 100%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 1.75rem;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }
        .process-glass-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 50% 0%, rgba(139, 92, 246, 0.15), transparent 70%);
            opacity: 0;
            transition: opacity 0.4s ease;
            pointer-events: none;
        }
        .process-glass-card:hover {
            transform: translateY(-8px) scale(1.02);
            border-color: rgba(139, 92, 246, 0.4);
            box-shadow: 0 25px 50px -15px rgba(0, 0, 0, 0.8), 0 0 35px -5px rgba(139, 92, 246, 0.25);
        }
        .process-glass-card:hover::before {
            opacity: 1;
        }

        /* FAQ Accordion Smooth Reveal */
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.35s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
            opacity: 0;
        }
        .faq-item.active .faq-answer {
            max-height: 280px;
            opacity: 1;
        }
        .faq-item.active .faq-icon {
            transform: rotate(45deg);
        }

        /* Glowing Accent Line */
        .glow-line {
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, rgba(139, 92, 246, 0.6) 50%, transparent 100%);
        }
    </style>
</head>
<body class="selection:bg-violet-600 selection:text-white relative min-h-screen">

    <!-- Ambient Mesh Glow Canvas (Framer Style) -->
    <div class="ambient-mesh">
        <div class="mesh-blob-1"></div>
        <div class="mesh-blob-2"></div>
        <div class="mesh-blob-3"></div>
    </div>

    <!-- 1. Sleek Frosted Glass Floating Navbar -->
    <header class="sticky top-0 z-50 glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand Identity -->
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-600 to-indigo-700 flex items-center justify-center text-white font-black font-urbanist text-xl shadow-[0_0_20px_rgba(139,92,246,0.5)] group-hover:scale-105 transition-transform duration-300">
                        PC
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="font-urbanist font-extrabold text-2xl tracking-tight text-white">PixelCraft</span>
                        <span class="w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_8px_#22d3ee] animate-pulse"></span>
                        <span class="text-[10px] font-urbanist font-bold uppercase tracking-widest text-slate-400 border border-white/10 px-2 py-0.5 rounded-full bg-white/[0.03] hidden sm:inline-block">SaaS ERP</span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-urbanist font-bold text-slate-300">
                    <a href="#features" class="hover:text-cyan-400 transition-colors">Platform Features</a>
                    <a href="#process" class="hover:text-violet-400 transition-colors">Our Process</a>
                    <a href="#solar-epc" class="hover:text-cyan-400 transition-colors">Solar PV Sizing</a>
                    <a href="#pos-terminal" class="hover:text-violet-400 transition-colors">Counter POS</a>
                    <a href="#pricing" class="hover:text-cyan-400 transition-colors">Pricing in LKR</a>
                    <a href="#faq" class="hover:text-violet-400 transition-colors">FAQ</a>
                </nav>

                <!-- Auth CTA Buttons -->
                <div class="flex items-center gap-4">
                    <a href="/app/login" class="text-sm font-urbanist font-bold text-slate-200 hover:text-white transition-colors hidden sm:block">
                        Sign In
                    </a>
                    <a href="/app/register" class="btn-radiant px-6 py-2.5 text-sm shadow-md">
                        <span>Start Free Trial</span>
                        <span class="btn-arrow text-base font-bold">↗</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="relative z-10">
        <!-- 2. The MarketingLab Style Kinetic Display Hero -->
        <section class="pt-8 sm:pt-14 pb-16 sm:pb-24 relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Meta Row (©2026 left, Sri Lanka B2B right) -->
                <div class="flex items-center justify-between text-xs sm:text-sm font-urbanist font-bold uppercase tracking-wider text-slate-400 border-b border-white/10 pb-4 mb-2">
                    <div class="flex items-center gap-2">
                        <span class="text-white font-black">©2026</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-violet-400"></span>
                        <span class="text-slate-400 hidden sm:inline font-mono">COLOMBO, SRI LANKA</span>
                    </div>
                    <div class="tracking-widest text-slate-300 font-mono text-[11px] sm:text-xs">
                        MULTI-TENANT CLOUD ERP & POS PLATFORM
                    </div>
                </div>

                <!-- Massive Kinetic Display Word -->
                <div class="hero-kinetic-title select-none" data-motion-hero>
                    PIXELCRAFT
                </div>

                <!-- Hero Interactive Visual Card / Obsidian Surface -->
                <div class="relative rounded-3xl overflow-hidden glass-card p-6 sm:p-12 lg:p-16 border border-white/10 shadow-[0_20px_70px_rgba(0,0,0,0.8)]">
                    <!-- Dot Grid Overlay -->
                    <div class="absolute inset-0 bg-grid-dots pointer-events-none opacity-40"></div>

                    <!-- Ambient Glow Corner Mesh -->
                    <div class="absolute -top-32 -right-32 w-96 h-96 bg-violet-600/20 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-cyan-600/15 rounded-full blur-3xl pointer-events-none"></div>

                    <!-- Hero Content Grid -->
                    <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                        <div class="lg:col-span-7 space-y-6">
                            <!-- Announcement Pill Badge -->
                            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/[0.05] border border-white/15 text-slate-200 text-xs font-urbanist font-bold tracking-wide">
                                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
                                <span class="text-cyan-300 font-extrabold">NEW 3.4</span>
                                <span class="text-slate-400">•</span>
                                <span>CEB Solar PV Sizing & Milestone Invoicing</span>
                                <span class="text-slate-400">→</span>
                            </div>

                            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-urbanist font-black text-white leading-[1.08] tracking-tight">
                                Sri Lanka's Intelligent Cloud Workspace for <span class="bg-gradient-to-r from-violet-400 via-indigo-300 to-cyan-400 bg-clip-text text-transparent">Solar EPC & Trade Retail.</span>
                            </h1>

                            <p class="text-base sm:text-lg text-slate-300 font-normal leading-relaxed max-w-xl">
                                Built from the ground up for Sri Lankan engineering contractors, trade distributors, and counter merchants. Seamlessly integrates CEB & LECO solar sizing, Zoho-style milestone billing, sub-second 80mm thermal POS, and statutory EPF/ETF payroll.
                            </p>

                            <!-- Hero Action CTAs -->
                            <div class="pt-2 flex flex-wrap items-center gap-4">
                                <a href="/app/register" class="btn-radiant px-8 py-4 text-base font-bold shadow-xl">
                                    <span>Start 14-Day Free Trial</span>
                                    <span class="btn-arrow font-black text-lg">↗</span>
                                </a>
                                <a href="/app/login" class="btn-ghost-glass px-7 py-4 text-base font-bold">
                                    <span>Sign In to Workspace</span>
                                    <span class="btn-arrow text-slate-400">↗</span>
                                </a>
                            </div>

                            <!-- Trust Checkmarks -->
                            <div class="pt-2 flex flex-wrap items-center gap-6 text-xs text-slate-400 font-urbanist font-semibold">
                                <span class="flex items-center gap-1.5 text-slate-300">
                                    <span class="text-cyan-400 font-bold">✓</span> No credit card required
                                </span>
                                <span class="flex items-center gap-1.5 text-slate-300">
                                    <span class="text-cyan-400 font-bold">✓</span> 100% Sri Lanka VAT compliant
                                </span>
                                <span class="flex items-center gap-1.5 text-slate-300">
                                    <span class="text-cyan-400 font-bold">✓</span> Instant tenant activation
                                </span>
                            </div>
                        </div>

                        <!-- Right: Interactive Mockup Showcase with Glowing Mesh -->
                        <div class="lg:col-span-5 relative">
                            <!-- Hero SaaS UI Dashboard Mockup -->
                            <div class="relative rounded-2xl overflow-hidden border border-white/15 bg-obsidian-card shadow-[0_20px_50px_rgba(0,0,0,0.9)] group">
                                <img
                                    src="{{ asset('images/landing/hero-dashboard.webp') }}"
                                    alt="PixelCraft Cloud ERP & Solar PV Analytics Dashboard"
                                    class="w-full h-auto object-cover transform transition-transform duration-700 group-hover:scale-105"
                                    loading="eager"
                                    width="1280"
                                    height="720"
                                >
                                <div class="absolute inset-0 bg-gradient-to-t from-[#070709] via-transparent to-transparent opacity-60 pointer-events-none"></div>

                                <!-- Floating Live Stat Badge Over Mockup -->
                                <div class="absolute bottom-4 left-4 right-4 p-4 rounded-xl bg-black/60 backdrop-blur-xl border border-white/15 text-white flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-cyan-500/20 border border-cyan-500/40 flex items-center justify-center text-cyan-300 font-black">
                                            ☀️
                                        </div>
                                        <div>
                                            <div class="font-urbanist font-black text-sm text-white">485 kWp Managed</div>
                                            <div class="text-[11px] text-emerald-400 font-mono">CEB Net-Metering Sync</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-mono font-bold text-slate-200">Rs. 1.25M</div>
                                        <div class="text-[10px] text-cyan-300 font-bold uppercase tracking-wider">Live Month</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Floating Floating Pills (Framer style) -->
                            <div class="absolute -top-5 -left-4 floating-pill hidden sm:inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-black/70 backdrop-blur-md border border-white/20 text-white text-xs font-urbanist font-bold shadow-xl">
                                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                                📑 Zoho Milestone Invoicing
                            </div>

                            <div class="absolute -bottom-5 -right-4 floating-pill-delayed hidden sm:inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-black/70 backdrop-blur-md border border-white/20 text-white text-xs font-urbanist font-bold shadow-xl">
                                <span class="w-2 h-2 rounded-full bg-violet-400 animate-pulse"></span>
                                🧾 80mm ESC/POS Printing
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Metrics Strip (MarketingLab KPI Matrix) -->
                    <div class="mt-14 pt-8 border-t border-white/10 grid grid-cols-2 md:grid-cols-4 gap-6 font-urbanist">
                        <div class="space-y-1">
                            <div class="text-3xl sm:text-4xl font-black text-white">14 Days</div>
                            <div class="text-xs text-slate-400 font-bold uppercase tracking-wider">Unrestricted Free Trial</div>
                        </div>
                        <div class="space-y-1">
                            <div class="text-3xl sm:text-4xl font-black text-white">Rs. 500M+</div>
                            <div class="text-xs text-slate-400 font-bold uppercase tracking-wider">Transactions Processed</div>
                        </div>
                        <div class="space-y-1">
                            <div class="text-3xl sm:text-4xl font-black text-white">100%</div>
                            <div class="text-xs text-slate-400 font-bold uppercase tracking-wider">CEB & LECO Grid Standard</div>
                        </div>
                        <div class="space-y-1">
                            <div class="text-3xl sm:text-4xl font-black text-white">0.2 sec</div>
                            <div class="text-xs text-slate-400 font-bold uppercase tracking-wider">Thermal POS Speed</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. Infinite Smooth Marquee Ticker -->
        <div class="border-y border-white/10 bg-black/40 py-5 overflow-hidden select-none backdrop-blur-md">
            <div class="marquee-track flex items-center text-xs sm:text-sm font-urbanist font-extrabold uppercase tracking-widest text-slate-300">
                <div class="flex items-center gap-8 shrink-0 px-4">
                    <span><span class="text-cyan-400 font-black mr-2">/</span> SOLAR PV SIZING ENGINE</span>
                    <span><span class="text-violet-400 font-black mr-2">/</span> ZOHO MILESTONE INVOICING</span>
                    <span><span class="text-cyan-400 font-black mr-2">/</span> 80MM ESC/POS COUNTER POS</span>
                    <span><span class="text-violet-400 font-black mr-2">/</span> EPF 8%/12% & ETF 3% PAYROLL</span>
                    <span><span class="text-cyan-400 font-black mr-2">/</span> CEB & LECO CONTRACT AGREEMENTS</span>
                    <span><span class="text-violet-400 font-black mr-2">/</span> OFFICIAL COMMISSIONING CERTIFICATES</span>
                    <span><span class="text-cyan-400 font-black mr-2">/</span> WARRANTY SERIAL NUMBER VAULT</span>
                    <span><span class="text-violet-400 font-black mr-2">/</span> MARGIN & COGS CONFIDENTIALITY</span>
                </div>
                <div class="flex items-center gap-8 shrink-0 px-4">
                    <span><span class="text-cyan-400 font-black mr-2">/</span> SOLAR PV SIZING ENGINE</span>
                    <span><span class="text-violet-400 font-black mr-2">/</span> ZOHO MILESTONE INVOICING</span>
                    <span><span class="text-cyan-400 font-black mr-2">/</span> 80MM ESC/POS COUNTER POS</span>
                    <span><span class="text-violet-400 font-black mr-2">/</span> EPF 8%/12% & ETF 3% PAYROLL</span>
                    <span><span class="text-cyan-400 font-black mr-2">/</span> CEB & LECO CONTRACT AGREEMENTS</span>
                    <span><span class="text-violet-400 font-black mr-2">/</span> OFFICIAL COMMISSIONING CERTIFICATES</span>
                    <span><span class="text-cyan-400 font-black mr-2">/</span> WARRANTY SERIAL NUMBER VAULT</span>
                    <span><span class="text-violet-400 font-black mr-2">/</span> MARGIN & COGS CONFIDENTIALITY</span>
                </div>
            </div>
        </div>

        <!-- 4. The Manifesto Statement (MarketingLab Dark Aesthetic) -->
        <section class="py-24 sm:py-32 relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                    <!-- Left Meta Column -->
                    <div class="lg:col-span-3 space-y-4 font-mono text-xs text-slate-400 tracking-widest uppercase">
                        <div class="text-cyan-400 font-bold">ESTABLISHED 2024</div>
                        <div>SRI LANKA MARKET</div>
                        <div>MULTI-TENANT ISOLATION</div>
                        <div>FILAMENT 3.X DUAL PANELS</div>
                    </div>

                    <!-- Right Giant Manifesto -->
                    <div class="lg:col-span-9 space-y-8">
                        <h2 class="text-2xl sm:text-4xl lg:text-5xl font-urbanist font-extrabold text-white leading-[1.25]">
                            We don't do disconnected spreadsheets. Every Sri Lankan enterprise gets a unified cloud workspace engineered with obsessive precision and localized compliance — <span class="bg-gradient-to-r from-violet-400 via-indigo-300 to-cyan-400 bg-clip-text text-transparent">until every single rupee is accounted for.</span>
                        </h2>

                        <!-- Partner / Compliance Standards Cards -->
                        <div class="pt-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div class="glass-card p-5 text-center group">
                                <div class="font-urbanist font-black text-lg text-white group-hover:text-cyan-300 transition-colors">CEB Ready</div>
                                <div class="text-[11px] text-slate-400 mt-1">Net-Metering & Net-Plus</div>
                            </div>
                            <div class="glass-card p-5 text-center group">
                                <div class="font-urbanist font-black text-lg text-white group-hover:text-violet-300 transition-colors">LECO Standards</div>
                                <div class="text-[11px] text-slate-400 mt-1">Grid Sizing Algorithms</div>
                            </div>
                            <div class="glass-card p-5 text-center group">
                                <div class="font-urbanist font-black text-lg text-white group-hover:text-cyan-300 transition-colors">ESC/POS 80mm</div>
                                <div class="text-[11px] text-slate-400 mt-1">Raw Thermal Receipts</div>
                            </div>
                            <div class="glass-card p-5 text-center group">
                                <div class="font-urbanist font-black text-lg text-white group-hover:text-violet-300 transition-colors">EPF / ETF Statutory</div>
                                <div class="text-[11px] text-slate-400 mt-1">Labour Department Laws</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. "Our Process" — 4-Step Operational Framework (Framer MarketingLab Style) -->
        <section id="process" class="py-24 sm:py-32 relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
                    <div>
                        <span class="text-xs font-mono font-bold uppercase tracking-widest text-cyan-400">
                            Disciplined Execution
                        </span>
                        <h2 class="text-4xl sm:text-6xl font-urbanist font-black text-white mt-3 tracking-tight">
                            Our Process
                        </h2>
                    </div>
                    <p class="text-sm sm:text-base text-slate-400 max-w-md font-normal leading-relaxed">
                        A disciplined four-step operational workflow that eliminates billing guesswork, protects profit margins, and secures cash flow.
                    </p>
                </div>

                <!-- 4 Process Glowing Glass Cards (01, 02, 03, 04) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Step 01 -->
                    <div class="process-glass-card p-8 h-[440px] flex flex-col justify-between group">
                        <div class="flex items-start justify-between">
                            <span class="text-6xl sm:text-7xl font-urbanist font-black text-white/90 group-hover:text-white transition-colors">01</span>
                            <span class="w-8 h-8 rounded-full bg-violet-500/20 border border-violet-500/40 flex items-center justify-center text-violet-300 text-xs font-bold">✓</span>
                        </div>
                        <div class="space-y-3">
                            <h3 class="text-2xl font-urbanist font-black text-white group-hover:translate-x-1 transition-transform">
                                Onboard & Brand
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-normal">
                                Instant multi-tenant registration. Upload corporate logo, set branch locations, configure VAT registration numbers, and invite role-restricted staff in under 2 minutes.
                            </p>
                            <div class="pt-2">
                                <span class="text-[11px] font-mono font-semibold uppercase tracking-wider text-cyan-300 bg-cyan-950/60 border border-cyan-800/40 px-3 py-1 rounded-full">
                                    Step 01 • Configuration
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 02 -->
                    <div class="process-glass-card p-8 h-[440px] flex flex-col justify-between group">
                        <div class="flex items-start justify-between">
                            <span class="text-6xl sm:text-7xl font-urbanist font-black text-white/90 group-hover:text-white transition-colors">02</span>
                            <span class="w-8 h-8 rounded-full bg-cyan-500/20 border border-cyan-500/40 flex items-center justify-center text-cyan-300 text-xs font-bold">✓</span>
                        </div>
                        <div class="space-y-3">
                            <h3 class="text-2xl font-urbanist font-black text-white group-hover:translate-x-1 transition-transform">
                                Engineer & Quote
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-normal">
                                Input customer CEB monthly electricity units for instant kWp solar sizing. Draft Zoho-style milestone proposals with exact inverter/panel bill of quantities.
                            </p>
                            <div class="pt-2">
                                <span class="text-[11px] font-mono font-semibold uppercase tracking-wider text-violet-300 bg-violet-950/60 border border-violet-800/40 px-3 py-1 rounded-full">
                                    Step 02 • Estimation
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 03 -->
                    <div class="process-glass-card p-8 h-[440px] flex flex-col justify-between group">
                        <div class="flex items-start justify-between">
                            <span class="text-6xl sm:text-7xl font-urbanist font-black text-white/90 group-hover:text-white transition-colors">03</span>
                            <span class="w-8 h-8 rounded-full bg-indigo-500/20 border border-indigo-500/40 flex items-center justify-center text-indigo-300 text-xs font-bold">✓</span>
                        </div>
                        <div class="space-y-3">
                            <h3 class="text-2xl font-urbanist font-black text-white group-hover:translate-x-1 transition-transform">
                                Bill & Transact
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-normal">
                                Convert approved quotations into milestone invoices (60% advance, 30% delivery, 10% commissioning) or print instant 80mm ESC/POS counter receipts.
                            </p>
                            <div class="pt-2">
                                <span class="text-[11px] font-mono font-semibold uppercase tracking-wider text-indigo-300 bg-indigo-950/60 border border-indigo-800/40 px-3 py-1 rounded-full">
                                    Step 03 • Transaction
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 04 -->
                    <div class="process-glass-card p-8 h-[440px] flex flex-col justify-between group">
                        <div class="flex items-start justify-between">
                            <span class="text-6xl sm:text-7xl font-urbanist font-black text-white/90 group-hover:text-white transition-colors">04</span>
                            <span class="w-8 h-8 rounded-full bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-emerald-300 text-xs font-bold">✓</span>
                        </div>
                        <div class="space-y-3">
                            <h3 class="text-2xl font-urbanist font-black text-white group-hover:translate-x-1 transition-transform">
                                Settle & Profit
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-normal">
                                Issue official A4 Commissioning Certificates, vault serial numbers for warranty claims, generate EPF/ETF pay slips, and calculate net project margins.
                            </p>
                            <div class="pt-2">
                                <span class="text-[11px] font-mono font-semibold uppercase tracking-wider text-emerald-300 bg-emerald-950/60 border border-emerald-800/40 px-3 py-1 rounded-full">
                                    Step 04 • Settlement
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. Features Bento Grid (MarketingLab Dark Bento Style) -->
        <section id="features" class="py-24 sm:py-32 relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
                    <div>
                        <span class="text-xs font-mono font-bold uppercase tracking-widest text-violet-400">
                            Engineered Architecture
                        </span>
                        <h2 class="text-4xl sm:text-6xl font-urbanist font-black text-white mt-3 tracking-tight">
                            Platform Features
                        </h2>
                    </div>
                    <p class="text-sm sm:text-base text-slate-400 max-w-md font-normal leading-relaxed">
                        Comprehensive enterprise tools crafted specifically for Sri Lankan business workflows, tax frameworks, and hardware environments.
                    </p>
                </div>

                <!-- Bento Grid Layout -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                    <!-- Feature 1: Large Bento (Solar PV Sizing & BOQ) -->
                    <div class="md:col-span-8 glass-card p-8 sm:p-10 relative overflow-hidden flex flex-col justify-between group">
                        <div class="absolute -right-16 -top-16 w-80 h-80 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none group-hover:bg-cyan-500/20 transition-all duration-500"></div>

                        <div class="relative z-10 space-y-4 max-w-xl">
                            <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400 text-2xl font-bold">
                                ☀️
                            </div>
                            <span class="text-xs font-mono font-bold uppercase tracking-wider text-cyan-400">Solar EPC Module</span>
                            <h3 class="text-2xl sm:text-4xl font-urbanist font-black text-white">
                                Algorithmic Solar PV Sizing & Automated Legal Contracts
                            </h3>
                            <p class="text-sm sm:text-base text-slate-300 leading-relaxed font-normal">
                                Enter customer CEB monthly electricity consumption units to automatically compute exact kWp requirements, inverter configurations, and Tier-1 panel quantities. Generates official Sri Lankan contract agreements and gate pass PDFs in seconds.
                            </p>
                        </div>

                        <!-- Interactive Micro Graphic -->
                        <div class="mt-8 pt-6 border-t border-white/10 grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs font-mono text-slate-300">
                            <div class="p-3 rounded-xl bg-white/[0.03] border border-white/10">
                                <span class="text-slate-400 block text-[10px]">CEB STANDARD</span>
                                <span class="text-white font-bold">Net-Metering / Plus</span>
                            </div>
                            <div class="p-3 rounded-xl bg-white/[0.03] border border-white/10">
                                <span class="text-slate-400 block text-[10px]">GRID PHASES</span>
                                <span class="text-white font-bold">Single & 3-Phase</span>
                            </div>
                            <div class="p-3 rounded-xl bg-white/[0.03] border border-white/10 col-span-2 sm:col-span-1">
                                <span class="text-slate-400 block text-[10px]">CONTRACT BOQ</span>
                                <span class="text-cyan-400 font-bold">Auto Inverter Specs</span>
                            </div>
                        </div>
                    </div>

                    <!-- Feature 2: 3D Abstract Badge Visual Card -->
                    <div class="md:col-span-4 glass-card p-8 relative overflow-hidden flex flex-col justify-between text-center items-center group">
                        <div class="relative w-48 h-48 my-auto">
                            <img
                                src="{{ asset('images/landing/abstract-badge.webp') }}"
                                alt="3D Kinetic SaaS Badge"
                                class="w-full h-full object-contain rounded-2xl transform group-hover:scale-110 transition-transform duration-500 drop-shadow-[0_0_30px_rgba(139,92,246,0.4)]"
                                loading="lazy"
                                width="300"
                                height="300"
                            >
                        </div>
                        <div class="space-y-2 pt-4">
                            <h4 class="text-xl font-urbanist font-black text-white">Cloud Multi-Tenant Isolation</h4>
                            <p class="text-xs text-slate-400 leading-relaxed">
                                Complete database separation ensuring strict privacy for your inventory, margins, and customer data.
                            </p>
                        </div>
                    </div>

                    <!-- Feature 3: Zoho-Style Milestone Invoicing -->
                    <div class="md:col-span-4 glass-card p-8 relative overflow-hidden flex flex-col justify-between group">
                        <div class="space-y-4">
                            <div class="w-12 h-12 rounded-2xl bg-violet-500/10 border border-violet-500/30 flex items-center justify-center text-violet-400 text-2xl font-bold">
                                📑
                            </div>
                            <span class="text-xs font-mono font-bold uppercase tracking-wider text-violet-400">Billing Engine</span>
                            <h3 class="text-2xl font-urbanist font-black text-white">
                                Zoho-Style Milestone Billing
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-normal">
                                Dual-pane interactive editor with live PDF preview. Divide contracts into progress milestones: 60% Advance, 30% Material Delivery, and 10% Testing & Commissioning.
                            </p>
                        </div>
                        <div class="pt-6 border-t border-white/10 flex items-center justify-between text-xs font-urbanist font-bold text-violet-300">
                            <span>WhatsApp & Email Sharing</span>
                            <span>↗</span>
                        </div>
                    </div>

                    <!-- Feature 4: 80mm Counter POS Terminal -->
                    <div class="md:col-span-4 glass-card p-8 relative overflow-hidden flex flex-col justify-between group">
                        <div class="space-y-4">
                            <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400 text-2xl font-bold">
                                🧾
                            </div>
                            <span class="text-xs font-mono font-bold uppercase tracking-wider text-cyan-400">Cashier POS</span>
                            <h3 class="text-2xl font-urbanist font-black text-white">
                                Sub-Second Counter POS
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-normal">
                                Lightning-fast retail counter checkout. Barcode scanner support, cash tender change computation, and raw ESC/POS 80mm thermal receipt printing.
                            </p>
                        </div>
                        <div class="pt-6 border-t border-white/10 flex items-center justify-between text-xs font-urbanist font-bold text-cyan-300">
                            <span>0.2s Direct Print Speed</span>
                            <span>↗</span>
                        </div>
                    </div>

                    <!-- Feature 5: EPF/ETF Statutory Engine -->
                    <div class="md:col-span-4 glass-card p-8 relative overflow-hidden flex flex-col justify-between group">
                        <div class="space-y-4">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/30 flex items-center justify-center text-indigo-400 text-2xl font-bold">
                                👥
                            </div>
                            <span class="text-xs font-mono font-bold uppercase tracking-wider text-indigo-400">Statutory Payroll</span>
                            <h3 class="text-2xl font-urbanist font-black text-white">
                                EPF & ETF Payroll Engine
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-normal">
                                Automated monthly payroll compliant with Sri Lanka Department of Labour. Computes Employee 8%, Employer 12%, and ETF 3% with printable payslips.
                            </p>
                        </div>
                        <div class="pt-6 border-t border-white/10 flex items-center justify-between text-xs font-urbanist font-bold text-indigo-300">
                            <span>Labour Law Compliant</span>
                            <span>↗</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 7. Solar EPC Deep Dive & Certificate Preview (solar-epc section) -->
        <section id="solar-epc" class="py-24 sm:py-32 relative border-t border-white/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                    <div class="lg:col-span-6 space-y-6">
                        <span class="text-xs font-mono font-bold uppercase tracking-widest text-cyan-400 bg-cyan-950/60 border border-cyan-800/40 px-3.5 py-1.5 rounded-full">
                            Solar EPC Specialized Engine
                        </span>
                        <h2 class="text-3xl sm:text-5xl font-urbanist font-black text-white leading-tight">
                            Official Commissioning Certificates & Warranty Serial Tracking
                        </h2>
                        <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                            Never misplace serial numbers for high-value solar modules, inverters, or battery banks. PixelCraft SaaS links equipment barcodes directly to customer invoices and produces certified Commissioning Certificates ready for CEB grid approvals.
                        </p>

                        <div class="space-y-3 pt-2">
                            <div class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-cyan-500/20 border border-cyan-500/40 text-cyan-300 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">✓</span>
                                <div class="text-xs sm:text-sm text-slate-200">
                                    <strong class="font-urbanist font-bold text-white">A4 & A5 Certificate Generator:</strong> Instant executive solar system commissioning certificates with verification QR codes.
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-cyan-500/20 border border-cyan-500/40 text-cyan-300 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">✓</span>
                                <div class="text-xs sm:text-sm text-slate-200">
                                    <strong class="font-urbanist font-bold text-white">Warranty Serial Vault:</strong> Track individual panel & inverter serial numbers for rapid warranty claims without searching through filing cabinets.
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full bg-cyan-500/20 border border-cyan-500/40 text-cyan-300 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">✓</span>
                                <div class="text-xs sm:text-sm text-slate-200">
                                    <strong class="font-urbanist font-bold text-white">Freelance Promoter Vouchers:</strong> 1-click A5 commission settlement vouchers for external solar sales promoters and lead agents.
                                </div>
                            </div>
                        </div>

                        <div class="pt-4">
                            <a href="/app/register" class="btn-radiant px-8 py-3.5 text-sm">
                                <span>Get Started with Solar EPC Module</span>
                                <span class="btn-arrow text-base font-bold">↗</span>
                            </a>
                        </div>
                    </div>

                    <!-- Interactive Certificate Preview Mockup -->
                    <div class="lg:col-span-6">
                        <div class="glass-card p-6 sm:p-8 relative shadow-[0_20px_50px_rgba(0,0,0,0.8)] border border-white/15">
                            <div class="border border-white/10 rounded-2xl p-6 sm:p-8 bg-obsidian-surface text-center space-y-4">
                                <div class="flex items-center justify-between border-b border-white/10 pb-3 text-xs text-slate-400">
                                    <span class="font-mono font-bold text-cyan-400">CERTIFICATE #SL-SOL-2026-089</span>
                                    <span class="bg-emerald-500/20 text-emerald-400 font-bold px-2.5 py-0.5 rounded-full border border-emerald-500/30 text-[10px]">VERIFIED FOR CEB</span>
                                </div>

                                <div class="pt-2">
                                    <div class="text-[11px] font-mono font-bold text-slate-400 uppercase tracking-widest">OFFICIAL COMPLETION & COMMISSIONING</div>
                                    <h3 class="text-xl sm:text-2xl font-urbanist font-black text-white mt-1">SOLAR PV POWER SYSTEM</h3>
                                    <p class="text-xs text-slate-400 mt-1">Issued to Project Owner: <strong class="text-slate-200">Vertex Luxury Residencies Ltd</strong></p>
                                </div>

                                <div class="inline-flex items-center gap-2 bg-white/[0.04] border border-white/10 text-white px-4 py-2 rounded-xl text-xs font-urbanist font-bold">
                                    <span>☀️ 15.0 kWp On-Grid System</span>
                                    <span class="text-white/40">•</span>
                                    <span>28x 550W Tier-1 Mono PERC</span>
                                </div>

                                <div class="grid grid-cols-2 gap-3 text-left pt-2 text-xs">
                                    <div class="bg-white/[0.02] p-2.5 rounded-lg border border-white/10">
                                        <div class="text-[10px] text-slate-400 font-bold">INVERTER S/N</div>
                                        <div class="font-mono font-bold text-slate-200 truncate">SUNGROW-2026-9812A</div>
                                    </div>
                                    <div class="bg-white/[0.02] p-2.5 rounded-lg border border-white/10">
                                        <div class="text-[10px] text-slate-400 font-bold">GRID APPROVAL</div>
                                        <div class="font-mono font-bold text-emerald-400">CEB NET-METERING</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 8. Counter POS & Margin Confidentiality (pos-terminal section) -->
        <section id="pos-terminal" class="py-24 sm:py-32 relative border-t border-white/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                    <!-- Left: Role & Document Immutability Matrix -->
                    <div class="lg:col-span-6 order-2 lg:order-1">
                        <div class="glass-card p-6 sm:p-8 bg-obsidian-surface shadow-2xl border border-white/15">
                            <div class="flex items-center justify-between pb-4 border-b border-white/10 text-xs">
                                <span class="font-urbanist font-bold text-white tracking-wider uppercase">Staff Role Hierarchy</span>
                                <span class="text-violet-400 font-mono font-bold">Strict Audit Log</span>
                            </div>

                            <div class="space-y-3 pt-5">
                                <div class="p-4 rounded-xl bg-white/[0.03] border border-white/10 flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-urbanist font-extrabold text-white">Company Owner / MD</div>
                                        <div class="text-xs text-slate-400">Executive Admin Access</div>
                                    </div>
                                    <span class="bg-violet-500/20 text-violet-300 border border-violet-500/40 text-xs font-bold px-3 py-1 rounded-full">
                                        Full P&L & COGS
                                    </span>
                                </div>

                                <div class="p-4 rounded-xl bg-white/[0.03] border border-white/10 flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-urbanist font-extrabold text-white">Sales Engineer</div>
                                        <div class="text-xs text-slate-400">Solar PV Quoting & Invoicing</div>
                                    </div>
                                    <span class="bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 text-xs font-bold px-3 py-1 rounded-full">
                                        Create Immutable Quotes
                                    </span>
                                </div>

                                <div class="p-4 rounded-xl bg-white/[0.03] border border-white/10 flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-urbanist font-extrabold text-white">Counter Cashier</div>
                                        <div class="text-xs text-slate-400">80mm POS Terminal</div>
                                    </div>
                                    <span class="bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-xs font-bold px-3 py-1 rounded-full">
                                        Receipts Only (Zero Profit View)
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Info -->
                    <div class="lg:col-span-6 order-1 lg:order-2 space-y-6">
                        <span class="text-xs font-mono font-bold uppercase tracking-widest text-violet-400 bg-violet-950/60 border border-violet-800/40 px-3.5 py-1.5 rounded-full">
                            Security & Margin Governance
                        </span>
                        <h2 class="text-3xl sm:text-5xl font-urbanist font-black text-white leading-tight">
                            Immutable Records & Margin Confidentiality
                        </h2>
                        <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                            Protect your profit margins. Supplier equipment wholesale costs and company net margins are strictly invisible to non-admin staff. Once quotations and invoices are dispatched, tamper-resistant locking ensures figures cannot be altered without owner approval.
                        </p>
                        <div class="pt-2 flex flex-wrap gap-4">
                            <a href="/app/register" class="btn-radiant px-8 py-3.5 text-sm">
                                <span>Create Your Secure Workspace</span>
                                <span class="btn-arrow font-bold">↗</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 9. Dynamic Pricing Section in LKR (with Monthly / Annual Switcher) -->
        <section id="pricing" class="py-24 sm:py-32 relative border-t border-white/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="text-center max-w-3xl mx-auto space-y-4 mb-14">
                    <span class="text-xs font-mono font-bold uppercase tracking-widest text-cyan-400 bg-cyan-950/60 border border-cyan-800/40 px-3.5 py-1.5 rounded-full">
                        Predictable Pricing in LKR
                    </span>
                    <h2 class="text-4xl sm:text-6xl font-urbanist font-black text-white tracking-tight">
                        Simple Plans for Sri Lankan Businesses
                    </h2>
                    <p class="text-sm sm:text-base text-slate-400">
                        Every plan includes a 14-day risk-free trial. Upgrade or renew conveniently via local bank transfer or card.
                    </p>

                    <!-- Monthly vs Annual Switcher -->
                    <div class="pt-6 flex items-center justify-center">
                        <div class="bg-white/[0.04] p-1.5 rounded-full inline-flex items-center gap-1 border border-white/10 shadow-inner" id="billingSwitcher">
                            <button
                                type="button"
                                id="btnMonthly"
                                class="px-6 py-2.5 rounded-full text-xs font-urbanist font-extrabold transition-all bg-gradient-to-r from-violet-600 to-indigo-600 text-white shadow-md"
                                onclick="switchBilling('monthly')"
                            >
                                Monthly Billing
                            </button>
                            <button
                                type="button"
                                id="btnYearly"
                                class="px-6 py-2.5 rounded-full text-xs font-urbanist font-extrabold transition-all text-slate-400 hover:text-white"
                                onclick="switchBilling('yearly')"
                            >
                                <span>Annual Billing</span>
                                <span class="ml-1 text-[10px] bg-cyan-400 text-black font-extrabold px-2 py-0.5 rounded-full">Save 2 Mo.</span>
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
                        <div class="glass-card p-8 flex flex-col justify-between relative transition-all duration-300 {{ $isPopular ? 'border-2 border-violet-500 shadow-[0_0_40px_rgba(139,92,246,0.3)] scale-[1.03] z-10' : '' }}">
                            @if ($isPopular)
                                <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-gradient-to-r from-violet-600 to-cyan-500 text-white text-[11px] font-urbanist font-black uppercase tracking-wider px-4 py-1 rounded-full shadow-lg">
                                    ★ Most Popular for EPC
                                </div>
                            @endif

                            <div>
                                <h3 class="text-xl font-urbanist font-black text-white">{{ $plan->name }}</h3>
                                <p class="text-xs text-slate-400 mt-1">
                                    {{ $plan->slug === 'starter' ? 'Ideal for solo installers & retail counters' : ($plan->slug === 'professional' ? 'Complete EPC ERP for growing contractors' : 'Unlimited multi-branch corporate governance') }}
                                </p>

                                <!-- Dynamic Price Display -->
                                <div class="mt-6 flex items-baseline gap-1">
                                    <span class="text-4xl font-urbanist font-black text-white plan-price" data-monthly="{{ $plan->price_monthly }}" data-yearly="{{ $plan->price_yearly }}">
                                        Rs. {{ number_format($plan->price_monthly, 0) }}
                                    </span>
                                    <span class="text-xs text-slate-400 font-semibold period-label">/ month</span>
                                </div>

                                <div class="text-[11px] text-cyan-400 font-mono font-bold mt-1 period-subtext">
                                    or Rs. {{ number_format($plan->price_yearly, 0) }} paid annually
                                </div>

                                <!-- Features List -->
                                <div class="mt-8 pt-6 border-t border-white/10 space-y-3">
                                    <div class="text-xs font-urbanist font-extrabold uppercase tracking-wider text-slate-400">Features Included:</div>
                                    @if (is_array($plan->features))
                                        @foreach ($plan->features as $feature)
                                            <div class="flex items-center gap-2.5 text-xs text-slate-200">
                                                <span class="text-cyan-400 font-black text-sm">✓</span>
                                                <span>{{ $feature }}</span>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="flex items-center gap-2.5 text-xs text-slate-200">
                                            <span class="text-cyan-400 font-black text-sm">✓</span>
                                            <span>Max Users: {{ $plan->max_users }}</span>
                                        </div>
                                        <div class="flex items-center gap-2.5 text-xs text-slate-200">
                                            <span class="text-cyan-400 font-black text-sm">✓</span>
                                            <span>Max Invoices: {{ $plan->max_invoices }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- CTA Button -->
                            <div class="mt-8 pt-6 border-t border-white/10">
                                <a
                                    href="/app/register"
                                    class="{{ $isPopular ? 'btn-radiant' : 'btn-ghost-glass' }} w-full py-3 text-center text-sm font-urbanist font-bold block"
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
        <section id="faq" class="py-24 sm:py-32 relative border-t border-white/10">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center space-y-3 mb-16">
                    <span class="text-xs font-mono font-bold uppercase tracking-widest text-violet-400">
                        Common Questions
                    </span>
                    <h2 class="text-4xl sm:text-5xl font-urbanist font-black text-white tracking-tight">
                        Frequently Asked Questions
                    </h2>
                </div>

                <div class="space-y-4">
                    <!-- FAQ 1 -->
                    <div class="glass-card p-6 faq-item active cursor-pointer" onclick="toggleFaq(this)">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base sm:text-lg font-urbanist font-bold text-white">
                                How does the 14-day free trial work?
                            </h3>
                            <span class="faq-icon text-2xl font-light text-cyan-400 transition-transform duration-300">✕</span>
                        </div>
                        <div class="faq-answer text-xs sm:text-sm text-slate-300 leading-relaxed pt-3">
                            You get immediate, unrestricted access to the complete PixelCraft SaaS platform including Solar PV sizing, milestone invoicing, and POS. No credit card is required to register. After 14 days, you can choose any LKR subscription plan.
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="glass-card p-6 faq-item cursor-pointer" onclick="toggleFaq(this)">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base sm:text-lg font-urbanist font-bold text-white">
                                Is the Solar PV sizing tool compliant with CEB & LECO?
                            </h3>
                            <span class="faq-icon text-2xl font-light text-cyan-400 transition-transform duration-300">+</span>
                        </div>
                        <div class="faq-answer text-xs sm:text-sm text-slate-300 leading-relaxed pt-3">
                            Yes. Our sizing algorithms calculate required system kilowatt-peak (kWp) against Sri Lankan solar irradiance factors, CEB single/three-phase thresholds, and Net-Metering/Net-Plus policies.
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="glass-card p-6 faq-item cursor-pointer" onclick="toggleFaq(this)">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base sm:text-lg font-urbanist font-bold text-white">
                                Can I use standard 80mm ESC/POS thermal printers?
                            </h3>
                            <span class="faq-icon text-2xl font-light text-cyan-400 transition-transform duration-300">+</span>
                        </div>
                        <div class="faq-answer text-xs sm:text-sm text-slate-300 leading-relaxed pt-3">
                            Absolutely. The counter POS module formats receipts specifically for 80mm thermal paper widths, supporting USB, Bluetooth, and network thermal printers commonly used across Sri Lankan retail stores.
                        </div>
                    </div>

                    <!-- FAQ 4 -->
                    <div class="glass-card p-6 faq-item cursor-pointer" onclick="toggleFaq(this)">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base sm:text-lg font-urbanist font-bold text-white">
                                Can staff members see our profit margins or cost prices?
                            </h3>
                            <span class="faq-icon text-2xl font-light text-cyan-400 transition-transform duration-300">+</span>
                        </div>
                        <div class="faq-answer text-xs sm:text-sm text-slate-300 leading-relaxed pt-3">
                            No. Role-based tenant isolation strictly hides internal cost of goods sold (COGS), supplier bills, and profit metrics from sales staff and cashiers. Only verified Company Owners and Admins have access to profitability reports.
                        </div>
                    </div>

                    <!-- FAQ 5 -->
                    <div class="glass-card p-6 faq-item cursor-pointer" onclick="toggleFaq(this)">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base sm:text-lg font-urbanist font-bold text-white">
                                What payment methods are supported in Sri Lanka?
                            </h3>
                            <span class="faq-icon text-2xl font-light text-cyan-400 transition-transform duration-300">+</span>
                        </div>
                        <div class="faq-answer text-xs sm:text-sm text-slate-300 leading-relaxed pt-3">
                            We support Sri Lankan direct bank transfers (Sampath, Commercial, HNB, BOC), corporate cheques, and Visa/Mastercard payments. Subscriptions are billed natively in LKR with zero foreign exchange fees.
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 11. High-Impact Call to Action Banner (MarketingLab Dark Glow Style) -->
        <section class="py-16 sm:py-24 relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="rounded-3xl glass-card p-8 sm:p-16 lg:p-20 relative overflow-hidden border border-white/15 text-center space-y-8 shadow-[0_20px_60px_rgba(0,0,0,0.9)]">
                    <!-- Radial Glow in CTA -->
                    <div class="absolute inset-0 bg-gradient-to-r from-violet-600/20 via-indigo-600/20 to-cyan-600/20 pointer-events-none"></div>
                    <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-96 h-96 bg-violet-500/20 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="relative z-10 max-w-2xl mx-auto space-y-4">
                        <span class="text-xs font-mono font-bold uppercase tracking-widest text-cyan-400">
                            Get Started Today
                        </span>
                        <h2 class="text-3xl sm:text-5xl lg:text-6xl font-urbanist font-black text-white leading-tight">
                            Ready to upgrade your business operations?
                        </h2>
                        <p class="text-sm sm:text-base text-slate-300 max-w-lg mx-auto">
                            Join leading Sri Lankan solar EPC contractors and retail distributors running on PixelCraft SaaS.
                        </p>
                    </div>

                    <div class="relative z-10 flex flex-col sm:flex-row items-center justify-center gap-4 pt-2">
                        <a href="/app/register" class="btn-radiant px-9 py-4 text-base font-bold shadow-xl w-full sm:w-auto">
                            <span>Start 14-Day Free Trial</span>
                            <span class="btn-arrow text-lg font-black">↗</span>
                        </a>
                        <a href="/app/login" class="btn-ghost-glass px-8 py-4 text-base font-bold w-full sm:w-auto">
                            <span>Sign In to Workspace</span>
                            <span class="btn-arrow text-slate-400">↗</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- 12. Clean Obsidian Dark Footer -->
    <footer class="border-t border-white/10 bg-black/60 py-16 relative z-10 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
                <!-- Col 1: Brand -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-600 to-indigo-700 flex items-center justify-center text-white font-urbanist font-black text-lg shadow-[0_0_15px_rgba(139,92,246,0.4)]">
                            PC
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="font-urbanist font-extrabold text-xl text-white">PixelCraft</span>
                            <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Sri Lanka's dedicated multi-tenant cloud platform for engineering, retail point-of-sale, and wholesale milestone invoicing.
                    </p>
                    <div class="text-[11px] font-mono text-cyan-400 font-bold">
                        app.pixelcraftlk.com
                    </div>
                </div>

                <!-- Col 2: Solutions -->
                <div class="space-y-3 font-urbanist">
                    <h4 class="text-xs font-black uppercase tracking-wider text-slate-300">Solutions</h4>
                    <ul class="space-y-2 text-xs text-slate-400">
                        <li><a href="#solar-epc" class="hover:text-cyan-400 transition-colors">CEB Solar PV Sizing</a></li>
                        <li><a href="#features" class="hover:text-violet-400 transition-colors">Zoho Milestone Invoicing</a></li>
                        <li><a href="#pos-terminal" class="hover:text-cyan-400 transition-colors">80mm Counter Thermal POS</a></li>
                        <li><a href="#features" class="hover:text-violet-400 transition-colors">Statutory EPF/ETF Payroll</a></li>
                    </ul>
                </div>

                <!-- Col 3: Access -->
                <div class="space-y-3 font-urbanist">
                    <h4 class="text-xs font-black uppercase tracking-wider text-slate-300">Access & Company</h4>
                    <ul class="space-y-2 text-xs text-slate-400">
                        <li><a href="/app/login" class="hover:text-cyan-400 transition-colors">Workspace Sign In</a></li>
                        <li><a href="/app/register" class="hover:text-violet-400 transition-colors">Register Company</a></li>
                        <li><a href="#pricing" class="hover:text-cyan-400 transition-colors">Subscription Plans in LKR</a></li>
                        <li><a href="#process" class="hover:text-violet-400 transition-colors">Operational Framework</a></li>
                    </ul>
                </div>

                <!-- Col 4: Regional Compliance -->
                <div class="space-y-3 font-urbanist">
                    <h4 class="text-xs font-black uppercase tracking-wider text-slate-300">Compliance & Support</h4>
                    <p class="text-xs text-slate-400 leading-relaxed font-sans">
                        Multi-tenant database isolation. Native Sri Lankan LKR currency, VAT invoice compliance, and bank transfer support.
                    </p>
                    <div class="text-xs text-slate-400 font-sans">
                        Support: <a href="mailto:support@pixelcraftlk.com" class="text-cyan-400 hover:underline font-bold">support@pixelcraftlk.com</a>
                    </div>
                    <div class="text-[11px] text-emerald-400 font-bold flex items-center gap-1.5 pt-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Official Cloud Production System
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 gap-4 font-urbanist">
                <div>
                    © {{ date('Y') }} PixelCraft SaaS. All rights reserved.
                </div>
                <div class="flex items-center gap-6">
                    <a href="/privacy" class="hover:text-cyan-400 transition-colors">Privacy Policy</a>
                    <a href="/terms" class="hover:text-violet-400 transition-colors">Terms of Service</a>
                    <a href="/app/login" class="hover:text-cyan-400 transition-colors">Staff Launchpad</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Interactive Scripts & Framer Motion Engine -->
    <script type="module">
        import { animate, inView, stagger, spring } from 'https://cdn.jsdelivr.net/npm/motion@12.4.2/+esm';

        // 1. Framer Motion Scroll Reveals
        try {
            animate('[data-motion-hero]', 
                { opacity: [0, 1], y: [40, 0], scale: [0.96, 1] }, 
                { duration: 0.9, easing: spring({ stiffness: 120, damping: 20 }) }
            );

            inView('#process', () => {
                animate('.process-glass-card', 
                    { opacity: [0, 1], y: [60, 0] }, 
                    { delay: stagger(0.12), duration: 0.8, easing: spring({ stiffness: 100, damping: 18 }) }
                );
            });

            inView('.glass-card', (info) => {
                animate(info.target, 
                    { opacity: [0, 1], y: [25, 0] }, 
                    { duration: 0.6, easing: spring({ stiffness: 110, damping: 20 }) }
                );
            });
        } catch (e) {
            console.log('Framer Motion initialized with CSS fallback:', e);
        }

        // 2. Billing Period Switcher
        window.switchBilling = function(period) {
            const btnMonthly = document.getElementById('btnMonthly');
            const btnYearly = document.getElementById('btnYearly');
            const priceElements = document.querySelectorAll('.plan-price');
            const periodLabels = document.querySelectorAll('.period-label');
            const periodSubtexts = document.querySelectorAll('.period-subtext');

            if (period === 'yearly') {
                btnYearly.classList.add('bg-gradient-to-r', 'from-violet-600', 'to-indigo-600', 'text-white', 'shadow-md');
                btnYearly.classList.remove('text-slate-400');
                btnMonthly.classList.remove('bg-gradient-to-r', 'from-violet-600', 'to-indigo-600', 'text-white', 'shadow-md');
                btnMonthly.classList.add('text-slate-400');

                priceElements.forEach(el => {
                    const yearly = parseFloat(el.getAttribute('data-yearly'));
                    const monthlyEquiv = Math.round(yearly / 12);
                    el.innerText = 'Rs. ' + monthlyEquiv.toLocaleString();
                });
                periodLabels.forEach(el => el.innerText = '/ month, billed yearly');
                periodSubtexts.forEach(el => el.innerText = 'Save ~17% (2 Months Free)');
            } else {
                btnMonthly.classList.add('bg-gradient-to-r', 'from-violet-600', 'to-indigo-600', 'text-white', 'shadow-md');
                btnMonthly.classList.remove('text-slate-400');
                btnYearly.classList.remove('bg-gradient-to-r', 'from-violet-600', 'to-indigo-600', 'text-white', 'shadow-md');
                btnYearly.classList.add('text-slate-400');

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

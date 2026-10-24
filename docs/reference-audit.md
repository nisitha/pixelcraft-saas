# Phase 0 — Scalient Reference Website Audit

**Audit Date**: October 20, 2026  
**Auditor**: Senior Frontend Engineer & UI/UX Reverse-Engineering Specialist  
**Reference Website**: [https://scalient.webflow.io/](https://scalient.webflow.io/)  
**Target Website**: [https://pixelcraftlk.com/](https://pixelcraftlk.com/) (PixelCraft Studios)  
**Status**: Completed (Audit & Specification Only — No Code Implemented)

---

## 1. Executive Summary & Objective

The objective of this Phase 0 audit is to systematically reverse-engineer the observable visual design, layout architecture, typography, navigation patterns, scroll interactions, micro-animations, and responsive behavior of the reference website (**Scalient**). This audit serves as the technical blueprint for developing the high-performance, dark-aesthetic website for **PixelCraft Studios** (`pixelcraftlk.com`), utilizing 100% original PixelCraft branding, original assets, and original marketing content.

In strict adherence to project guidelines:
- **No application code was created or modified**.
- **No proprietary copyrighted copy, client logos, or private assets were downloaded or reused**.
- All visual systems, dimensions, colors, and layout structures were directly inspected via browser automation and DOM/CSS extraction.
- Clear distinctions are maintained between **verified observations**, **technical translations**, and **unresolved items**.

---

## 2. Inspection Methodology

The audit was conducted via automated headless browser subagents and direct DOM/CSS stylesheet extraction:
1. **Direct Browser Navigation**: Full crawl and interaction testing across Desktop (1440px+), Tablet (768px - 991px), and Mobile (375px - 479px) viewports.
2. **Interactive State Verification**: Testing navigation dropdowns, mega-menu expanders, mobile hamburger drawers, hover transitions, and pricing toggle switches.
3. **Computed Style & Token Parsing**: Extraction of all 176 CSS variables, root tokens, font declarations, and media queries from `scalient.webflow.shared.ee5e40e0e.css`.
4. **Visual Evidence Capture**: 9 high-resolution screenshots capturing critical page states across desktop, tablet, and mobile.

### Visual Evidence Captured
- `screenshots/desktop_hero_nav.png`: Desktop Hero section with navigation, badges, typography, and stat widgets.
- `screenshots/desktop_dropdown_open.png`: Desktop "All Pages" mega-dropdown menu open state.
- `screenshots/desktop_services_features.png`: Growth solutions and feature cards.
- `screenshots/desktop_work_portfolio.png`: "OUR BEST WORKS" project showcase cards.
- `screenshots/desktop_pricing_stats.png`: Pricing table with Monthly/Yearly billing toggle.
- `screenshots/desktop_accordion_faq.png`: Accordion and FAQ collapsible interaction states.
- `screenshots/desktop_footer.png`: 4-column footer layout, newsletter capture form, and legal links.
- `screenshots/mobile_hero.png`: Mobile viewport hero presentation (375px width).
- `screenshots/mobile_menu_open.png`: Mobile hamburger navigation drawer overlay.

---

## 3. Complete Homepage Section Order & Structural Inventory

The homepage is structured inside a primary container (`.pages-wrapper`) containing 12 distinct sections in exact top-to-bottom sequence:

| # | Section Identifier | CSS Class | Core Content & Headings | Key Components & Functionality |
|---|--------------------|-----------|-------------------------|--------------------------------|
| **1** | **Header / Navigation** | `.navbar.gray.w-nav` | Logo (`/`), Nav Links, Cart Button | Sticky blur header, "All Pages" mega-dropdown, "Contact Now" CTA button, eCommerce slide-out Cart modal |
| **2** | **Hero Section** | `.section.hero` | H2: *"Fueling the Next Generation of Brands"*<br>Badge: *"New — No Hidden Pricing"*<br>Subtitle: *"Powering bold ideas with strategy..."* | Double-layer animated text CTA ("Get Started"), Lightbox video trigger ("Watch Demo"), 3 animated counter cards, Client trust avatar row |
| **3** | **Editorial About Statement** | `.section.about-v1` | H3: *"At Scalient, we believe marketing isn't about selling it's about connecting..."* | High-contrast oversized editorial typography, CTA button: *"Learn More"* (`/about`) |
| **4** | **Best Works Showcase** | `.section.work` | H1: *"OUR BEST WORKS"* (Segmented typography header) | 5 Featured Project cards ("Growth Engine", "Market Magnet", "Market Motion", "Tidal Strategy", "Trend Beacon") with hover zoom & CTA "View Project" |
| **5** | **Trusted Brands Marquee** | `.section.trusted` | Title: *"Trusted by Leading Brands"* | Infinite horizontal marquee loop featuring 18 brand partner SVGs |
| **6** | **Growth Solutions (Services)** | `.section.growth` | H2: *"Solutions Tailored for Your Growth"* | 4 Service feature cards ("Content Marketing", "Marketing Automation", "Influencer Marketing", "Email Marketing") with vector iconography & hover lift |
| **7** | **Pricing & Plans** | `.section.plan` | Badge: *"Our Plans"*<br>H2: *"Pricing and Plans Tailored to any Team"* | Billing Cycle Switcher ("Monthly" / "Yearly"), 3 pricing tiers ("Beginners", "Professional", "Enterprise"), feature checklist, "Get Started" CTAs |
| **8** | **Metrics & Success Numbers** | `.section.number` | H2: *"The Numbers Behind Success"* | Dynamic counters (e.g. 98.05% quality rating, team scale, project counts), dark elevated cards, CTA: *"Get Started"* |
| **9** | **Feedback & Testimonials** | `.section.feedback` | H2: *"Real People, Real Results Feedback"* | Customer quote cards, client profile avatars, star ratings, horizontal testimonial carousel/slider |
| **10** | **Insights & Strategy (Blog)** | `.section.blog-v1` | H2: *"Market Insights and our Strategy"* | 3 Featured editorial cards with cover photography, author avatars, reading times, CTA: *"View Blogs"* (`/blog`) |
| **11** | **Infinite CTA Ribbon** | `.section.cta` | Continuous Ticker: *"Let's Get Started ✦ Let's Get Started ✦"* | High-velocity infinite ticker marquee banner with custom accent divider icons |
| **12** | **Global Footer** | `.section.footer` | H2: *"Sign up for our newsletter today."* | Email newsletter capture form with confirmation validation, 4 structured link columns, social media links, legal copyright bar |

---

## 4. Header & Navigation Architecture

### Desktop Header
- **Positioning**: Sticky header with `backdrop-filter: blur(12px)` and subtle semi-transparent dark background (`rgba(8, 13, 13, 0.85)`).
- **Brand Logo**: Left-aligned, links to `/`.
- **Primary Nav Links**:
  - `Company` (`/about`)
  - `All Pages` (Mega Dropdown Trigger)
  - `Works` (`/work`)
  - `Pricing` (`/pricing`)
  - `Resources` (`/blog`)
- **Right Action Cluster**:
  - `Contact Now` Primary Button (Electric lime accent background `#bcea3e` with black text `#080d0d` and dual-text hover animation).
  - `Cart` Button (Dark rounded pill with item counter badge '0' opening an eCommerce checkout drawer).

### "All Pages" Mega Menu Dropdown
Hovering or clicking "All Pages" exposes a categorized multi-column drawer:
- **Main Pages**: Home, Company, Works, Services, Pricing, Career, Contact
- **CMS Detail Pages**: Works Details, Pricing Details, Service Details, Career Details, Blog Single
- **Utility Pages**: Terms & Conditions, Privacy Policy, Style Guide, License, Changelog, Password Protected (401), Coming Soon, Request a Demo, 404

### Mobile Navigation (≤ 991px)
- Desktop links collapse into a custom hamburger button (`.menu-button.w-nav-button`).
- Hamburger click triggers a vertical mobile navigation overlay drawer covering full mobile height.
- Submenus expand via accordion style.
- Sticky header remains anchored at top with z-index `1000`.

---

## 5. Visual System & Aesthetics

### Dark Palette Philosophy
The reference website achieves a signature, high-end agency aesthetic by avoiding standard flat black (`#000000`). Instead, it uses a deep teal-tinted charcoal black (`#080d0d`) paired with layered dark elevation surfaces (`#111616`, `#151a1a`) and border strokes (`#222a2a` / `rgba(255,255,255,0.08)`).

### Accent & Interaction Colors
- **Signature Accent**: Electric Lime Green (`#bcea3e`) — used for primary action buttons, key highlighted badges, active toggle states, and visual focus points.
- **Secondary Accent**: Vibrant Indigo/Purple (`#7970ff`) — used for subtle gradients and secondary badges.
- **Warning / Alert**: Vivid Orange (`#ff5d2b`).
- **Body & Muted Text**: Slate Gray (`#6f7781` and `#899698`) on dark backgrounds ensures optimal readability without excessive contrast fatigue.

### Typography
- **Primary Typeface**: `Interdisplay` (geometric, clean, contemporary neo-grotesque sans-serif with tight negative tracking at large heading scales).
- **Decorative Script Typeface**: `Italianno` (used sparingly for cursive organic emphasis).
- **Heading Scale**: Ranging from H1 (`100px` desktop / `36px` mobile with `-4px` letter-spacing) down to H6 (`24px`).
- **Body Scale**: Paragraph L (`18px`), Paragraph M (`16px`), Paragraph S (`14px`).

---

## 6. Verified Observations vs Assumptions & Gaps

### Verified Observations (Inspected Directly)
1. **Exact Colors & Tokens**: All 176 CSS custom variables extracted directly from active production stylesheet.
2. **Exact DOM Structure**: 12 sections in sequential order inside `.pages-wrapper`.
3. **Button Hover Mechanism**: Double container dual-text sliding transition with arrow icon.
4. **All Internal URL Routes**: All 28 unique routes inventoried from live anchors.
5. **Responsive Breakpoints**: 991px (tablet), 767px (mobile landscape), 479px (mobile portrait).

### Assumptions & Technical Adaptations for PixelCraft
1. **CMS Backend**: Reference uses Webflow CMS (`w-dyn-list`). For PixelCraft, we will use modern headless Markdown/MDX or React CMS data structures.
2. **E-Commerce Cart**: Reference uses Webflow eCommerce cart. For PixelCraft, unless direct merchandise is sold, the cart button should be adapted to an interactive "Book a Project" or "Project Scope Estimator" drawer.
3. **Motion Engine**: Webflow IX2 interactions (`data-w-id`) will be translated into standard **GSAP + ScrollTrigger** and CSS keyframes for smoother 60fps hardware-accelerated performance.

---

## 7. Confirmation of Compliance

- **No application code was implemented or modified**.
- **No proprietary copyrighted assets, logos, or copy were downloaded or cloned**.
- All documentation is in clear, professional English.
- Stop condition met: Awaiting User Approval before initiating Phase 1.

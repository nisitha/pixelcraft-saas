# PixelCraft Studios — Asset Inventory & Replacement Plan

**Objective**: Comprehensive catalog of all asset types required to build the PixelCraft website matching Scalient's design fidelity, with 100% compliant, original PixelCraft branding and high-resolution assets.

---

## 1. Compliance & Asset Guidelines

In strict compliance with project rules:
1. **Zero Copying of Restricted Assets**: No private client logos, copyrighted photography, proprietary illustrations, or author headshots from Scalient may be used.
2. **Original Vector & SVG Architecture**: All icons, UI arrows, badges, checkmarks, and brand marks must be clean, lightweight SVGs or modern icon libraries (Lucide React / Heroicons / custom SVG paths).
3. **Optimized Formats**: All photographic assets must be WebP / AVIF format with responsive `srcset` and explicit `width`/`height` attributes to eliminate Cumulative Layout Shift (CLS).

---

## 2. Asset Type Inventory & Substitution Strategy

| Asset Category | Reference Observation | Format | PixelCraft Replacement Strategy | Target Destination |
|---|---|---|---|---|
| **Primary Logo** | Scalient wordmark + geometric symbol | SVG | Custom PixelCraft Studios logo (`PixelCraft ✦`) in Electric Lime (`#bcea3e`) and White | `public/brand/logo.svg` |
| **Favicon & Webclip** | Scalient branded favicon / app touch icon | ICO / PNG | PixelCraft "PC" monogram icon in dark background with lime spark | `public/favicon.ico`, `public/apple-touch-icon.png` |
| **Navigation Icons** | Arrow dropdown, chevron right, cart bag | SVG | Clean modern SVG icons (16px & 20px) | `components/icons/*.svg` |
| **Partner / Brand Logos** | 18 Client brand logos in Marquee loop | Monochrome SVG | 12–16 Curated partner/technology logos (e.g. Next.js, React, Tailwind, Stripe, AWS, Vercel, Supabase, Figma) | `public/partners/*.svg` |
| **Hero Visual / Mockup** | 3D agency composition / metric card visual | WebP / CSS | Custom interactive PixelCraft dark mode dashboard mockup or 3D composition with glowing accents | `public/hero/hero-visual.webp` |
| **Feature / Service Icons** | Growth Card Icons (Strategy, Automation, Influencer, Content) | SVG | 4 Custom PixelCraft service icons with dual-tone lime & slate gradient strokes | `public/services/*.svg` |
| **Portfolio Showcase** | 5 Project imagery covers ("Growth Engine", "Market Magnet", etc.) | WebP (16:9 / 4:3) | 5 High-resolution original PixelCraft case study covers showcasing real web, SaaS, and mobile projects | `public/portfolio/*.webp` |
| **Client Testimonial Avatars** | Author profile headshots | WebP (1:1, 100x100) | Professional, licensed/royalty-free studio portraits of diverse founders and executives | `public/testimonials/*.webp` |
| **Blog Article Covers** | Editorial marketing graphics | WebP (16:9, 800x450) | Clean dark-themed conceptual graphics illustrating modern tech, performance, and UI/UX | `public/blog/*.webp` |
| **Checkmark & Status Icons** | Feature list checkmarks (grey & green) | SVG | Minimalist circular checkmark SVGs | `components/icons/check-circle.svg` |
| **Video Play / Lightbox Icon** | Play icon inside circular ring | SVG | Play button with subtle pulse ring | `components/icons/play.svg` |
| **Social Media Icons** | Facebook, X (Twitter), Instagram, LinkedIn | SVG | Standard clean official social platform marks in monochrome white/gray | `components/icons/social/*.svg` |

---

## 3. Font Asset Specification

The reference site utilizes modern neo-grotesque and script typefaces:

1. **Primary Font**: `Inter` / `Inter Display`
   - Source: Google Fonts / Self-hosted WOFF2
   - Weights: 400 (Regular), 500 (Medium), 600 (Semi-bold), 700 (Bold)
   - Fallback: `system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif`
2. **Decorative Script Font**: `Italianno`
   - Source: Google Fonts (Open Source SIL Open Font License)
   - Usage: Accent words in headings (e.g. *"connecting"*, *"purpose"*)
   - Fallback: `cursive`

---

## 4. Image Aspect Ratios & Optimization Rules

| Usage Context | Aspect Ratio | Dimensions (Desktop) | Dimensions (Mobile) | Format |
|---|---|---|---|---|
| **Portfolio Card** | `16:10` or `4:3` | `720px x 450px` | `375px x 250px` | WebP (q=85) |
| **Blog Card** | `16:9` | `640px x 360px` | `340px x 190px` | WebP (q=85) |
| **Testimonial Avatar** | `1:1` (Circle) | `64px x 64px` | `56px x 56px` | WebP (q=90) |
| **Author Avatar** | `1:1` (Circle) | `48px x 48px` | `40px x 40px` | WebP (q=90) |
| **Partner Logo** | Fit within box | Max `140px x 40px` | Max `100px x 32px` | SVG (Vector) |
| **Hero Dashboard Mockup**| `16:10` | `1100px x 680px` | `375px x 240px` | WebP + CSS shadows |

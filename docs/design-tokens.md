# PixelCraft Studios — Design Tokens Specification

**Source**: Extracted from Scalient Reference Stylesheet (`scalient.webflow.shared.ee5e40e0e.css`)  
**Application**: PixelCraft Studios Modern Web Design System  
**Format**: CSS Custom Properties / Tailwind Theme Extension Tokens

---

## 1. Color Palette System

The design uses an ultra-refined dark aesthetic with high-contrast lime highlights and subtle slate accents.

### Core Background & Surface Colors

| Token Name | Hex Code | Purpose / Usage | Contrast Context |
|---|---|---|---|
| `--dark-color` | `#080d0d` | Base Page Background | Primary backdrop across all pages |
| `--dark-field` | `#111616` | Card Surfaces, Form Inputs, Badges | Level 1 elevated container background |
| `--surface-elevated` | `#151a1a` | Elevated Cards, Modals, Dropdowns | Level 2 elevated surfaces |
| `--border-dark` | `rgba(255, 255, 255, 0.08)` / `#222a2a` | Card borders, divider rules | Subtle depth separation |
| `--border-subtle` | `#dedede` | Light border variant / active stroke | Used for high-contrast accents |

### Accent & Feedback Colors

| Token Name | Hex Code | Purpose / Usage |
|---|---|---|
| `--primary-color` | `#bcea3e` | Electric Lime Green — Primary CTA buttons, key highlight text, active pills |
| `--blue-color` | `#7970ff` | Vibrant Indigo / Purple — Secondary badges, subtle glow gradients |
| `--warnning-color` | `#ff5d2b` | Alert / Urgent Notice — Status indicators |
| `--white` | `#ffffff` | Primary Headings, high-contrast titles |
| `--dark-gray` | `#6f7781` | Secondary headings, subtitle text, icons |
| `--light-gray` | `#899698` | Body paragraphs, muted captions, placeholder text |
| `--light-field` | `#f1f1f1` | Inverted surfaces (light mode accents) |

---

## 2. Typography System

### Font Families
- **Primary Font**: `Interdisplay, Inter, Arial, sans-serif`
  - Clean, modern, geometric neo-grotesque sans-serif with tight kerning.
- **Accent / Script Font**: `Italianno, cursive, sans-serif`
  - Decorative cursive typography used for select organic artistic flourishes.
- **Monospace Font**: `ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace`

### Heading Typography Scale (Desktop vs Mobile)

| Level | Desktop Size | Mobile Size | Line Height | Letter Spacing (Tracking) | Weight |
|---|---|---|---|---|---|
| **H1** | `100px` (`6.25rem`) | `36px` (`2.25rem`) | `110%` (`1.1`) | `-4px` (Desktop) / `-1.5px` (Mobile) | `600` / `700` |
| **H2** | `76px` (`4.75rem`) | `32px` (`2.0rem`) | `110%` (`1.1`) | `-3.8px` (Desktop) / `-1.0px` (Mobile) | `600` |
| **H3** | `40px` (`2.5rem`) | `30px` (`1.875rem`) | `120%` (`1.2`) | `-.8px` | `500` / `600` |
| **H4** | `34px` (`2.125rem`) | `26px` (`1.625rem`) | `130%` (`1.3`) | `-.34px` | `500` |
| **H5** | `30px` (`1.875rem`) | `24px` (`1.5rem`) | `130%` (`1.3`) | `-.6px` | `500` |
| **H6** | `24px` (`1.5rem`) | `20px` (`1.25rem`) | `140%` (`1.4`) | `0px` | `500` |

### Body Paragraph Scale

| Level | Size | Line Height | Letter Spacing | Weight |
|---|---|---|---|---|
| **Paragraph XL** | `18px` (`1.125rem`) | `150%` | `-0.2px` | `400` / `500` |
| **Paragraph L** | `18px` (`1.125rem`) | `140%` | `0px` | `400` |
| **Paragraph M** (Default Body) | `16px` (`1.0rem`) | `140%` | `0px` | `400` |
| **Paragraph S** (Captions/Meta) | `14px` (`0.875rem`) | `140%` | `0px` | `400` |

---

## 3. Spacing & Layout Tokens

### Layout Containers
- **Max Container Width (Desktop XL)**: `1280px`
- **Standard Container Width**: `1200px`
- **Page Side Padding (Desktop)**: `40px`
- **Page Side Padding (Tablet)**: `24px`
- **Page Side Padding (Mobile)**: `16px`

### Vertical Section Spacing
- **Standard Section Gap (Desktop)**: `120px` (`--_gap---gap-7xll` / `8xll`)
- **Medium Section Gap**: `80px` / `60px`
- **Mobile Section Gap**: `60px` / `40px`

### Gap Scale Tokens

```css
--_gap---gap-none: 0px;
--_gap---gap-xs: 15px;
--_gap---gap-s: 20px;
--_gap---gap-m: 24px;
--_gap---gap-l: 30px;
--_gap---gap-xl: 40px;
--_gap---gap-2xll: 50px;
--_gap---gap-6xll: 60px;
--_gap---gap-7xll: 120px;
```

---

## 4. Border Radius & Elevation Tokens

### Border Radiuses

```css
--_border-radius---border-radius-s: 10px;    /* Badges, sub-elements */
--_border-radius---border-radius-m: 12px;    /* Cards, dropdowns, inputs */
--_border-radius---border-radius-l: 16px;    /* Large feature containers */
--_border-radius---border-radius-2xl: 20px;   /* Modal windows, hero widgets */
--_border-radius---border-radius-4xl: 999px;  /* Pill buttons, pill tags */
```

### Shadows & Glows

```css
--shadow-card: 0 4px 24px -1px rgba(0, 0, 0, 0.4);
--shadow-elevated: 0 12px 48px -4px rgba(0, 0, 0, 0.6);
--glow-primary: 0 0 25px rgba(188, 234, 62, 0.25);
```

---

## 5. Interactive Component Dimensions

### Primary Action Button (`.primary-button`)
- **Height**: `52px`
- **Padding**: `14px 28px`
- **Border Radius**: `999px` (Pill)
- **Background**: `#bcea3e` (Electric Lime)
- **Text Color**: `#080d0d` (Dark charcoal)
- **Font Size**: `16px` (`font-weight: 600`)
- **Icon Dimensions**: `18px x 18px` (right arrow)
- **Dual Text Hover Structure**:
  - Two stacked text containers (`._01` and `._02`) inside an overflow-hidden wrapper.
  - On hover: `._01` slides `translateY(-100%)` and `._02` slides from `translateY(100%)` to `translateY(0%)`.

### Pill Badge (`.sub-title-wrap`)
- **Height**: `36px`
- **Padding**: `6px 14px`
- **Border Radius**: `999px`
- **Background**: `#111616` with `1px solid rgba(255, 255, 255, 0.1)`
- **Badge Tag ("New")**: `#bcea3e` text with subtle border

### Pricing Card (`.plan-card`)
- **Padding**: `36px 32px`
- **Border Radius**: `16px`
- **Background**: `#111616` (Normal) / `#151a1a` (Featured)
- **Border**: `1px solid rgba(255, 255, 255, 0.08)` (Normal) / `1px solid #bcea3e` (Featured)

---

## 6. Responsive Breakpoint Specification

| Breakpoint Name | Media Query | Layout Adjustments |
|---|---|---|
| **Desktop Large** | `@media screen and (min-width: 1280px)` | Max container `1280px`, full grid columns |
| **Desktop Standard**| `@media screen and (min-width: 992px)` | Full desktop navigation, 3-column pricing, 4-column services |
| **Tablet** | `@media screen and (max-width: 991px)` | Navigation collapses to hamburger, 2-column grids, H1 reduces to 64px |
| **Mobile Landscape**| `@media screen and (max-width: 767px)` | Single-column cards, section spacing reduces to 60px |
| **Mobile Portrait** | `@media screen and (max-width: 479px)` | H1 scales to 36px, full-width buttons, horizontal padding 16px |

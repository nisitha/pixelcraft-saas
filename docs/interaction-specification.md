# PixelCraft Studios — Interaction & Animation Specification

**Reference**: Scalient Webflow Interactions (`data-w-id`, `data-ix2-`)  
**Target Implementation**: Modern Web Application (CSS Transitions, GSAP ScrollTrigger, React State)

---

## 1. Technical Implementation Classification Matrix

To ensure maximum runtime performance (60fps), zero layout shifts (CLS = 0), and clean code separation, all interactions are categorized into their optimal implementation layer:

| Interaction Name | Trigger / Event | Mechanism | Technology Layer | Performance Impact |
|---|---|---|---|---|
| **Sticky Blur Header** | Window scroll > 50px | CSS class toggle / Backdrop filter | CSS + React scroll listener | Negligible (GPU accelerated) |
| **Dual-Text Button Hover** | Mouse enter / leave | `translateY` offset on stacked text spans | Pure CSS (`transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1)`) | Zero layout reflow |
| **Mega Dropdown Menu** | Click / Hover toggle | Height expand + opacity fade | React State + CSS transition | Zero reflow |
| **Initial Hero Reveal** | Initial Page Load | Staggered fade in & slide up (`y: 30 -> 0`, `opacity: 0 -> 1`) | GSAP timeline / CSS keyframes | High visual impact |
| **Infinite Brand Marquee** | Continuous automatic | Seamless horizontal loop (`translateX: 0% -> -50%`) | Pure CSS `@keyframes` | Lightweight (hardware composited) |
| **Portfolio Card Hover** | Pointer hover on project card | Image zoom (`scale: 1.05`), overlay tint, button reveal | CSS `transform`, `transition` | GPU accelerated |
| **Pricing Billing Toggle** | Click Monthly / Yearly | State toggle, price calculation & cross-fade | React State + CSS Transition | Instantaneous |
| **Animated Number Counters** | Section scroll into viewport | Number interpolation from `0` to target (e.g. `98.05%`) | GSAP ScrollTrigger or lightweight counter hook | Triggered once on scroll |
| **Infinite CTA Ribbon** | Continuous automatic | Seamless text marquee with divider star icon | Pure CSS `@keyframes` | Zero CPU load |
| **Testimonial Carousel** | Drag / Arrow click / Auto-play | Horizontal slide translation | Swiper.js or React custom carousel | Smooth touch gestures |
| **Mobile Navigation Drawer** | Hamburger button click | Slide in from right / top + backdrop lock | React State + CSS `transform` | Smooth mobile transition |
| **FAQ Accordion** | Click question header | Height transition `0 -> auto` with icon rotation | React state / CSS `grid-template-rows: 0fr -> 1fr` | Clean, modern CSS expansion |

---

## 2. Interaction Specifications in Detail

### 1. Initial Page-Load & Hero Reveal
- **Observed Behavior**:
  - The hero elements do not abruptly pop into view. They start at `opacity: 0` and `transform: translateY(24px)`.
  - In sequence:
    1. Navigation bar fades in (`0ms` delay, `600ms` duration).
    2. Pill badge ("New — No Hidden Pricing") fades in (`150ms` delay, `500ms` duration).
    3. H2 Heading ("Fueling the Next Generation of Brands") slides up into view (`300ms` delay, `700ms` duration).
    4. Subtitle paragraph and CTA button cluster fade up (`450ms` delay, `600ms` duration).
    5. Stat cards and trust avatars reveal with a gentle stagger (`600ms` delay).
- **Easing Curve**: `cubic-bezier(0.22, 1, 0.36, 1)` (smooth ease-out).

### 2. Dual-Layer Text Button Hover (`.primary-button`)
- **Structure**:
  ```html
  <a class="primary-button">
    <div class="primary-btn-wrap">
      <div class="primary-btn-text-wrap _01">
        <span>Get Started</span>
        <svg class="primary-icon">...</svg>
      </div>
      <div class="primary-btn-text-wrap _02">
        <span>Get Started</span>
        <svg class="primary-icon">...</svg>
      </div>
    </div>
  </a>
  ```
- **CSS Specification**:
  ```css
  .primary-btn-wrap {
    position: relative;
    overflow: hidden;
    height: 24px;
  }
  .primary-btn-text-wrap._01 {
    display: flex;
    align-items: center;
    gap: 8px;
    transform: translateY(0%);
    transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .primary-btn-text-wrap._02 {
    display: flex;
    align-items: center;
    gap: 8px;
    position: absolute;
    top: 0;
    left: 0;
    transform: translateY(100%);
    transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
  }
  .primary-button:hover .primary-btn-text-wrap._01 {
    transform: translateY(-100%);
  }
  .primary-button:hover .primary-btn-text-wrap._02 {
    transform: translateY(0%);
  }
  ```

### 3. Infinite Trusted Brands Marquee (`.section.trusted`)
- **Observed Behavior**: Horizontal continuous scrolling banner displaying partner logos.
- **Specification**:
  - Two identical rows of SVG logos placed side-by-side inside an overflow-hidden wrapper.
  - Linear animation infinitely translating `translateX(0)` to `translateX(-50%)`.
  - Pause on hover: `@media (hover: hover) { .marquee-track:hover { animation-play-state: paused; } }`.
  - Duration: `30s` linear infinite.
  - Mask: Left and right edge gradient fades (`linear-gradient(to right, #080d0d 0%, transparent 15%, transparent 85%, #080d0d 100%)`).

### 4. Best Works / Portfolio Card Interactions (`.section.work`)
- **Observed Behavior**:
  - Cards feature a high-contrast dark card container with rounded corners (`16px`).
  - On cursor enter:
    - Inner project image scales smoothly by `1.04x` over `0.6s ease`.
    - Dark vignette gradient deepens from `0.3` to `0.55` opacity.
    - "View Project" pill button glows with accent border (`#bcea3e`).

### 5. Pricing Billing Cycle Toggle (`.section.plan`)
- **Observed Behavior**:
  - Pill switcher between "Monthly" and "Yearly".
  - Active pill has a subtle sliding highlight indicator with `#151a1a` or `#bcea3e` background.
  - Switching toggles numerical prices instantly with a brief 200ms opacity cross-fade.
  - "Yearly" toggle displays a small green discount tag ("Save 20%").

### 6. Scroll-Triggered Metric Counters (`.section.number`)
- **Observed Behavior**:
  - Numbers start at `0` when scrolled into view.
  - Smoothly interpolate up to the final values (e.g. `98.05%`, `250+`, `10x`, `24/7`).
  - Duration: `1.8s` with `power2.out` easing.
  - Triggers once per page session using `IntersectionObserver` or GSAP ScrollTrigger `once: true`.

### 7. Infinite CTA Ribbon Marquee (`.section.cta`)
- **Observed Behavior**:
  - Full-width dark band running above the footer.
  - Repeated text: `Let's Get Started ✦ Let's Get Started ✦ Let's Get Started`.
  - Fast, energetic linear marquee movement (`duration: 18s`).

### 8. Mobile Navigation Drawer (≤ 991px)
- **Observed Behavior**:
  - Hamburger button features 2-bar or 3-bar animated icon that morphs into an 'X' on click.
  - Mobile menu slides down/across with dark backdrop blur (`rgba(8, 13, 13, 0.96)`).
  - Body scrolling is locked while the drawer is open (`overflow: hidden`).
  - Links have large touch targets (min height `48px`) for effortless finger taps.

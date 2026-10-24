# Scalient & PixelCraft Website Page Inventory

**Project**: Scalient Reverse-Engineering & PixelCraft Target Architecture  
**Audit Source**: Navigation Links, Mega Dropdown, Footer, and Sitemap Crawl  
**Total Pages Inventoried**: 28 Unique Routes

---

## 1. Page Category Overview

The website pages are partitioned into 4 distinct architectural layers:
1. **Core Brand & Conversion Pages** (Marketing & Lead Gen)
2. **Dynamic CMS Template Pages** (Projects, Services, Careers, Blog)
3. **Commerce & Pricing Pages** (Subscription tiers / Service checkout)
4. **Utility & System Pages** (Legal, 404, 401, Coming Soon, Styleguide)

---

## 2. Comprehensive Route Matrix

| Category | Reference URL Path | Page Title / Purpose | Key Structural Elements & Sections | Target PixelCraft Adaptation |
|---|---|---|---|---|
| **Core** | `/` | Home | Hero, About Statement, Best Works, Marquee, Growth Services, Pricing, Stats, Testimonials, Blog, CTA, Footer | PixelCraft Studios Homepage |
| **Core** | `/about` | Company / About Us | Agency story, leadership team, mission, culture, capabilities, values, timeline | About PixelCraft Studios |
| **Core** | `/work` | Works / Portfolio | Grid/filter of agency case studies, category tabs, featured client transformations | PixelCraft Client Showcase |
| **Core** | `/service` | Services Overview | Comprehensive breakdown of services (Web, Branding, SaaS, Growth, AI) | PixelCraft Services Menu |
| **Core** | `/pricing` | Pricing & Packages | Detailed monthly/yearly tier comparison, feature matrix, FAQ accordion | PixelCraft Agency Plans & Retainers |
| **Core** | `/career` | Career / Open Roles | Company perks, culture, open job positions list, application CTA | Careers at PixelCraft |
| **Core** | `/contact` | Contact & Inquiries | Interactive inquiry form, office location, direct email, phone, schedule call | PixelCraft Project Consultation |
| **Core** | `/request-a-demo` | Request a Demo | Specialized discovery / consultation booking flow | Project Discovery Session |
| **CMS** | `/work/[slug]` | Work / Case Study Details | Client brief, challenge, strategy, visual mockup carousel, results/metrics, next project link | PixelCraft Case Study Template (e.g. `market-magnet`, `growth-engine`) |
| **CMS** | `/service/[slug]`| Service Detail Page | In-depth service breakdown, process methodology, deliverables checklist, related work | Individual Service Landing (e.g. `automation-marketing`) |
| **CMS** | `/career/[slug]` | Career Job Spec | Job responsibilities, requirements, benefits, application modal | Individual Job Listing (e.g. `seo-specialist`) |
| **CMS** | `/blog` | Blog / Resources Index | Category filters, featured article hero, pagination/infinite scroll, newsletter capture | PixelCraft Engineering & Design Blog |
| **CMS** | `/blog/[slug]` | Blog Article Post | Editorial layout, table of contents, author bio, social share, related posts | PixelCraft Technical Article Template |
| **Commerce**| `/product/beginners` | Starter Package | Plan overview, deliverables checklist, purchase/retainer checkout trigger | PixelCraft Starter Retainer |
| **Commerce**| `/product/professional` | Professional Package | Mid-tier plan specifications, SLA details, add-on options | PixelCraft Growth Retainer |
| **Commerce**| `/product/enterprise` | Enterprise Package | High-volume custom engagement, dedicated team scope, custom quote CTA | PixelCraft Enterprise Solutions |
| **Commerce**| `/checkout` | Checkout Drawer/Page | Order summary, client details, payment processing gateway | Inquiry Scope Confirmation |
| **Utility** | `/terms-condition` | Terms of Service | Legal terms, client service agreements, IP rights | PixelCraft Legal Terms |
| **Utility** | `/privacy-policy` | Privacy Policy | Data privacy regulations, GDPR compliance, cookie policy | PixelCraft Privacy Policy |
| **Utility** | `/license` | License Information | Typography licenses, icon credits, media attribution | PixelCraft Asset Licenses |
| **Utility** | `/changelog` | Changelog | Version history, platform updates, release notes | PixelCraft Release Notes |
| **Utility** | `/style-guide` | Style Guide | Internal design system tokens, typography scales, buttons, form inputs | PixelCraft Living UI Style Guide |
| **Utility** | `/coming-soon` | Coming Soon | Teaser countdown, early access newsletter waitlist | Coming Soon Landing |
| **Utility** | `/401` | Password Protected | Password gate form for private / NDA client work | Private Client Portal Gate |
| **Utility** | `/404` | 404 Not Found | Branded 404 graphic, error message, return to home button | PixelCraft Custom 404 Page |

---

## 3. Navigation Hierarchy & Menu Link Mapping

### Header Primary Bar
1. **Logo** -> `/`
2. **Company** -> `/about`
3. **All Pages (Mega Dropdown)**:
   - **Column 1 — Main Pages**:
     - Home (`/`)
     - Company (`/about`)
     - Works (`/work`)
     - Services (`/service`)
     - Pricing (`/pricing`)
     - Career (`/career`)
     - Contact (`/contact`)
   - **Column 2 — Detail Pages**:
     - Works Details (`/work/market-magnet`)
     - Pricing Details (`/product/beginners`)
     - Service Details (`/service/automation-marketing`)
     - Career Details (`/career/seo-specialist`)
     - Blog Single (`/blog/fatal-flaw-in-growth-strategy`)
   - **Column 3 — Utility Pages**:
     - Terms & Conditions (`/terms-condition`)
     - Privacy Policy (`/privacy-policy`)
     - Style Guide (`/style-guide`)
     - License (`/license`)
     - Changelog (`/changelog`)
     - Password Protected (`/401`)
     - Coming Soon (`/coming-soon`)
     - Request a Demo (`/request-a-demo`)
     - 404 (`/404`)
4. **Works** -> `/work`
5. **Pricing** -> `/pricing`
6. **Resources** -> `/blog`
7. **Contact Now (CTA)** -> `/contact`
8. **Cart Button** -> `/checkout` / Slide-out Cart Drawer

### Footer Link Columns
1. **Newsletter Banner**: Title `"Sign up for our newsletter today."` + Email input (`id="email-form"`) + Reassurance copy `"No spam, Just valued update."`
2. **Column 1 (Main Pages)**: Home, Company, Works, Services, Pricing, Career, Contact
3. **Column 2 (Inner Pages)**: Works Details, Pricing Details, Service Details, Career Details
4. **Column 3 (Utility Pages)**: Terms & Condition, Privacy Policy, License, Change Log, Style Guide, Password, Coming Soon, Request a Demo, 404
5. **Column 4 (Social Media)**: Facebook, X (Twitter), Instagram
6. **Sub-Footer Legal Bar**: Privacy policy, Terms of Use, License, Copyright PixelCraft Studios

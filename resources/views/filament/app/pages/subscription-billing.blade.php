<x-filament-panels::page>
    @php
        $tenant = $this->getTenant();
        $isSubActive = $tenant ? $tenant->isSubscriptionActive() : false;
        $expiryDate = $tenant?->subscription_ends_at;
        $timeInfo = $this->getHumanReadableTimeRemaining();

        $plans = $this->getAvailablePlans();
        $currentPlan = $tenant?->plan ?? ($plans->firstWhere('id', $this->plan_id) ?? $plans->first());
        $currentPlanPrice = (float) ($currentPlan?->price_monthly ?? 7500.00);

        $usedUsers = $this->getUsedUsersCount();
        $maxUsers = $this->getMaxUsersCount();
        $userPct = min(100, (int) round(($usedUsers / max(1, $maxUsers)) * 100));

        $quotationsCount = $this->getQuotationsCount();
        $requests = $this->getSubscriptionRequests();

        $amountLkr = $this->getEstimatedAmount();
        $amountUsd = $this->getEstimatedAmountUsd();
    @endphp

    {{-- Linear / Vercel-Style Scoped Minimalist CSS --}}
    <style>
        .sub-root {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #101828;
            max-width: 1060px;
            margin: 0 auto;
            padding: 4px 0 32px;
        }
        .sub-root * {
            box-sizing: border-box;
        }

        /* Strict SVG dimensions */
        .sub-root svg {
            max-width: 24px !important;
            max-height: 24px !important;
            flex-shrink: 0 !important;
            display: inline-block !important;
            vertical-align: middle !important;
        }
        .sub-root svg.icon-xs {
            width: 13px !important; height: 13px !important;
        }
        .sub-root svg.icon-sm {
            width: 16px !important; height: 16px !important;
        }
        .sub-root svg.icon-md {
            width: 20px !important; height: 20px !important;
        }

        /* 1. Clean Header */
        .sub-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
            gap: 16px;
            flex-wrap: wrap;
        }
        .sub-title {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.02em;
            margin: 0;
            line-height: 1.2;
        }
        .sub-subtitle {
            font-size: 13px;
            color: #64748b;
            margin-top: 4px;
        }
        .btn-change-plan {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid #0284c7;
            color: #0284c7;
            background: #ffffff;
            border-radius: 8px;
            padding: 6px 14px;
            font-weight: 500;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.15s ease;
            box-shadow: 0 1px 2px rgba(2, 132, 199, 0.05);
            text-decoration: none;
        }
        .btn-change-plan:hover {
            background: #f0f9ff;
            border-color: #0369a1;
            color: #0369a1;
        }

        /* 2. Soft Blue Notice Banner */
        .sub-banner-active {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 12px;
            padding: 12px 18px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }
        .sub-banner-expired {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 12px 18px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }
        .banner-info-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .banner-icon-circle {
            width: 28px;
            height: 28px;
            min-width: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e0f2fe;
            color: #0284c7;
        }
        .banner-icon-circle.danger {
            background: #fee2e2;
            color: #dc2626;
        }
        .banner-title {
            font-size: 13px;
            font-weight: 700;
            color: #0369a1;
            line-height: 1.3;
        }
        .banner-title.danger {
            color: #b91c1c;
        }
        .banner-sub {
            font-size: 12px;
            color: #0284c7;
            margin-top: 1px;
        }
        .banner-sub.danger {
            color: #dc2626;
        }
        .banner-action-link {
            font-size: 12px;
            font-weight: 600;
            color: #0284c7;
            text-decoration: none;
            cursor: pointer;
            padding: 5px 12px;
            border-radius: 6px;
            background: #ffffff;
            border: 1px solid #bae6fd;
            transition: all 0.15s ease;
            white-space: nowrap;
        }
        .banner-action-link:hover {
            background: #e0f2fe;
            color: #0369a1;
        }

        /* 3. Primary Hero Card */
        .sub-hero-card {
            background: #ffffff;
            border: 1px solid #eaecf0;
            border-radius: 14px;
            padding: 24px 28px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, 0.05);
            transition: box-shadow 0.2s ease;
        }
        .sub-hero-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            flex-wrap: wrap;
        }
        .plan-title-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .plan-name {
            font-size: 22px;
            font-weight: 700;
            color: #101828;
            letter-spacing: -0.02em;
            margin: 0;
            line-height: 1.2;
        }
        .badge-active {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
            border-radius: 100px;
            padding: 2px 10px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .badge-expired {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
            border-radius: 100px;
            padding: 2px 10px;
            font-size: 12px;
            font-weight: 600;
        }
        .plan-subtitle {
            color: #667085;
            font-size: 13px;
            margin-top: 5px;
        }
        .plan-price-wrap {
            text-align: right;
        }
        .plan-price-num {
            font-size: 26px;
            font-weight: 700;
            color: #101828;
            letter-spacing: -0.02em;
        }
        .plan-price-period {
            font-size: 16px;
            color: #667085;
            font-weight: 400;
        }
        .hero-divider {
            border-top: 1px solid #f2f4f7;
            margin-top: 20px;
            padding-top: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .annual-upgrade-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #475467;
            cursor: pointer;
            text-decoration: none;
            font-weight: 500;
            background: none;
            border: none;
            padding: 0;
            transition: color 0.15s;
        }
        .annual-upgrade-link:hover {
            color: #0284c7;
        }
        .badge-save {
            background: #fffaeb;
            color: #b54708;
            border: 1px solid #fef08a;
            border-radius: 100px;
            padding: 1px 8px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 6px;
        }
        .toggle-details-btn {
            background: none;
            border: none;
            color: #0284c7;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 8px;
            border-radius: 6px;
            transition: background 0.15s;
        }
        .toggle-details-btn:hover {
            background: #f0f9ff;
        }

        /* 4. Two-Column Usage & Metric Cards Grid */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }
        @media (max-width: 768px) {
            .metrics-grid {
                grid-template-columns: 1fr;
            }
        }
        .metric-card {
            background: #ffffff;
            border: 1px solid #eaecf0;
            border-radius: 14px;
            padding: 20px 24px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, 0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .metric-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }
        .metric-icon-box {
            width: 36px;
            height: 36px;
            min-width: 36px;
            border-radius: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #475467;
        }
        .metric-title {
            font-size: 15px;
            font-weight: 600;
            color: #101828;
            margin: 0;
            line-height: 1.3;
        }
        .metric-subtitle {
            font-size: 12px;
            color: #667085;
            margin-top: 1px;
        }
        .metric-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
        }
        .metric-label {
            color: #475467;
            font-weight: 500;
        }
        .metric-count {
            font-weight: 700;
            color: #059669;
        }
        .progress-bar-bg {
            background: #f2f4f7;
            height: 6px;
            border-radius: 100px;
            margin: 10px 0 16px;
            overflow: hidden;
        }
        .progress-bar-fill {
            background: #10b981;
            height: 100%;
            border-radius: 100px;
            transition: width 0.3s ease;
        }
        .metric-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 12px;
            border-top: 1px solid #f2f4f7;
            font-size: 12px;
        }
        .metric-footer-text {
            color: #667085;
        }
        .btn-pill-action {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            border: 1px solid #bae6fd;
            color: #0284c7;
            background: #f0f9ff;
            border-radius: 100px;
            padding: 4px 12px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .btn-pill-action:hover {
            background: #e0f2fe;
            border-color: #7dd3fc;
            color: #0369a1;
        }

        /* 5. Payment Drawer & Checkout Card */
        .payment-drawer-card {
            background: #ffffff;
            border: 1px solid #eaecf0;
            border-radius: 14px;
            padding: 24px 28px;
            margin-bottom: 24px;
            box-shadow: 0 4px 16px rgba(16, 24, 40, 0.08);
        }
        .drawer-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 18px;
            border-bottom: 1px solid #f2f4f7;
            margin-bottom: 20px;
            gap: 16px;
            flex-wrap: wrap;
        }
        .drawer-title {
            font-size: 17px;
            font-weight: 700;
            color: #101828;
            letter-spacing: -0.01em;
            margin: 0;
        }
        .drawer-sub {
            font-size: 13px;
            color: #667085;
            margin-top: 3px;
        }

        /* Plan & Cycle Selectors */
        .plan-selector-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }
        @media (max-width: 640px) {
            .plan-selector-grid {
                grid-template-columns: 1fr;
            }
        }
        .form-label-xs {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #475467;
            margin-bottom: 6px;
        }
        .form-select-clean {
            width: 100%;
            padding: 9px 12px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            border: 1px solid #d0d5dd;
            background-color: #ffffff;
            color: #101828;
            outline: none;
            transition: border-color 0.15s;
        }
        .form-select-clean:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
        }

        /* Live Total Payable Banner */
        .live-total-box {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-radius: 10px;
            padding: 16px 20px;
            color: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 12px;
            flex-wrap: wrap;
        }
        .total-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #94a3b8;
        }
        .total-amount-lkr {
            font-size: 22px;
            font-weight: 800;
            color: #38bdf8;
            margin-top: 2px;
        }
        .total-amount-usd {
            font-size: 13px;
            font-weight: 600;
            color: #cbd5e1;
            text-align: right;
        }

        /* Method Selector Tabs */
        .method-tabs-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        @media (max-width: 640px) {
            .method-tabs-grid {
                grid-template-columns: 1fr;
            }
        }
        .method-tab-btn {
            border: 1px solid #eaecf0;
            background: #ffffff;
            border-radius: 10px;
            padding: 12px 14px;
            text-align: left;
            cursor: pointer;
            transition: all 0.15s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
        }
        .method-tab-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }
        .method-tab-btn.active {
            border-color: #0284c7;
            background: #f0f9ff;
            box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.2);
        }
        .method-tab-icon {
            width: 32px;
            height: 32px;
            min-width: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .method-tab-title {
            font-size: 13px;
            font-weight: 700;
            color: #101828;
            line-height: 1.2;
        }
        .method-tab-desc {
            font-size: 11px;
            color: #667085;
            margin-top: 2px;
        }

        /* Bank Account Card inside Tab A */
        .bank-account-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 18px;
        }
        .bank-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            gap: 8px;
            flex-wrap: wrap;
        }
        .bank-brand-title {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }
        .bank-branch-tag {
            font-size: 11px;
            color: #64748b;
        }
        .bank-acc-row {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            gap: 12px;
            flex-wrap: wrap;
        }
        .acc-num-display {
            font-family: monospace;
            font-size: 17px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.08em;
        }
        .btn-copy-acc {
            background: #0284c7;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .btn-copy-acc:hover {
            background: #0369a1;
        }
        .bank-beneficiary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            font-size: 12px;
        }
        @media (max-width: 480px) {
            .bank-beneficiary-grid {
                grid-template-columns: 1fr;
            }
        }
        .beneficiary-lbl {
            color: #64748b;
        }
        .beneficiary-val {
            font-weight: 600;
            color: #1e293b;
        }

        /* Card Checkout Box */
        .card-checkout-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 18px 20px;
            margin-bottom: 18px;
        }

        /* Form Inputs */
        .clean-input {
            width: 100%;
            padding: 9px 12px;
            font-size: 13px;
            border-radius: 8px;
            border: 1px solid #d0d5dd;
            background: #ffffff;
            color: #101828;
            outline: none;
            box-sizing: border-box;
            transition: border-color 0.15s;
        }
        .clean-input:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
        }

        /* Primary Submit Buttons */
        .btn-primary-submit {
            width: 100%;
            padding: 11px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            color: #ffffff;
            background: #0284c7;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: background 0.15s;
            box-shadow: 0 1px 2px rgba(2, 132, 199, 0.1);
        }
        .btn-primary-submit:hover {
            background: #0369a1;
        }
        .btn-primary-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* 6. Renewal History Section */
        .history-card {
            background: #ffffff;
            border: 1px solid #eaecf0;
            border-radius: 14px;
            padding: 20px 24px;
            box-shadow: 0 1px 3px rgba(16, 24, 40, 0.05);
        }
        .history-title {
            font-size: 15px;
            font-weight: 600;
            color: #101828;
            margin: 0;
        }
        .history-sub {
            font-size: 12px;
            color: #667085;
            margin-top: 2px;
            margin-bottom: 16px;
        }
        .history-table-wrapper {
            overflow-x: auto;
        }
        .history-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            text-align: left;
        }
        .history-table th {
            padding: 10px 12px;
            font-weight: 600;
            color: #475467;
            border-bottom: 1px solid #eaecf0;
            background: #f9fafb;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .history-table td {
            padding: 12px;
            border-bottom: 1px solid #f2f4f7;
            color: #344054;
        }

        /* Dark Mode Adaptations */
        html.dark .sub-root {
            color: #f8fafc;
        }
        html.dark .sub-title {
            color: #f8fafc;
        }
        html.dark .sub-subtitle {
            color: #94a3b8;
        }
        html.dark .btn-change-plan {
            background: #1e293b;
            border-color: #38bdf8;
            color: #38bdf8;
        }
        html.dark .btn-change-plan:hover {
            background: #0f172a;
            color: #7dd3fc;
        }
        html.dark .sub-banner-active {
            background: rgba(14, 165, 233, 0.1);
            border-color: rgba(14, 165, 233, 0.25);
        }
        html.dark .sub-banner-expired {
            background: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.25);
        }
        html.dark .banner-title {
            color: #38bdf8;
        }
        html.dark .banner-sub {
            color: #7dd3fc;
        }
        html.dark .banner-action-link {
            background: #1e293b;
            border-color: #38bdf8;
            color: #38bdf8;
        }
        html.dark .banner-action-link:hover {
            background: #0f172a;
        }
        html.dark .sub-hero-card,
        html.dark .metric-card,
        html.dark .payment-drawer-card,
        html.dark .history-card {
            background: #111827;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }
        html.dark .plan-name,
        html.dark .plan-price-num,
        html.dark .metric-title,
        html.dark .drawer-title,
        html.dark .history-title,
        html.dark .method-tab-title {
            color: #f8fafc;
        }
        html.dark .plan-subtitle,
        html.dark .plan-price-period,
        html.dark .metric-subtitle,
        html.dark .metric-label,
        html.dark .metric-footer-text,
        html.dark .drawer-sub,
        html.dark .history-sub,
        html.dark .method-tab-desc,
        html.dark .beneficiary-lbl {
            color: #94a3b8;
        }
        html.dark .hero-divider,
        html.dark .metric-footer,
        html.dark .drawer-header,
        html.dark .history-table td {
            border-color: rgba(255, 255, 255, 0.06);
        }
        html.dark .metric-icon-box {
            background: #1f2937;
            border-color: #374151;
            color: #94a3b8;
        }
        html.dark .progress-bar-bg {
            background: #1f2937;
        }
        html.dark .btn-pill-action {
            background: rgba(14, 165, 233, 0.12);
            border-color: rgba(14, 165, 233, 0.3);
            color: #38bdf8;
        }
        html.dark .annual-upgrade-link {
            color: #cbd5e1;
        }
        html.dark .toggle-details-btn {
            color: #38bdf8;
        }
        html.dark .toggle-details-btn:hover {
            background: rgba(14, 165, 233, 0.1);
        }
        html.dark .method-tab-btn {
            background: #1f2937;
            border-color: #374151;
        }
        html.dark .method-tab-btn:hover {
            background: #242f3d;
        }
        html.dark .method-tab-btn.active {
            background: rgba(14, 165, 233, 0.12);
            border-color: #38bdf8;
        }
        html.dark .bank-account-box,
        html.dark .card-checkout-box {
            background: #1e293b;
            border-color: #334155;
        }
        html.dark .bank-acc-row {
            background: #0f172a;
            border-color: #334155;
        }
        html.dark .acc-num-display,
        html.dark .bank-brand-title,
        html.dark .beneficiary-val {
            color: #f8fafc;
        }
        html.dark .form-select-clean,
        html.dark .clean-input {
            background: #1f2937;
            border-color: #374151;
            color: #f8fafc;
        }
        html.dark .history-table th {
            background: #1f2937;
            color: #cbd5e1;
            border-color: #374151;
        }
    </style>

    <div class="sub-root" x-data="{ showDrawer: {{ $isSubActive ? 'false' : 'true' }}, copiedAcc: false, copiedEmail: false }">

        {{-- 1. Clean Header with Action Button --}}
        <div class="sub-header">
            <div>
                <h1 class="sub-title">Subscription</h1>
                <div class="sub-subtitle">
                    Manage your active plan tier, team licenses, and official settlement records with Pixel Craft Studios.
                </div>
            </div>

            <button
                type="button"
                class="btn-change-plan"
                @click="showDrawer = !showDrawer; if(showDrawer) $nextTick(() => document.getElementById('payment-drawer-section')?.scrollIntoView({ behavior: 'smooth' }))"
            >
                <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
                <span>Change plan</span>
            </button>
        </div>

        {{-- 2. Soft Blue Notice Banner --}}
        @if($isSubActive)
            <div class="sub-banner-active">
                <div class="banner-info-wrap">
                    <div class="banner-icon-circle">
                        <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="banner-title">Subscription Active & In Good Standing</div>
                        <div class="banner-sub">
                            Your {{ $currentPlan->name ?? 'Gold Enterprise' }} workspace is active with full features and uninterrupted access.
                        </div>
                    </div>
                </div>

                <button
                    type="button"
                    class="banner-action-link"
                    @click="showDrawer = !showDrawer; if(showDrawer) $nextTick(() => document.getElementById('payment-drawer-section')?.scrollIntoView({ behavior: 'smooth' }))"
                >
                    Manage Plan
                </button>
            </div>
        @else
            <div class="sub-banner-expired">
                <div class="banner-info-wrap">
                    <div class="banner-icon-circle danger">
                        <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="banner-title danger">Subscription Suspended or Expired</div>
                        <div class="banner-sub danger">
                            Your workspace is currently in read-only mode. Please renew your plan below to restore all operational features.
                        </div>
                    </div>
                </div>

                <button
                    type="button"
                    class="banner-action-link"
                    @click="showDrawer = true; $nextTick(() => document.getElementById('payment-drawer-section')?.scrollIntoView({ behavior: 'smooth' }))"
                >
                    Renew Now
                </button>
            </div>
        @endif

        {{-- 3. Primary Hero Card (Current Active Plan) --}}
        <div class="sub-hero-card">
            <div class="sub-hero-top">
                <div>
                    <div class="plan-title-row">
                        <h2 class="plan-name">{{ $currentPlan->name ?? 'Gold Enterprise' }}</h2>
                        @if($isSubActive)
                            <span class="badge-active">
                                <svg class="icon-xs" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Active</span>
                            </span>
                        @else
                            <span class="badge-expired">Suspended</span>
                        @endif
                    </div>
                    <div class="plan-subtitle">
                        @if($expiryDate)
                            {{ $isSubActive ? 'Renews on ' . $expiryDate->format('l, M j, Y') : 'Expired on ' . $expiryDate->format('l, M j, Y') }} ({{ $timeInfo['text'] }})
                        @else
                            Active workspace — Permanent license
                        @endif
                    </div>
                </div>

                <div class="plan-price-wrap">
                    <span class="plan-price-num">Rs. {{ number_format($currentPlanPrice, 2) }}</span>
                    <span class="plan-price-period">/mth</span>
                </div>
            </div>

            <div class="hero-divider">
                <button
                    type="button"
                    class="annual-upgrade-link"
                    wire:click="switchToAnnualBilling"
                    @click="showDrawer = true; $nextTick(() => document.getElementById('payment-drawer-section')?.scrollIntoView({ behavior: 'smooth' }))"
                >
                    <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Upgrade to annual billing</span>
                    <span class="badge-save">Save 16%</span>
                </button>

                <button
                    type="button"
                    class="toggle-details-btn"
                    @click="showDrawer = !showDrawer; if(showDrawer) $nextTick(() => document.getElementById('payment-drawer-section')?.scrollIntoView({ behavior: 'smooth' }))"
                >
                    <span>Payment details</span>
                    <span x-show="!showDrawer">⌄</span>
                    <span x-show="showDrawer">⌃</span>
                </button>
            </div>
        </div>

        {{-- 4. Two-Column Usage & Metric Cards Grid --}}
        <div class="metrics-grid">
            {{-- Card 1: Team Licenses / Users --}}
            <div class="metric-card">
                <div>
                    <div class="metric-header">
                        <div class="metric-icon-box">
                            <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="metric-title">Team Licenses</h3>
                            <div class="metric-subtitle">Manage your team member licenses</div>
                        </div>
                    </div>

                    <div class="metric-row">
                        <span class="metric-label">Used licenses</span>
                        <span class="metric-count">{{ $usedUsers }} of {{ $maxUsers }}</span>
                    </div>

                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width: {{ $userPct }}%;"></div>
                    </div>
                </div>

                <div class="metric-footer">
                    <span class="metric-footer-text">Add Licenses (Included in plan)</span>
                    @if($tenant)
                        <a href="{{ route('filament.app.resources.team-members.create', ['tenant' => $tenant]) }}" class="btn-pill-action">
                            <span>+ Add</span>
                        </a>
                    @else
                        <span class="btn-pill-action">+ Add</span>
                    @endif
                </div>
            </div>

            {{-- Card 2: Monthly Invoicing & Proposals --}}
            <div class="metric-card">
                <div>
                    <div class="metric-header">
                        <div class="metric-icon-box">
                            <svg class="icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="metric-title">Monthly Invoicing & Proposals</h3>
                            <div class="metric-subtitle">Track your monthly document generation</div>
                        </div>
                    </div>

                    <div class="metric-row">
                        <span class="metric-label">Quotations & Invoices</span>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <span class="metric-count">{{ $quotationsCount }} Issued</span>
                            <span class="badge-active" style="padding: 1px 7px; font-size: 11px;">
                                <svg class="icon-xs" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Unlimited</span>
                            </span>
                        </div>
                    </div>

                    <div class="progress-bar-bg">
                        <div class="progress-bar-fill" style="width: 100%; background: linear-gradient(90deg, #10b981, #0284c7);"></div>
                    </div>
                </div>

                <div class="metric-footer">
                    <span class="metric-footer-text">Need custom enterprise limits?</span>
                    @if($tenant)
                        <a href="{{ route('filament.app.resources.quotations.index', ['tenant' => $tenant]) }}" class="btn-pill-action">
                            <span>+ View</span>
                        </a>
                    @else
                        <span class="btn-pill-action">+ View</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- 5. Pixel Craft Studios Payment Details & Checkout Drawer --}}
        <div id="payment-drawer-section" x-show="showDrawer" x-transition.duration.300ms class="payment-drawer-card">
            <div class="drawer-header">
                <div>
                    <h3 class="drawer-title">Official Provider & Settlement — Pixel Craft Studios</h3>
                    <div class="drawer-sub">
                        Direct official billing desk: <a href="mailto:pixelcraftstudioslk@gmail.com" style="color: #0284c7; font-weight: 600; text-decoration: none;">pixelcraftstudioslk@gmail.com</a>
                    </div>
                </div>

                <button type="button" @click="showDrawer = false" class="toggle-details-btn" style="color: #64748b;">
                    <span>Close ✕</span>
                </button>
            </div>

            {{-- Plan Tier & Billing Cycle Selector --}}
            <div class="plan-selector-grid">
                <div>
                    <label class="form-label-xs">Select Plan Tier</label>
                    <select wire:model.live="plan_id" class="form-select-clean">
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}">
                                {{ $plan->name }} — Rs. {{ number_format($plan->price_monthly, 0) }}/mo
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label-xs">Billing Duration</label>
                    <select wire:model.live="billing_cycle" class="form-select-clean">
                        <option value="monthly">Monthly Cycle (1 Month)</option>
                        <option value="yearly">Yearly Cycle (12 Months — Save 16%)</option>
                    </select>
                </div>
            </div>

            {{-- Live Total Payable Banner --}}
            <div class="live-total-box">
                <div>
                    <span class="total-label">Total Payable ({{ ucfirst($billing_cycle) }} Cycle):</span>
                    <div class="total-amount-lkr">
                        Rs. {{ number_format($amountLkr, 2) }} <span style="font-size: 13px; font-weight: 600; color: #94a3b8;">LKR</span>
                    </div>
                </div>

                <div>
                    <div class="total-amount-usd">≈ ${{ number_format($amountUsd, 2) }} USD</div>
                    <div style="font-size: 11px; color: #94a3b8; text-align: right; margin-top: 2px;">Automated Tax Included</div>
                </div>
            </div>

            {{-- Payment Method Selector Tabs --}}
            <div style="margin-bottom: 8px;">
                <label class="form-label-xs">Select Payment Method</label>
            </div>

            <div class="method-tabs-grid">
                {{-- Method 1: Direct Bank Transfer --}}
                <button
                    type="button"
                    wire:click="setPaymentMethod('bank_transfer')"
                    class="method-tab-btn {{ $payment_method === 'bank_transfer' ? 'active' : '' }}"
                >
                    <div class="method-tab-icon" style="background: #ecfdf5; color: #059669;">
                        <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <div class="method-tab-title">HNB Transfer</div>
                        <div class="method-tab-desc">Direct Bank Remittance</div>
                    </div>
                </button>

                {{-- Method 2: Credit / Debit Card --}}
                <button
                    type="button"
                    wire:click="setPaymentMethod('card')"
                    class="method-tab-btn {{ $payment_method === 'card' ? 'active' : '' }}"
                >
                    <div class="method-tab-icon" style="background: #f0f9ff; color: #0284c7;">
                        <svg class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="method-tab-title">Online Card</div>
                        <div class="method-tab-desc">Instant Activation</div>
                    </div>
                </button>

                {{-- Method 3: PayPal Express --}}
                <button
                    type="button"
                    wire:click="setPaymentMethod('paypal')"
                    class="method-tab-btn {{ $payment_method === 'paypal' ? 'active' : '' }}"
                >
                    <div class="method-tab-icon" style="background: #eef2ff; color: #4f46e5;">
                        <svg class="icon-sm" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.282-.533 3.411-2.67 5.753-5.908 5.753H9.986L8.47 19.349a.641.641 0 0 1-.63.535l-.764 1.453z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="method-tab-title">PayPal Express</div>
                        <div class="method-tab-desc">International Checkout</div>
                    </div>
                </button>
            </div>

            {{-- TAB A: DIRECT BANK TRANSFER (HNB GALLE) --}}
            @if($payment_method === 'bank_transfer')
                <div class="bank-account-box">
                    <div class="bank-header-row">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="background: #059669; color: #ffffff; font-weight: 800; font-size: 11px; padding: 2px 6px; border-radius: 4px;">HNB</span>
                            <span class="bank-brand-title">Hatton National Bank PLC (HNB)</span>
                        </div>
                        <span class="bank-branch-tag">Galle Branch, Sri Lanka</span>
                    </div>

                    <div class="bank-acc-row">
                        <div>
                            <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #64748b;">Account Number</div>
                            <div class="acc-num-display">227020114025</div>
                        </div>

                        <button
                            type="button"
                            class="btn-copy-acc"
                            @click="navigator.clipboard.writeText('227020114025'); copiedAcc = true; setTimeout(() => copiedAcc = false, 2500)"
                        >
                            <svg class="icon-xs" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                            </svg>
                            <span x-show="!copiedAcc">Copy Account</span>
                            <span x-show="copiedAcc">✓ Copied!</span>
                        </button>
                    </div>

                    <div class="bank-beneficiary-grid">
                        <div>
                            <span class="beneficiary-lbl">Account Beneficiary:</span>
                            <span class="beneficiary-val"> P M N S T Dharmarathna</span>
                        </div>
                        <div>
                            <span class="beneficiary-lbl">Parent Provider:</span>
                            <span class="beneficiary-val"> Pixel Craft Studios</span>
                        </div>
                    </div>
                </div>

                <form wire:submit.prevent="submitRenewalRequest" style="display: flex; flex-direction: column; gap: 14px;">
                    <div>
                        <label class="form-label-xs">
                            Bank Transfer Reference / Deposit Slip No <span style="color: #dc2626;">*</span>
                        </label>
                        <input
                            wire:model="payment_reference"
                            type="text"
                            placeholder="e.g. HNB-TXN-984210 or SLIP-884920"
                            required
                            class="clean-input"
                        />
                        @error('payment_reference')
                            <span style="color: #dc2626; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label-xs">Attach Deposit Slip / Receipt (Optional)</label>
                        <input
                            type="file"
                            wire:model="receipt"
                            accept="image/jpeg,image/png,image/webp,application/pdf"
                            class="clean-input"
                            style="padding: 6px 10px;"
                        />
                        <div wire:loading wire:target="receipt" style="font-size: 12px; color: #0284c7; margin-top: 4px;">
                            Uploading deposit slip...
                        </div>
                        @error('receipt')
                            <span style="color: #dc2626; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label-xs">Additional Notes (Optional)</label>
                        <textarea
                            wire:model="notes"
                            rows="2"
                            placeholder="e.g. Sent via HNB Corporate Online Banking"
                            class="clean-input"
                        ></textarea>
                    </div>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="btn-primary-submit"
                    >
                        <svg wire:loading.remove wire:target="submitRenewalRequest" class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <svg wire:loading wire:target="submitRenewalRequest" class="icon-sm" style="animation: spin 1s linear infinite;" fill="none" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity="0.25"></circle>
                            <path fill="currentColor" opacity="0.75" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Submit Payment Verification</span>
                    </button>
                </form>
            @endif

            {{-- TAB B: ONLINE CARD CHECKOUT --}}
            @if($payment_method === 'card')
                <div class="card-checkout-box">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; flex-wrap: wrap; gap: 8px;">
                        <span style="font-size: 13px; font-weight: 700; color: #101828;">Supported Cards</span>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            {{-- Visa Badge --}}
                            <svg style="height: 20px; width: auto; max-height: 24px; object-fit: contain;" viewBox="0 0 40 24" fill="none">
                                <rect width="40" height="24" rx="4" fill="#0D2B6B"/>
                                <text x="7" y="16.5" font-family="'Helvetica Neue', Arial, sans-serif" font-size="12" font-style="italic" font-weight="900" fill="#FFFFFF" letter-spacing="1">VISA</text>
                            </svg>
                            {{-- MasterCard Badge --}}
                            <svg style="height: 20px; width: auto; max-height: 24px; object-fit: contain;" viewBox="0 0 38 24" fill="none">
                                <rect width="38" height="24" rx="4" fill="#1E293B"/>
                                <circle cx="15" cy="12" r="7" fill="#EB001B"/>
                                <circle cx="23" cy="12" r="7" fill="#F79E1B" fill-opacity="0.85"/>
                            </svg>
                            {{-- Amex Badge --}}
                            <svg style="height: 20px; width: auto; max-height: 24px; object-fit: contain;" viewBox="0 0 40 24" fill="none">
                                <rect width="40" height="24" rx="4" fill="#006FCF"/>
                                <text x="6" y="15.5" font-family="'Helvetica Neue', Arial, sans-serif" font-size="9" font-weight="900" fill="#FFFFFF" letter-spacing="0.5">AMEX</text>
                            </svg>
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <div>
                            <label class="form-label-xs">Cardholder Name</label>
                            <input wire:model="card_name" type="text" placeholder="e.g. Sunil Perera" class="clean-input" />
                        </div>
                        <div>
                            <label class="form-label-xs">Card Number</label>
                            <input wire:model="card_number" type="text" placeholder="•••• •••• •••• ••••" class="clean-input" style="font-family: monospace;" />
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div>
                                <label class="form-label-xs">Expiry Date</label>
                                <input wire:model="card_expiry" type="text" placeholder="MM/YY" class="clean-input" style="font-family: monospace;" />
                            </div>
                            <div>
                                <label class="form-label-xs">CVC / CVV</label>
                                <input wire:model="card_cvc" type="password" maxlength="4" placeholder="•••" class="clean-input" style="font-family: monospace;" />
                            </div>
                        </div>
                    </div>
                </div>

                <button
                    type="button"
                    wire:click="processCardPayment"
                    wire:loading.attr="disabled"
                    class="btn-primary-submit"
                >
                    <svg wire:loading.remove wire:target="processCardPayment" class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <svg wire:loading wire:target="processCardPayment" class="icon-sm" style="animation: spin 1s linear infinite;" fill="none" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity="0.25"></circle>
                        <path fill="currentColor" opacity="0.75" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Pay Online via Card — Instant Activation</span>
                </button>
            @endif

            {{-- TAB C: PAYPAL CHECKOUT --}}
            @if($payment_method === 'paypal')
                <div class="bank-account-box">
                    <div class="bank-header-row">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="background: #003087; color: #ffffff; font-weight: 800; font-size: 11px; padding: 2px 6px; border-radius: 4px;">PayPal</span>
                            <span class="bank-brand-title">PayPal Direct Merchant Account</span>
                        </div>
                        <span class="bank-branch-tag">USD Equivalent: ${{ number_format($amountUsd, 2) }}</span>
                    </div>

                    <div class="bank-acc-row">
                        <div>
                            <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #64748b;">Recipient Email</div>
                            <div class="acc-num-display" style="font-size: 14px; letter-spacing: normal;">pixelcraftstudioslk@gmail.com</div>
                        </div>

                        <div style="display: flex; gap: 6px;">
                            <button
                                type="button"
                                class="btn-copy-acc"
                                style="background: #334155;"
                                @click="navigator.clipboard.writeText('pixelcraftstudioslk@gmail.com'); copiedEmail = true; setTimeout(() => copiedEmail = false, 2500)"
                            >
                                <span x-show="!copiedEmail">Copy Email</span>
                                <span x-show="copiedEmail">✓ Copied!</span>
                            </button>

                            <a
                                href="https://www.paypal.com/signin"
                                target="_blank"
                                rel="noopener"
                                class="btn-copy-acc"
                                style="text-decoration: none; background: #4f46e5;"
                            >
                                <span>PayPal ↗</span>
                            </a>
                        </div>
                    </div>
                </div>

                <form wire:submit.prevent="submitRenewalRequest" style="display: flex; flex-direction: column; gap: 14px;">
                    <div>
                        <label class="form-label-xs">
                            PayPal Transaction ID / Invoice Ref <span style="color: #dc2626;">*</span>
                        </label>
                        <input
                            wire:model="payment_reference"
                            type="text"
                            placeholder="e.g. 8AB12345CD67890E or PayPal Receipt Number"
                            required
                            class="clean-input"
                        />
                        @error('payment_reference')
                            <span style="color: #dc2626; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label-xs">Attach PayPal Screenshot / PDF (Optional)</label>
                        <input
                            type="file"
                            wire:model="receipt"
                            accept="image/jpeg,image/png,image/webp,application/pdf"
                            class="clean-input"
                            style="padding: 6px 10px;"
                        />
                    </div>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="btn-primary-submit"
                        style="background: #4f46e5;"
                    >
                        <svg wire:loading.remove wire:target="submitRenewalRequest" class="icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Submit PayPal Proof for Verification</span>
                    </button>
                </form>
            @endif
        </div>

        {{-- 6. Subscription Renewal History Table --}}
        <div class="history-card">
            <h3 class="history-title">Subscription Renewal History</h3>
            <div class="history-sub">
                Historical record of all renewal submissions and license extensions for {{ $tenant?->name ?? 'your workspace' }}.
            </div>

            <div class="history-table-wrapper">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th># Reference</th>
                            <th>Plan Tier</th>
                            <th>Duration</th>
                            <th>Amount (LKR)</th>
                            <th>Method</th>
                            <th>Transaction Ref</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Verification</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                            <tr>
                                <td style="font-family: monospace; font-weight: 700;">
                                    REQ-{{ str_pad($req->id, 4, '0', STR_PAD_LEFT) }}
                                </td>
                                <td style="font-weight: 600;">
                                    {{ $req->plan?->name ?? 'Enterprise' }}
                                </td>
                                <td style="text-transform: capitalize;">
                                    {{ $req->billing_cycle }}
                                </td>
                                <td style="font-weight: 700; color: #059669;">
                                    Rs. {{ number_format($req->amount, 2) }}
                                </td>
                                <td>
                                    @if($req->payment_method === 'card')
                                        <span class="badge-save" style="background: #f0f9ff; color: #0284c7; border-color: #bae6fd;">Card</span>
                                    @elseif($req->payment_method === 'paypal')
                                        <span class="badge-save" style="background: #eef2ff; color: #4f46e5; border-color: #c7d2fe;">PayPal</span>
                                    @else
                                        <span class="badge-active" style="padding: 1px 7px; font-size: 11px;">HNB Transfer</span>
                                    @endif
                                </td>
                                <td style="font-family: monospace;">
                                    <div>{{ $req->payment_reference }}</div>
                                    @if($req->receipt_path)
                                        <a href="{{ asset('storage/'.$req->receipt_path) }}" target="_blank" style="font-size: 11px; color: #0284c7; text-decoration: underline; display: inline-flex; align-items: center; gap: 3px; margin-top: 2px;">
                                            <span>View Receipt</span>
                                        </a>
                                    @endif
                                </td>
                                <td style="color: #64748b;">
                                    {{ $req->created_at ? $req->created_at->format('d M Y') : '—' }}
                                </td>
                                <td>
                                    @if($req->status === 'approved')
                                        <span class="badge-active">Approved</span>
                                    @elseif($req->status === 'rejected')
                                        <span class="badge-expired">Rejected</span>
                                    @else
                                        <span class="badge-save">Under Review</span>
                                    @endif
                                </td>
                                <td style="font-size: 11px; color: #64748b;">
                                    @if($req->status === 'approved')
                                        <span style="font-weight: 600; color: #059669;">{{ $req->approver?->name ?? 'Pixel Craft Studios' }}</span>
                                    @elseif($req->status === 'rejected')
                                        <span style="color: #dc2626;">Declined</span>
                                    @else
                                        <span style="font-style: italic;">Awaiting Review</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 24px; color: #94a3b8; font-style: italic;">
                                    No subscription renewal requests submitted yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-filament-panels::page>

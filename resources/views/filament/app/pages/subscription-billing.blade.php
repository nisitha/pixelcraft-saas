<x-filament-panels::page>
    <style>
        /* Strict sizing safeguards for Subscription & Billing */
        .subscription-billing-container svg,
        .fi-page svg {
            max-width: 26px !important;
            max-height: 26px !important;
            flex-shrink: 0 !important;
            display: inline-block !important;
            vertical-align: middle !important;
        }
        .subscription-billing-container svg.icon-xs {
            width: 14px !important; height: 14px !important; max-width: 14px !important; max-height: 14px !important;
        }
        .subscription-billing-container svg.icon-sm {
            width: 16px !important; height: 16px !important; max-width: 16px !important; max-height: 16px !important;
        }
        .subscription-billing-container svg.icon-md {
            width: 20px !important; height: 20px !important; max-width: 22px !important; max-height: 22px !important;
        }
        .subscription-billing-container svg.icon-lg {
            width: 22px !important; height: 22px !important; max-width: 26px !important; max-height: 26px !important;
        }
        .payment-badge-logo {
            height: 24px !important;
            width: auto !important;
            max-height: 28px !important;
            object-fit: contain !important;
            display: inline-block !important;
            vertical-align: middle !important;
        }
    </style>

    <div class="subscription-billing-container">
    @php
        $tenant = $this->getTenant();
        $isSubActive = $tenant ? $tenant->isSubscriptionActive() : false;
        $expiryDate = $tenant?->subscription_ends_at;
        $timeInfo = $this->getHumanReadableTimeRemaining();

        $requests = $this->getSubscriptionRequests();
        $plans = $this->getAvailablePlans();
        $amountLkr = $this->getEstimatedAmount();
        $amountUsd = $this->getEstimatedAmountUsd();
    @endphp

    {{-- Top Status Alert Banner --}}
    <div class="mb-6">
        @if(!$isSubActive)
            <div class="rounded-xl border border-red-200 bg-red-50 dark:border-red-900/50 dark:bg-red-950/40 p-4 sm:p-5 flex items-start gap-4 shadow-sm">
                <div class="p-2 rounded-lg bg-red-100 dark:bg-red-900/60 text-red-600 dark:text-red-400 shrink-0">
                    <svg style="width: 22px; height: 22px; min-width: 22px; min-height: 22px; max-width: 26px; max-height: 26px; display: inline-block; vertical-align: middle;" class="w-6 h-6 icon-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-bold text-red-900 dark:text-red-200">
                        Subscription Suspended or Expired
                    </h3>
                    <p class="text-sm text-red-700 dark:text-red-300 mt-1">
                        Your workspace is currently in read-only mode. Please choose a plan and payment method below to renew your license with Pixel Craft Studios and immediately restore full operational features.
                    </p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-red-200 text-red-800 dark:bg-red-900 dark:text-red-200 shrink-0">
                    Expired
                </span>
            </div>
        @else
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 dark:border-emerald-900/50 dark:bg-emerald-950/30 p-4 sm:p-5 flex items-center justify-between gap-4 flex-wrap shadow-sm">
                <div class="flex items-center gap-3.5">
                    <div class="p-2 rounded-lg bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400 shrink-0">
                        <svg style="width: 22px; height: 22px; min-width: 22px; min-height: 22px; max-width: 26px; max-height: 26px; display: inline-block; vertical-align: middle;" class="w-6 h-6 icon-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-bold text-emerald-900 dark:text-emerald-100">
                                Subscription Active & In Good Standing
                            </h3>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-emerald-200 text-emerald-800 dark:bg-emerald-900/80 dark:text-emerald-200">
                                Verified
                            </span>
                        </div>
                        <p class="text-xs text-emerald-700 dark:text-emerald-300 mt-0.5">
                            Parent Provider: <span class="font-semibold">Pixel Craft Studios</span> • Full access to Quotation Builder, Milestone Invoices, POS & Inventory.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 text-right">
                    <span class="px-3 py-1.5 rounded-lg text-xs font-bold bg-white/80 dark:bg-slate-900/80 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 shadow-sm">
                        {{ $timeInfo['text'] }}
                    </span>
                </div>
            </div>
        @endif
    </div>

    {{-- 2-Column Responsive Workspace: Overview Left, Interactive Checkout Right --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        {{-- LEFT COLUMN: Current Plan Status & Provider Trust (5 Cols) --}}
        <div class="lg:col-span-5 space-y-6">
            
            {{-- Status Card --}}
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2 text-slate-900 dark:text-slate-100">
                        <x-filament::icon icon="heroicon-o-shield-check" class="w-5 h-5 text-emerald-500" />
                        <span class="font-bold">Active Plan Overview</span>
                    </div>
                </x-slot>
                <x-slot name="description">
                    Current subscription validity and tenant tier details.
                </x-slot>

                <div class="space-y-3 mt-1">
                    <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-slate-900/70 border border-slate-200 dark:border-slate-800">
                        <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Current Plan Tier</span>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-slate-900 dark:text-white">
                                {{ $tenant?->plan?->name ?? 'Free Plan' }}
                            </span>
                            @if($tenant?->plan)
                                <x-filament::badge color="info">Enterprise</x-filament::badge>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-slate-900/70 border border-slate-200 dark:border-slate-800">
                        <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Account Health</span>
                        <div>
                            @if($isSubActive)
                                <x-filament::badge color="success">Active Workspace</x-filament::badge>
                            @else
                                <x-filament::badge color="danger">Suspended</x-filament::badge>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-slate-900/70 border border-slate-200 dark:border-slate-800">
                        <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Valid Until</span>
                        <span class="text-sm font-semibold text-slate-900 dark:text-slate-200">
                            {{ $expiryDate ? $expiryDate->format('d M Y') : 'No Expiry Set' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-slate-900/70 border border-slate-200 dark:border-slate-800">
                        <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Time Remaining</span>
                        <div>
                            <x-filament::badge :color="$timeInfo['badge']" size="md">
                                {{ $timeInfo['text'] }}
                            </x-filament::badge>
                        </div>
                    </div>
                </div>

                {{-- Included SaaS Capabilities --}}
                <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-800">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2.5">
                        Included Tier Features:
                    </h4>
                    <ul class="space-y-1.5 text-xs text-slate-600 dark:text-slate-300">
                        <li class="flex items-center gap-2">
                            <svg style="width: 16px; height: 16px; min-width: 16px; min-height: 16px; max-width: 18px; max-height: 18px; display: inline-block; vertical-align: middle;" class="w-4 h-4 text-emerald-500 shrink-0 icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Unlimited Invoices, Quotations & Receipts</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg style="width: 16px; height: 16px; min-width: 16px; min-height: 16px; max-width: 18px; max-height: 18px; display: inline-block; vertical-align: middle;" class="w-4 h-4 text-emerald-500 shrink-0 icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Solar System Sizing & Certificate Builder</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg style="width: 16px; height: 16px; min-width: 16px; min-height: 16px; max-width: 18px; max-height: 18px; display: inline-block; vertical-align: middle;" class="w-4 h-4 text-emerald-500 shrink-0 icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Multi-user Access, Role Control & Audit Logs</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg style="width: 16px; height: 16px; min-width: 16px; min-height: 16px; max-width: 18px; max-height: 18px; display: inline-block; vertical-align: middle;" class="w-4 h-4 text-emerald-500 shrink-0 icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>POS Terminal & Sales Commission Management</span>
                        </li>
                    </ul>
                </div>
            </x-filament::section>

            {{-- Verified Provider Card (Pixel Craft Studios) --}}
            <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-sky-500 to-indigo-600 flex items-center justify-center text-white font-black text-sm shadow-md">
                        PCS
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white">
                                Pixel Craft Studios
                            </h4>
                            <svg style="width: 16px; height: 16px; min-width: 16px; min-height: 16px; max-width: 18px; max-height: 18px; display: inline-block; vertical-align: middle;" class="w-4 h-4 text-sky-500 icon-sm" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Official Cloud Platform & Infrastructure Provider
                        </p>
                    </div>
                </div>

                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                    All licenses, recurring renewals, and platform upgrades are verified and issued directly by Pixel Craft Studios.
                </p>

                <div class="flex items-center justify-between text-[11px] pt-2 border-t border-slate-100 dark:border-slate-800 text-slate-500 dark:text-slate-400">
                    <span>Direct Billing Desk:</span>
                    <a href="mailto:pixelcraftstudioslk@gmail.com" class="font-medium text-sky-600 dark:text-sky-400 hover:underline">
                        pixelcraftstudioslk@gmail.com
                    </a>
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN: Multi-Method Checkout Selector (7 Cols) --}}
        <div class="lg:col-span-7">
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2 text-slate-900 dark:text-slate-100">
                        <x-filament::icon icon="heroicon-o-credit-card" class="w-5 h-5 text-teal-500" />
                        <span class="font-bold text-base">Select Plan & Payment Method</span>
                    </div>
                </x-slot>
                <x-slot name="description">
                    Renew your subscription seamlessly via Card, Direct HNB Bank Transfer, or PayPal.
                </x-slot>

                <div class="space-y-5">
                    
                    {{-- 1. Plan Tier & Billing Cycle Selector --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                                Subscription Tier
                            </label>
                            <select wire:model.live="plan_id" class="w-full text-sm font-semibold rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}">
                                        {{ $plan->name }} — Rs. {{ number_format($plan->price_monthly, 0) }}/mo
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-1.5">
                                Billing Duration
                            </label>
                            <select wire:model.live="billing_cycle" class="w-full text-sm font-semibold rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                <option value="monthly">Monthly Cycle (1 Month)</option>
                                <option value="yearly">Yearly Cycle (12 Months — Save 17%)</option>
                            </select>
                        </div>
                    </div>

                    {{-- 2. Header: Live Total Payable Banner --}}
                    <div class="rounded-xl p-4 bg-gradient-to-r from-teal-900/90 via-slate-900 to-indigo-950 text-white shadow-md border border-teal-500/30 flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <span class="text-xs font-semibold text-teal-300 uppercase tracking-widest block">
                                Total Payable ({{ ucfirst($billing_cycle) }} Cycle):
                            </span>
                            <div class="text-2xl font-black tracking-tight text-white mt-0.5">
                                Rs. {{ number_format($amountLkr, 2) }} <span class="text-xs font-semibold text-teal-300">LKR</span>
                            </div>
                        </div>

                        <div class="text-right">
                            <span class="text-xs text-slate-300 block">USD Equivalent:</span>
                            <span class="text-base font-bold text-sky-300 font-mono">
                                ≈ ${{ number_format($amountUsd, 2) }} USD
                            </span>
                        </div>
                    </div>

                    {{-- 3. Payment Method Switcher Tabs (Modern Pill/Radio selector) --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-2">
                            Select Payment Method:
                        </label>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            
                            {{-- Option 1: Credit / Debit Card --}}
                            <button
                                type="button"
                                wire:click="setPaymentMethod('card')"
                                class="p-3 rounded-xl border text-left transition flex items-center gap-3 {{ $payment_method === 'card' ? 'border-sky-500 bg-sky-50 dark:bg-sky-950/40 ring-2 ring-sky-500/30' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/60' }}"
                            >
                                <div class="w-8 h-8 rounded-lg bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0" style="width: 32px; height: 32px; min-width: 32px; min-height: 32px;">
                                    <svg style="width: 22px; height: 22px; min-width: 22px; min-height: 22px; max-width: 26px; max-height: 26px; display: inline-block; vertical-align: middle;" class="w-5 h-5 icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-900 dark:text-white">
                                        Credit / Debit
                                    </div>
                                    <div class="text-[10px] text-slate-500 dark:text-slate-400">
                                        Instant Activation
                                    </div>
                                </div>
                            </button>

                            {{-- Option 2: Direct Bank Transfer --}}
                            <button
                                type="button"
                                wire:click="setPaymentMethod('bank_transfer')"
                                class="p-3 rounded-xl border text-left transition flex items-center gap-3 {{ $payment_method === 'bank_transfer' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/40 ring-2 ring-emerald-500/30' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/60' }}"
                            >
                                <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0" style="width: 32px; height: 32px; min-width: 32px; min-height: 32px;">
                                    <svg style="width: 22px; height: 22px; min-width: 22px; min-height: 22px; max-width: 26px; max-height: 26px; display: inline-block; vertical-align: middle;" class="w-5 h-5 icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-900 dark:text-white">
                                        HNB Transfer
                                    </div>
                                    <div class="text-[10px] text-slate-500 dark:text-slate-400">
                                        Deposit / Slip
                                    </div>
                                </div>
                            </button>

                            {{-- Option 3: PayPal --}}
                            <button
                                type="button"
                                wire:click="setPaymentMethod('paypal')"
                                class="p-3 rounded-xl border text-left transition flex items-center gap-3 {{ $payment_method === 'paypal' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-950/40 ring-2 ring-indigo-500/30' : 'border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/60' }}"
                            >
                                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0" style="width: 32px; height: 32px; min-width: 32px; min-height: 32px;">
                                    <svg style="width: 22px; height: 22px; min-width: 22px; min-height: 22px; max-width: 26px; max-height: 26px; display: inline-block; vertical-align: middle;" class="w-5 h-5 icon-md" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.282-.533 3.411-2.67 5.753-5.908 5.753H9.986L8.47 19.349a.641.641 0 0 1-.63.535l-.764 1.453z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-900 dark:text-white">
                                        PayPal
                                    </div>
                                    <div class="text-[10px] text-slate-500 dark:text-slate-400">
                                        USD Checkout
                                    </div>
                                </div>
                            </button>

                        </div>
                    </div>

                    {{-- 4. Interactive Method Body --}}
                    
                    {{-- TAB A: CREDIT / DEBIT CARD CHECKOUT --}}
                    @if($payment_method === 'card')
                        <div class="rounded-xl border border-sky-200 dark:border-sky-900/50 bg-sky-50/50 dark:bg-sky-950/20 p-5 space-y-4">
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-200">
                                        Card Payment Gateway:
                                    </span>
                                    <div class="flex items-center gap-1.5">
                                        {{-- Visa Badge --}}
                                        <svg style="height: 24px; width: auto; max-height: 28px; object-fit: contain; display: inline-block; vertical-align: middle;" class="payment-badge-logo" viewBox="0 0 40 24" fill="none">
                                            <rect width="40" height="24" rx="4" fill="#0D2B6B"/>
                                            <text x="7" y="16.5" font-family="'Helvetica Neue', Arial, sans-serif" font-size="12" font-style="italic" font-weight="900" fill="#FFFFFF" letter-spacing="1">VISA</text>
                                        </svg>
                                        {{-- MasterCard Badge --}}
                                        <svg style="height: 24px; width: auto; max-height: 28px; object-fit: contain; display: inline-block; vertical-align: middle;" class="payment-badge-logo" viewBox="0 0 38 24" fill="none">
                                            <rect width="38" height="24" rx="4" fill="#1E293B"/>
                                            <circle cx="15" cy="12" r="7" fill="#EB001B"/>
                                            <circle cx="23" cy="12" r="7" fill="#F79E1B" fill-opacity="0.85"/>
                                        </svg>
                                        {{-- Amex Badge --}}
                                        <svg style="height: 24px; width: auto; max-height: 28px; object-fit: contain; display: inline-block; vertical-align: middle;" class="payment-badge-logo" viewBox="0 0 40 24" fill="none">
                                            <rect width="40" height="24" rx="4" fill="#006FCF"/>
                                            <text x="6" y="15.5" font-family="'Helvetica Neue', Arial, sans-serif" font-size="9" font-weight="900" fill="#FFFFFF" letter-spacing="0.5">AMEX</text>
                                        </svg>
                                    </div>
                                </div>
                                <span class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                    <svg style="width: 16px; height: 16px; min-width: 16px; min-height: 16px; max-width: 18px; max-height: 18px; display: inline-block; vertical-align: middle;" class="w-3.5 h-3.5 icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    Instant Automated Activation
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Cardholder Full Name</label>
                                    <input wire:model="card_name" type="text" placeholder="e.g. Sunil Perera" class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white" />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Card Number</label>
                                    <input wire:model="card_number" type="text" placeholder="•••• •••• •••• ••••" class="w-full text-xs font-mono rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white" />
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Expiry Date</label>
                                        <input wire:model="card_expiry" type="text" placeholder="MM/YY" class="w-full text-xs font-mono rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">CVC / CVV</label>
                                        <input wire:model="card_cvc" type="password" maxlength="4" placeholder="•••" class="w-full text-xs font-mono rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white" />
                                    </div>
                                </div>
                            </div>

                            <button
                                type="button"
                                wire:click="processCardPayment"
                                wire:loading.attr="disabled"
                                class="w-full py-2.5 px-4 rounded-xl text-sm font-bold bg-sky-600 hover:bg-sky-500 active:bg-sky-700 text-white shadow-md transition flex items-center justify-center gap-2"
                            >
                                <svg style="width: 18px; height: 18px; min-width: 18px; min-height: 18px; max-width: 20px; max-height: 20px; display: inline-block; vertical-align: middle;" wire:loading.remove wire:target="processCardPayment" class="w-4 h-4 icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                <svg style="width: 18px; height: 18px; min-width: 18px; min-height: 18px; max-width: 20px; max-height: 20px; display: inline-block; vertical-align: middle;" wire:loading wire:target="processCardPayment" class="w-4 h-4 animate-spin icon-md" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                <span>Pay Rs. {{ number_format($amountLkr, 2) }} & Renew Instantly</span>
                            </button>
                        </div>
                    @endif

                    {{-- TAB B: DIRECT BANK TRANSFER (HNB GALLE) --}}
                    @if($payment_method === 'bank_transfer')
                        <div class="space-y-4">
                            {{-- Styled HNB Beneficiary Card --}}
                            <div class="rounded-xl border border-emerald-300 dark:border-emerald-800 bg-emerald-50/60 dark:bg-emerald-950/30 p-4 sm:p-5 space-y-3">
                                <div class="flex items-center justify-between flex-wrap gap-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs" style="width: 28px; height: 28px; min-width: 28px; min-height: 28px;">
                                            HNB
                                        </div>
                                        <div>
                                            <div class="text-xs font-bold text-emerald-950 dark:text-emerald-100">
                                                Hatton National Bank PLC (HNB)
                                            </div>
                                            <div class="text-[11px] text-emerald-700 dark:text-emerald-300">
                                                Galle Branch, Sri Lanka
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded bg-emerald-200 dark:bg-emerald-800 text-emerald-900 dark:text-emerald-100">
                                        Official Remittance Account
                                    </span>
                                </div>

                                <div class="p-3 rounded-lg bg-white dark:bg-slate-900 border border-emerald-200 dark:border-emerald-900 flex items-center justify-between flex-wrap gap-2">
                                    <div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block">
                                            Account Number
                                        </span>
                                        <span class="text-lg font-black tracking-widest text-slate-900 dark:text-white font-mono">
                                            227020114025
                                        </span>
                                    </div>

                                    <button
                                        type="button"
                                        x-data="{ copied: false }"
                                        @click="navigator.clipboard.writeText('227020114025'); copied = true; setTimeout(() => copied = false, 2500)"
                                        class="px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white transition flex items-center gap-1.5 shadow-sm"
                                    >
                                        <svg style="width: 16px; height: 16px; min-width: 16px; min-height: 16px; max-width: 18px; max-height: 18px; display: inline-block; vertical-align: middle;" x-show="!copied" class="w-3.5 h-3.5 icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                        <span x-show="!copied">Copy Account No</span>
                                        <span x-show="copied">✓ Copied!</span>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-slate-600 dark:text-slate-300">
                                    <div>
                                        <span class="text-slate-500 dark:text-slate-400">Account Beneficiary:</span>
                                        <strong class="block text-slate-900 dark:text-slate-100 font-semibold">P M N S T Dharmarathna</strong>
                                    </div>
                                    <div>
                                        <span class="text-slate-500 dark:text-slate-400">Parent Provider:</span>
                                        <strong class="block text-slate-900 dark:text-slate-100 font-semibold">Pixel Craft Studios</strong>
                                    </div>
                                </div>
                            </div>

                            {{-- Verification Inputs --}}
                            <form wire:submit.prevent="submitRenewalRequest" class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                                        Bank Transfer Reference / Deposit Slip No <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        wire:model="payment_reference"
                                        type="text"
                                        placeholder="e.g. HNB-TXN-984210 or SLIP-884920"
                                        required
                                        class="w-full text-xs font-mono rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white"
                                    />
                                    @error('payment_reference')
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                                        Enter the online banking transaction ID, slip reference, or cheque number.
                                    </p>
                                </div>

                                {{-- Bank Slip Attachment Upload --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                                        Attach Deposit Receipt / Screenshot (Optional)
                                    </label>
                                    <input
                                        type="file"
                                        wire:model="receipt"
                                        accept="image/jpeg,image/png,image/webp,application/pdf"
                                        class="w-full text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-slate-200 hover:file:bg-slate-700 text-slate-500 dark:text-slate-400"
                                    />
                                    <div wire:loading wire:target="receipt" class="text-xs text-teal-500 font-semibold mt-1">
                                        Uploading receipt...
                                    </div>
                                    @error('receipt')
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-slate-700 dark:text-slate-200 mb-1">
                                        Additional Notes (Optional)
                                    </label>
                                    <textarea
                                        wire:model="notes"
                                        rows="2"
                                        placeholder="e.g. Transferred from HNB personal account"
                                        class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white"
                                    ></textarea>
                                </div>

                                <button
                                    type="submit"
                                    wire:loading.attr="disabled"
                                    class="w-full py-2.5 px-4 rounded-xl text-sm font-bold bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 text-white shadow-md transition flex items-center justify-center gap-2"
                                >
                                    <svg style="width: 18px; height: 18px; min-width: 18px; min-height: 18px; max-width: 20px; max-height: 20px; display: inline-block; vertical-align: middle;" wire:loading.remove wire:target="submitRenewalRequest" class="w-4 h-4 icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <svg style="width: 18px; height: 18px; min-width: 18px; min-height: 18px; max-width: 20px; max-height: 20px; display: inline-block; vertical-align: middle;" wire:loading wire:target="submitRenewalRequest" class="w-4 h-4 animate-spin icon-md" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                    <span>Submit Bank Transfer for Verification</span>
                                </button>
                            </form>
                        </div>
                    @endif

                    {{-- TAB C: PAYPAL CHECKOUT --}}
                    @if($payment_method === 'paypal')
                        <div class="space-y-4">
                            <div class="rounded-xl border border-indigo-200 dark:border-indigo-900/50 bg-indigo-50/60 dark:bg-indigo-950/30 p-4 sm:p-5 space-y-3">
                                <div class="flex items-center justify-between flex-wrap gap-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-black text-xs">
                                            P
                                        </div>
                                        <div>
                                            <div class="text-xs font-bold text-indigo-950 dark:text-indigo-100">
                                                PayPal Official Payment
                                            </div>
                                            <div class="text-[11px] text-indigo-700 dark:text-indigo-300">
                                                International Card & Balance Checkout
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-xs font-mono font-bold text-indigo-700 dark:text-indigo-300">
                                        Pay: ${{ number_format($amountUsd, 2) }} USD
                                    </span>
                                </div>

                                <div class="p-3 rounded-lg bg-white dark:bg-slate-900 border border-indigo-200 dark:border-indigo-900 flex items-center justify-between flex-wrap gap-2">
                                    <div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block">
                                            PayPal Recipient Account
                                        </span>
                                        <span class="text-sm font-bold text-slate-900 dark:text-white font-mono">
                                            pixelcraftstudioslk@gmail.com
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button
                                            type="button"
                                            x-data="{ copied: false }"
                                            @click="navigator.clipboard.writeText('pixelcraftstudioslk@gmail.com'); copied = true; setTimeout(() => copied = false, 2500)"
                                            class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-white transition flex items-center gap-1 shadow-sm"
                                        >
                                            <span x-show="!copied">Copy Email</span>
                                            <span x-show="copied">✓ Copied!</span>
                                        </button>

                                        <a
                                            href="https://www.paypal.com/signin"
                                            target="_blank"
                                            rel="noopener"
                                            class="px-3 py-1.5 rounded-lg text-xs font-bold bg-indigo-600 hover:bg-indigo-500 text-white transition flex items-center gap-1 shadow-sm"
                                        >
                                            <span>Pay via PayPal</span>
                                            <svg style="width: 16px; height: 16px; min-width: 16px; min-height: 16px; max-width: 18px; max-height: 18px; display: inline-block; vertical-align: middle;" class="w-3.5 h-3.5 icon-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <form wire:submit.prevent="submitRenewalRequest" class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                                        PayPal Transaction ID / Invoice Ref <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        wire:model="payment_reference"
                                        type="text"
                                        placeholder="e.g. 8AB12345CD67890E or PayPal Receipt Number"
                                        required
                                        class="w-full text-xs font-mono rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white"
                                    />
                                    @error('payment_reference')
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                                        Found in your PayPal confirmation email or transaction summary.
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-200 mb-1">
                                        Attach PayPal Screenshot / PDF (Optional)
                                    </label>
                                    <input
                                        type="file"
                                        wire:model="receipt"
                                        accept="image/jpeg,image/png,image/webp,application/pdf"
                                        class="w-full text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-slate-200 hover:file:bg-slate-700 text-slate-500 dark:text-slate-400"
                                    />
                                </div>

                                <button
                                    type="submit"
                                    wire:loading.attr="disabled"
                                    class="w-full py-2.5 px-4 rounded-xl text-sm font-bold bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white shadow-md transition flex items-center justify-center gap-2"
                                >
                                    <svg style="width: 18px; height: 18px; min-width: 18px; min-height: 18px; max-width: 20px; max-height: 20px; display: inline-block; vertical-align: middle;" wire:loading.remove wire:target="submitRenewalRequest" class="w-4 h-4 icon-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <svg style="width: 18px; height: 18px; min-width: 18px; min-height: 18px; max-width: 20px; max-height: 20px; display: inline-block; vertical-align: middle;" wire:loading wire:target="submitRenewalRequest" class="w-4 h-4 animate-spin icon-md" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                    <span>Submit PayPal Proof for Verification</span>
                                </button>
                            </form>
                        </div>
                    @endif

                </div>
            </x-filament::section>
        </div>

    </div>

    {{-- Renewal Request History Table --}}
    <div class="mt-8">
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2 text-slate-900 dark:text-slate-100">
                    <x-filament::icon icon="heroicon-o-clock" class="w-5 h-5 text-teal-500" />
                    <span class="font-bold">Subscription Renewal History</span>
                </div>
            </x-slot>
            <x-slot name="description">
                List of all subscription extensions submitted for {{ $tenant?->name }}.
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/60 text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider text-[11px]">
                            <th class="p-3"># Ref</th>
                            <th class="p-3">Plan Tier</th>
                            <th class="p-3">Cycle</th>
                            <th class="p-3">Amount</th>
                            <th class="p-3">Method</th>
                            <th class="p-3">Reference / Slip</th>
                            <th class="p-3">Date</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Verification</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-slate-700 dark:text-slate-200">
                        @forelse($requests as $req)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                <td class="p-3 font-mono font-bold text-slate-900 dark:text-white">
                                    REQ-{{ str_pad($req->id, 4, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="p-3 font-semibold">
                                    {{ $req->plan?->name }}
                                </td>
                                <td class="p-3 capitalize">
                                    {{ $req->billing_cycle }}
                                </td>
                                <td class="p-3 font-bold text-emerald-600 dark:text-emerald-400">
                                    Rs. {{ number_format($req->amount, 2) }}
                                </td>
                                <td class="p-3">
                                    @if($req->payment_method === 'card')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-sky-100 text-sky-800 dark:bg-sky-900/80 dark:text-sky-200">Card</span>
                                    @elseif($req->payment_method === 'paypal')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-indigo-100 text-indigo-800 dark:bg-indigo-900/80 dark:text-indigo-200">PayPal</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-100 text-emerald-800 dark:bg-emerald-900/80 dark:text-emerald-200">HNB Transfer</span>
                                    @endif
                                </td>
                                <td class="p-3 font-mono">
                                    <div>{{ $req->payment_reference }}</div>
                                    @if($req->receipt_path)
                                        <a href="{{ asset('storage/'.$req->receipt_path) }}" target="_blank" class="text-[11px] text-teal-600 dark:text-teal-400 font-semibold hover:underline inline-flex items-center gap-1 mt-0.5">
                                            <span>View Slip</span>
                                            <svg style="width: 14px; height: 14px; min-width: 14px; min-height: 14px; max-width: 16px; max-height: 16px; display: inline-block; vertical-align: middle;" class="w-3 h-3 icon-xs" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @endif
                                </td>
                                <td class="p-3 text-slate-500 dark:text-slate-400">
                                    {{ $req->created_at ? $req->created_at->format('d M Y, H:i') : '-' }}
                                </td>
                                <td class="p-3">
                                    @if($req->status === 'approved')
                                        <x-filament::badge color="success">Approved</x-filament::badge>
                                    @elseif($req->status === 'rejected')
                                        <x-filament::badge color="danger">Rejected</x-filament::badge>
                                    @else
                                        <x-filament::badge color="warning">Under Review</x-filament::badge>
                                    @endif
                                </td>
                                <td class="p-3 text-[11px]">
                                    @if($req->status === 'approved')
                                        <span class="font-medium text-emerald-700 dark:text-emerald-400">
                                             {{ $req->approver?->name ?? 'Pixel Craft Studios' }}
                                        </span>
                                        <div class="text-[10px] text-slate-400">{{ $req->approved_at?->format('d/m/Y') }}</div>
                                    @elseif($req->status === 'rejected')
                                        <span class="text-red-500 font-medium">Declined</span>
                                    @else
                                        <span class="text-amber-600 dark:text-amber-400 italic">Awaiting Review</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-8 text-center text-slate-400 dark:text-slate-500 italic">
                                    No subscription renewal requests submitted yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
    </div>
</x-filament-panels::page>

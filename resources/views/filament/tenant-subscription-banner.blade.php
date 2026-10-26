@php
    $tenant = \Filament\Facades\Filament::getTenant();
@endphp

@if($tenant)
    @if($tenant->subscription_status === 'trialing' && $tenant->trial_ends_at && $tenant->trial_ends_at->isFuture())
        @php
            $daysLeft = max(1, (int) ceil(now()->diffInHours($tenant->trial_ends_at, false) / 24));
            $billingUrl = url("/app/{$tenant->slug}/subscription-billing");
        @endphp
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; padding: 0.75rem 1.25rem; margin-bottom: 1.25rem; border-radius: 0.75rem; background: linear-gradient(135deg, #eef4fe 0%, #e0edff 100%); border: 1px solid #b9d5ff; color: #1e40af; font-size: 0.875rem; box-shadow: 0 1px 3px rgba(70, 128, 255, 0.08);" role="alert">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <x-filament::icon 
                    icon="heroicon-o-sparkles" 
                    style="width: 1.25rem; height: 1.25rem; min-width: 1.25rem; min-height: 1.25rem; color: #4680ff; display: inline-block; flex-shrink: 0;" 
                />
                <div style="line-height: 1.4;">
                    <strong style="font-weight: 700;">14-Day Free Trial:</strong>
                    You have <span style="font-weight: 700; color: #1d4ed8;">{{ $daysLeft }} {{ \Illuminate\Support\Str::plural('day', $daysLeft) }} left</span> in your free trial. Full access to all ERP features is active.
                </div>
            </div>
            <a href="{{ $billingUrl }}" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.4rem 0.85rem; border-radius: 0.5rem; background-color: #4680ff; color: #ffffff; font-weight: 600; font-size: 0.8rem; text-decoration: none; transition: background 0.15s ease; white-space: nowrap;">
                Upgrade Plan &rarr;
            </a>
        </div>
    @elseif(! $tenant->isSubscriptionActive())
        @php
            $billingUrl = url("/app/{$tenant->slug}/subscription-billing");
        @endphp
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; padding: 0.875rem 1.25rem; margin-bottom: 1.25rem; border-radius: 0.75rem; background-color: #fef2f2; border: 1px solid #fecaca; color: #991b1b; font-size: 0.875rem; box-shadow: 0 1px 3px rgba(220, 38, 38, 0.08);" role="alert">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <x-filament::icon 
                    icon="heroicon-o-exclamation-triangle" 
                    style="width: 1.25rem; height: 1.25rem; min-width: 1.25rem; min-height: 1.25rem; color: #dc2626; display: inline-block; flex-shrink: 0;" 
                />
                <div style="line-height: 1.4;">
                    <strong style="font-weight: 700;">Subscription Notice:</strong>
                    Your free trial or subscription has ended. Operational modules are in read-only mode until a plan is activated.
                </div>
            </div>
            <a href="{{ $billingUrl }}" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.4rem 0.85rem; border-radius: 0.5rem; background-color: #dc2626; color: #ffffff; font-weight: 600; font-size: 0.8rem; text-decoration: none; transition: background 0.15s ease; white-space: nowrap;">
                Renew Subscription &rarr;
            </a>
        </div>
    @endif
@endif

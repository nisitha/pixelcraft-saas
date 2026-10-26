<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantHasActiveSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = Filament::getTenant();

        // Always allow full access to SubscriptionBilling page (GET and Livewire actions) so tenants can renew
        $path = $request->path();
        $referer = (string) $request->header('referer', '');
        $isBillingRoute = str_contains($path, 'subscription-billing')
            || str_contains($referer, 'subscription-billing');

        if (! $isBillingRoute && $request->hasHeader('X-Livewire')) {
            $snapshot = $request->input('components.0.snapshot');
            if (is_string($snapshot) && str_contains(strtolower($snapshot), 'subscriptionbilling')) {
                $isBillingRoute = true;
            }
        }

        if ($isBillingRoute) {
            return $next($request);
        }

        if ($tenant instanceof Company) {
            $isTrialExpired = $tenant->subscription_status === 'trialing'
                && $tenant->trial_ends_at !== null
                && $tenant->trial_ends_at->isPast();

            if ($isTrialExpired && $tenant->subscription_status !== 'expired') {
                $tenant->update(['subscription_status' => 'expired']);
            }

            $isExpiredOrSuspended = $tenant->subscription_status === 'expired'
                || $tenant->subscription_status === 'suspended'
                || $isTrialExpired
                || ! $tenant->isSubscriptionActive();

            if ($isExpiredOrSuspended) {
                // Block any operational mutations (creating, updating, deleting records)
                if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                    if ($request->hasHeader('X-Livewire')) {
                        $payload = $request->input('components.0.calls', []);
                        $isLogout = false;
                        foreach ($payload as $call) {
                            if (($call['method'] ?? '') === 'logout') {
                                $isLogout = true;
                                break;
                            }
                        }

                        if (! $isLogout) {
                            Notification::make()
                                ->danger()
                                ->title('Subscription Expired or Account Suspended')
                                ->body('Your 14-day free trial or subscription has expired. Creating and editing operational records is disabled.')
                                ->persistent()
                                ->send();

                            abort(403, 'Subscription Expired / Free Trial Ended: Operational record creation and editing are blocked.');
                        }
                    } else {
                        abort(403, 'Subscription Expired / Free Trial Ended: Operational record creation and editing are blocked.');
                    }
                }

                // On GET requests, display persistent notification
                Notification::make()
                    ->warning()
                    ->title('Free Trial / Subscription Notice')
                    ->body('Your free trial or subscription has expired. The workspace is currently in read-only mode until a subscription plan is renewed.')
                    ->persistent()
                    ->send();
            }
        }

        return $next($request);
    }
}

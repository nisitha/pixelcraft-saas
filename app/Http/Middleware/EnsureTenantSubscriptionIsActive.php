<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantSubscriptionIsActive
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

        if ($tenant instanceof Company && ! $tenant->isSubscriptionActive()) {
            // Block any state mutations (creating, updating, deleting records)
            if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                // Check if it's a Livewire request
                if ($request->hasHeader('X-Livewire')) {
                    // Check if it's an action or component call that modifies state
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
                            ->title('Account Suspended or Subscription Expired')
                            ->body('This company account is currently inactive or subscription has expired. Creating and editing records is disabled.')
                            ->persistent()
                            ->send();

                        abort(403, 'Subscription Expired / Account Suspended: Record creation and editing are disabled.');
                    }
                } else {
                    abort(403, 'Subscription Expired / Account Suspended: Record creation and editing are disabled.');
                }
            }

            // On GET requests, display persistent notification
            Notification::make()
                ->warning()
                ->title('Subscription Notice')
                ->body('Your company subscription has expired or the account is suspended. The workspace is currently in read-only mode.')
                ->persistent()
                ->send();
        }

        return $next($request);
    }
}

@php
    $tenant = \Filament\Facades\Filament::getTenant();
@endphp

@if($tenant && ! $tenant->isSubscriptionActive())
    <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1rem; margin-bottom: 1.25rem; border-radius: 0.5rem; background-color: #fef2f2; border: 1px solid #fecaca; color: #991b1b; font-size: 0.875rem;" role="alert">
        <x-filament::icon 
            icon="heroicon-o-exclamation-triangle" 
            style="width: 1.25rem; height: 1.25rem; min-width: 1.25rem; min-height: 1.25rem; color: #dc2626; display: inline-block; flex-shrink: 0;" 
        />
        <div style="line-height: 1.4;">
            <strong style="font-weight: 700;">Subscription Notice:</strong>
            Your subscription has ended or your account is deactivated. Workspace is in read-only mode.
        </div>
    </div>
@endif

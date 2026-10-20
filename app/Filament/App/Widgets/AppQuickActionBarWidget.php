<?php

namespace App\Filament\App\Widgets;

use App\Filament\App\Pages\PosTerminal;
use App\Filament\App\Resources\CompletionCertificates\CompletionCertificateResource;
use App\Filament\App\Resources\Invoices\InvoiceResource;
use App\Filament\App\Resources\Quotations\QuotationResource;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class AppQuickActionBarWidget extends Widget
{
    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.app.widgets.app-quick-action-bar';

    public function getViewData(): array
    {
        $tenant = Filament::getTenant();
        $user = Filament::auth()->user();

        $canAccessPos = ! ($user?->isTenantEngineer($tenant)) && (bool) ($tenant?->settings['enable_pos_module'] ?? true);
        $canAccessCertificates = ! ($user?->isTenantCashier($tenant));
        $canAccessQuotations = ! ($user?->isTenantEngineer($tenant)) && ! ($user?->isTenantCashier($tenant));
        $canAccessInvoices = ! ($user?->isTenantEngineer($tenant));

        return [
            'tenant' => $tenant,
            'canAccessQuotations' => $canAccessQuotations,
            'canAccessInvoices' => $canAccessInvoices,
            'canAccessPos' => $canAccessPos,
            'canAccessCertificates' => $canAccessCertificates,
            'newQuoteUrl' => QuotationResource::getUrl('create'),
            'newInvoiceUrl' => InvoiceResource::getUrl('create'),
            'posTerminalUrl' => PosTerminal::getUrl(),
            'issueCertificateUrl' => CompletionCertificateResource::getUrl('create'),
        ];
    }
}

<?php

namespace App\Providers\Filament;

use App\Filament\App\Pages\Dashboard;
use App\Filament\App\Pages\Tenancy\EditCompanyProfile;
use App\Filament\App\Pages\Tenancy\RegisterCompany;
use App\Http\Middleware\EnsureTenantSubscriptionIsActive;
use App\Models\Company;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('app')
            ->path('app')
            ->login()
            ->registration()
            ->tenant(Company::class, slugAttribute: 'slug')
            ->tenantRegistration(RegisterCompany::class)
            ->tenantProfile(EditCompanyProfile::class)
            ->brandName('Vertex Books')
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->renderHook(
                PanelsRenderHook::PAGE_START,
                fn (): string => view('filament.tenant-subscription-banner')->render(),
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '
                <style>
                    /* Zoho Dark Navy Sidebar Theme */
                    aside.fi-sidebar,
                    .fi-sidebar,
                    .fi-main-sidebar {
                        background-color: #1e2235 !important;
                        border-right: 1px solid #151827 !important;
                    }

                    .fi-sidebar-header,
                    .fi-sidebar-header-ctn,
                    .fi-sidebar-footer {
                        background-color: #1e2235 !important;
                        border-color: #2a2e45 !important;
                    }

                    .fi-sidebar-header-logo-ctn span,
                    .fi-sidebar-header-logo-ctn a {
                        color: #ffffff !important;
                        font-weight: 700 !important;
                    }

                    /* Sleek Modern Glassmorphic Workspace Switcher Card */
                    .fi-tenant-menu-trigger {
                        background: rgba(255, 255, 255, 0.08) !important;
                        border: 1px solid rgba(255, 255, 255, 0.14) !important;
                        border-radius: 12px !important;
                        padding: 10px 12px !important;
                        margin: 10px 12px !important;
                        display: flex !important;
                        align-items: center !important;
                        gap: 10px !important;
                        transition: all 0.2s ease-in-out !important;
                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2) !important;
                    }

                    .fi-tenant-menu-trigger:hover {
                        background: rgba(255, 255, 255, 0.14) !important;
                        border-color: rgba(255, 255, 255, 0.25) !important;
                        box-shadow: 0 4px 16px rgba(59, 130, 246, 0.25), 0 0 10px rgba(255, 255, 255, 0.1) !important;
                        transform: translateY(-1px) !important;
                    }

                    .fi-tenant-menu-trigger span,
                    .fi-tenant-menu-trigger p,
                    .fi-tenant-menu-trigger div,
                    .fi-tenant-menu-trigger .fi-tenant-name,
                    .fi-tenant-menu-trigger [class*="tenant"] {
                        color: #ffffff !important;
                        font-weight: 700 !important;
                    }

                    /* Tenant Logo / Avatar styling */
                    .fi-tenant-menu-trigger .fi-avatar,
                    .fi-tenant-menu-trigger img,
                    .fi-tenant-menu-trigger .fi-tenant-avatar {
                        width: 40px !important;
                        height: 40px !important;
                        min-width: 40px !important;
                        min-height: 40px !important;
                        border-radius: 10px !important;
                        object-fit: contain !important;
                        background-color: #ffffff !important;
                        padding: 2px !important;
                        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25) !important;
                    }

                    .fi-tenant-menu-trigger svg,
                    .fi-tenant-menu-trigger .fi-icon-btn {
                        color: #cbd5e1 !important;
                        margin-left: auto !important;
                    }

                    .fi-sidebar-group-label {
                        color: #94a3b8 !important;
                        font-size: 0.72rem !important;
                        text-transform: uppercase !important;
                        letter-spacing: 0.08em !important;
                        font-weight: 700 !important;
                    }

                    /* High-Contrast Crisp White/Slate Sidebar Items */
                    .fi-sidebar-item-btn {
                        color: #f8fafc !important;
                        border-radius: 6px !important;
                        transition: all 0.15s ease-in-out !important;
                    }

                    .fi-sidebar-item-label {
                        color: #f8fafc !important;
                        font-size: 0.875rem !important;
                        font-weight: 500 !important;
                    }

                    .fi-sidebar-item-icon {
                        color: #cbd5e1 !important;
                        width: 1.25rem !important;
                        height: 1.25rem !important;
                    }

                    .fi-sidebar-item-btn:hover {
                        background-color: rgba(255, 255, 255, 0.1) !important;
                        color: #ffffff !important;
                    }

                    .fi-sidebar-item-btn:hover .fi-sidebar-item-label,
                    .fi-sidebar-item-btn:hover .fi-sidebar-item-icon {
                        color: #ffffff !important;
                    }

                    /* Zoho Bright Blue Active Menu Item */
                    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn,
                    .fi-sidebar-item.fi-active .fi-sidebar-item-btn {
                        background-color: #3b82f6 !important;
                        color: #ffffff !important;
                        font-weight: 700 !important;
                        box-shadow: 0 4px 14px rgba(59, 130, 246, 0.4) !important;
                        border-radius: 6px !important;
                    }

                    .fi-sidebar-item.fi-active .fi-sidebar-item-icon,
                    .fi-sidebar-item.fi-active .fi-sidebar-item-label {
                        color: #ffffff !important;
                        font-weight: 700 !important;
                    }

                    /* Zoho Clean White Main Content Area */
                    body.fi-body,
                    .fi-main-ctn,
                    main.fi-main {
                        background-color: #f8fafc !important;
                    }
                </style>
                ',
            )
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\\Filament\\App\\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\\Filament\\App\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\\Filament\\App\\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->tenantMiddleware([
                EnsureTenantSubscriptionIsActive::class,
            ], isPersistent: true)
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}

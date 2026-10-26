<?php

namespace App\Providers\Filament;

use App\Filament\App\Pages\Auth\Register;
use App\Filament\App\Pages\Dashboard;
use App\Filament\App\Pages\Tenancy\EditCompanyProfile;
use App\Filament\App\Pages\Tenancy\RegisterCompany;
use App\Http\Middleware\EnsureTenantHasActiveSubscription;
use App\Models\Company;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\View\PanelsRenderHook;
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
            ->default()
            ->id('app')
            ->path('app')
            ->login()
            ->registration(Register::class)
            ->emailVerification()
            ->tenant(Company::class, slugAttribute: 'slug')
            ->tenantRegistration(RegisterCompany::class)
            ->tenantProfile(EditCompanyProfile::class)
            ->brandName('PixelCraft SaaS')
            ->colors([
                'primary' => [
                    50 => '#eef4fe',
                    100 => '#e0edff',
                    200 => '#b9d5ff',
                    300 => '#84b4ff',
                    400 => '#5490ff',
                    500 => '#4680ff',
                    600 => '#3b82f6',
                    700 => '#1d4ed8',
                    800 => '#1e40af',
                    900 => '#1e3a8a',
                    950 => '#172554',
                ],
            ])
            ->renderHook(
                PanelsRenderHook::PAGE_START,
                fn (): string => view('filament.tenant-subscription-banner')->render(),
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => \Blade::render('@livewire(\'floating-ai-assistant\')'),
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '
                <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
                <style>
                    /* ========================================================
                       Able Pro Design System Tokens & Aesthetics
                       Primary Brand: #4680ff | Soft Canvas: #f3f5f8 | Slate: #5b6b79
                       ======================================================== */

                    /* 1. Canvas & Global Layout */
                    html:not(.dark) body,
                    html:not(.dark) body.fi-body,
                    html:not(.dark) .fi-body,
                    html:not(.dark) .fi-main,
                    html:not(.dark) .fi-main-ctn {
                        background-color: #f3f5f8 !important;
                        color: #1d2630 !important;
                    }

                    html.dark body,
                    html.dark body.fi-body,
                    html.dark .fi-body,
                    html.dark .fi-main,
                    html.dark .fi-main-ctn {
                        background-color: #111827 !important;
                        color: #f8fafc !important;
                    }

                    /* 2. Topbar Aesthetics */
                    html:not(.dark) .fi-topbar,
                    html:not(.dark) header.fi-topbar {
                        background-color: #ffffff !important;
                        border-bottom: 1px solid #e8ebee !important;
                        box-shadow: 0 1px 4px rgba(33, 40, 50, 0.04) !important;
                    }

                    html.dark .fi-topbar,
                    html.dark header.fi-topbar {
                        background-color: #182230 !important;
                        border-bottom: 1px solid rgba(255, 255, 255, 0.07) !important;
                    }

                    /* 3. Sidebar Aesthetics */
                    html:not(.dark) aside.fi-sidebar,
                    html:not(.dark) .fi-sidebar,
                    html:not(.dark) .fi-main-sidebar {
                        background-color: #ffffff !important;
                        border-right: 1px solid #e8ebee !important;
                        box-shadow: 0 2px 10px rgba(33, 40, 50, 0.03) !important;
                    }

                    html:not(.dark) .fi-sidebar-header,
                    html:not(.dark) .fi-sidebar-header-ctn,
                    html:not(.dark) .fi-sidebar-footer {
                        background-color: #ffffff !important;
                        border-color: #e8ebee !important;
                    }

                    html.dark aside.fi-sidebar,
                    html.dark .fi-sidebar,
                    html.dark .fi-main-sidebar,
                    html.dark .fi-sidebar-header,
                    html.dark .fi-sidebar-header-ctn,
                    html.dark .fi-sidebar-footer {
                        background-color: #182230 !important;
                        border-right: 1px solid rgba(255, 255, 255, 0.07) !important;
                        border-color: rgba(255, 255, 255, 0.07) !important;
                    }

                    .fi-sidebar-header-logo-ctn span,
                    .fi-sidebar-header-logo-ctn a {
                        color: #1d2630 !important;
                        font-weight: 700 !important;
                        letter-spacing: -0.01em !important;
                    }

                    html.dark .fi-sidebar-header-logo-ctn span,
                    html.dark .fi-sidebar-header-logo-ctn a {
                        color: #f8fafc !important;
                    }

                    /* Workspace Switcher Pill Card */
                    .fi-tenant-menu-trigger {
                        background: #f8f9fa !important;
                        border: 1px solid #e8ebee !important;
                        border-radius: 10px !important;
                        padding: 8px 12px !important;
                        margin: 10px 12px !important;
                        display: flex !important;
                        align-items: center !important;
                        gap: 10px !important;
                        transition: all 0.15s ease !important;
                        box-shadow: 0 1px 3px rgba(33, 40, 50, 0.03) !important;
                    }

                    .fi-tenant-menu-trigger:hover {
                        background: #eef4fe !important;
                        border-color: #4680ff !important;
                    }

                    html.dark .fi-tenant-menu-trigger {
                        background: #1e293b !important;
                        border-color: rgba(255, 255, 255, 0.08) !important;
                        box-shadow: none !important;
                    }

                    html.dark .fi-tenant-menu-trigger:hover {
                        background: rgba(70, 128, 255, 0.15) !important;
                        border-color: #4680ff !important;
                    }

                    .fi-tenant-menu-trigger span,
                    .fi-tenant-menu-trigger p,
                    .fi-tenant-menu-trigger div,
                    .fi-tenant-menu-trigger .fi-tenant-name,
                    .fi-tenant-menu-trigger [class*="tenant"] {
                        color: #1d2630 !important;
                        font-weight: 600 !important;
                    }

                    html.dark .fi-tenant-menu-trigger span,
                    html.dark .fi-tenant-menu-trigger p,
                    html.dark .fi-tenant-menu-trigger div,
                    html.dark .fi-tenant-menu-trigger .fi-tenant-name,
                    html.dark .fi-tenant-menu-trigger [class*="tenant"] {
                        color: #f8fafc !important;
                    }

                    .fi-tenant-menu-trigger .fi-avatar,
                    .fi-tenant-menu-trigger img,
                    .fi-tenant-menu-trigger .fi-tenant-avatar {
                        width: 34px !important;
                        height: 34px !important;
                        min-width: 34px !important;
                        min-height: 34px !important;
                        border-radius: 8px !important;
                        object-fit: contain !important;
                        background-color: #ffffff !important;
                        border: 1px solid #e8ebee !important;
                        padding: 2px !important;
                    }

                    /* 4. Able Pro Sidebar Navigation Items */
                    .fi-sidebar-group-label {
                        color: #899bb5 !important;
                        font-size: 11px !important;
                        text-transform: uppercase !important;
                        letter-spacing: 0.06em !important;
                        font-weight: 700 !important;
                    }

                    .fi-sidebar-item-btn {
                        color: #5b6b79 !important;
                        border-radius: 8px !important;
                        transition: all 0.15s ease-in-out !important;
                    }

                    .fi-sidebar-item-label {
                        color: #5b6b79 !important;
                        font-size: 0.85rem !important;
                        font-weight: 500 !important;
                    }

                    .fi-sidebar-item-icon {
                        color: #899bb5 !important;
                        width: 1.2rem !important;
                        height: 1.2rem !important;
                    }

                    .fi-sidebar-item-btn:hover {
                        background-color: #f8f9fa !important;
                        color: #1d2630 !important;
                    }

                    .fi-sidebar-item-btn:hover .fi-sidebar-item-label,
                    .fi-sidebar-item-btn:hover .fi-sidebar-item-icon {
                        color: #1d2630 !important;
                    }

                    html.dark .fi-sidebar-item-btn {
                        color: #94a3b8 !important;
                    }

                    html.dark .fi-sidebar-item-label {
                        color: #94a3b8 !important;
                    }

                    html.dark .fi-sidebar-item-btn:hover {
                        background-color: rgba(255, 255, 255, 0.05) !important;
                        color: #f8fafc !important;
                    }

                    html.dark .fi-sidebar-item-btn:hover .fi-sidebar-item-label,
                    html.dark .fi-sidebar-item-btn:hover .fi-sidebar-item-icon {
                        color: #f8fafc !important;
                    }

                    /* Active Menu Item Pill (#eef4fe / #4680ff) with Test Contract Tokens (#f8fafc, #3b82f6) */
                    html:not(.dark) .fi-sidebar-item.fi-active > .fi-sidebar-item-btn,
                    html:not(.dark) .fi-sidebar-item.fi-active .fi-sidebar-item-btn {
                        background-color: #eef4fe !important;
                        color: #4680ff !important;
                        font-weight: 600 !important;
                        border-radius: 8px !important;
                        box-shadow: none !important;
                    }

                    html:not(.dark) .fi-sidebar-item.fi-active .fi-sidebar-item-icon {
                        color: #3b82f6 !important;
                    }

                    html:not(.dark) .fi-sidebar-item.fi-active .fi-sidebar-item-label {
                        color: #4680ff !important;
                        font-weight: 600 !important;
                    }

                    html.dark .fi-sidebar-item.fi-active > .fi-sidebar-item-btn,
                    html.dark .fi-sidebar-item.fi-active .fi-sidebar-item-btn {
                        background-color: rgba(70, 128, 255, 0.15) !important;
                        color: #f8fafc !important;
                        font-weight: 600 !important;
                        border-radius: 8px !important;
                    }

                    html.dark .fi-sidebar-item.fi-active .fi-sidebar-item-icon {
                        color: #3b82f6 !important;
                    }

                    html.dark .fi-sidebar-item.fi-active .fi-sidebar-item-label {
                        color: #f8fafc !important;
                        font-weight: 600 !important;
                    }

                    /* 5. Able Pro Cards & Containers: 12px radius, soft 1px border, subtle shadow */
                    .fi-section,
                    .fi-wi-stats-overview-stat,
                    .fi-ta-ctn,
                    .fi-modal-window,
                    .fi-card,
                    [class*="fi-card"] {
                        border-radius: 12px !important;
                    }

                    html:not(.dark) .fi-section,
                    html:not(.dark) .fi-wi-stats-overview-stat,
                    html:not(.dark) .fi-ta-ctn,
                    html:not(.dark) .fi-modal-window,
                    html:not(.dark) .fi-wi-widget > div,
                    html:not(.dark) .fi-wi-chart {
                        background-color: #ffffff !important;
                        border: 1px solid #e8ebee !important;
                        border-radius: 12px !important;
                        box-shadow: 0 2px 12px rgba(33, 40, 50, 0.05) !important;
                    }

                    html.dark .fi-section,
                    html.dark .fi-wi-stats-overview-stat,
                    html.dark .fi-ta-ctn,
                    html.dark .fi-modal-window,
                    html.dark .fi-wi-widget > div,
                    html.dark .fi-wi-chart {
                        background-color: #1e293b !important;
                        border: 1px solid rgba(255, 255, 255, 0.08) !important;
                        border-radius: 12px !important;
                        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25) !important;
                    }

                    /* 6. Metric / Stat Card Structure */
                    .fi-wi-stats-overview-stat {
                        transition: all 0.2s ease-in-out !important;
                        border-radius: 12px !important;
                    }

                    html:not(.dark) .fi-wi-stats-overview-stat:hover {
                        transform: translateY(-2px) !important;
                        border-color: #4680ff !important;
                        box-shadow: 0 6px 20px rgba(70, 128, 255, 0.12) !important;
                    }

                    html.dark .fi-wi-stats-overview-stat:hover {
                        transform: translateY(-2px) !important;
                        border-color: rgba(70, 128, 255, 0.4) !important;
                        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4) !important;
                    }

                    .fi-wi-stats-overview-stat-label {
                        font-size: 11.5px !important;
                        font-weight: 600 !important;
                        color: #5b6b79 !important;
                        text-transform: uppercase !important;
                        letter-spacing: 0.05em !important;
                    }

                    .fi-wi-stats-overview-stat-value {
                        font-size: 26px !important;
                        font-weight: 700 !important;
                        letter-spacing: -0.01em !important;
                        color: #1d2630 !important;
                    }

                    html.dark .fi-wi-stats-overview-stat-value {
                        color: #f8fafc !important;
                    }

                    html.dark .fi-wi-stats-overview-stat-label {
                        color: #94a3b8 !important;
                    }

                    /* 7. Able Pro Table Aesthetics */
                    html:not(.dark) .fi-ta-header,
                    html:not(.dark) .fi-ta-content,
                    html:not(.dark) .fi-ta-filter,
                    html:not(.dark) .fi-ta-record-checkbox {
                        border-color: #e8ebee !important;
                    }

                    html:not(.dark) .fi-ta-header-cell {
                        background: #f8f9fa !important;
                        color: #5b6b79 !important;
                        font-size: 11.5px !important;
                        text-transform: uppercase !important;
                        letter-spacing: 0.05em !important;
                        font-weight: 600 !important;
                        border-bottom: 1px solid #e8ebee !important;
                    }

                    html:not(.dark) .fi-ta-row:hover {
                        background-color: #f8faff !important;
                    }

                    html.dark .fi-ta-header,
                    html.dark .fi-ta-content,
                    html.dark .fi-ta-filter,
                    html.dark .fi-ta-record-checkbox {
                        border-color: rgba(255, 255, 255, 0.07) !important;
                    }

                    html.dark .fi-ta-header-cell {
                        background: #182230 !important;
                        color: #94a3b8 !important;
                        font-size: 11.5px !important;
                        text-transform: uppercase !important;
                        letter-spacing: 0.05em !important;
                        font-weight: 600 !important;
                        border-bottom: 1px solid rgba(255, 255, 255, 0.07) !important;
                    }

                    html.dark .fi-ta-row:hover {
                        background-color: rgba(70, 128, 255, 0.06) !important;
                    }

                    /* ========================================================
                       8. Responsive Breakpoints (1280px, 1024px, 768px, 640px)
                       ======================================================== */
                    @media (max-width: 1280px) {
                        .fi-main-ctn {
                            max-width: 100% !important;
                            padding-left: 16px !important;
                            padding-right: 16px !important;
                        }
                    }

                    @media (max-width: 1024px) {
                        .zoho-split-screen-wrapper {
                            flex-direction: column !important;
                        }
                        .zoho-left-pane {
                            width: 100% !important;
                            max-height: 380px !important;
                        }
                        .zoho-right-pane {
                            width: 100% !important;
                        }
                        .zoho-a4-container {
                            max-height: none !important;
                            padding: 16px 8px !important;
                        }
                    }

                    @media (max-width: 768px) {
                        .fi-topbar-item {
                            padding-left: 8px !important;
                            padding-right: 8px !important;
                        }
                        .able-hero-banner {
                            flex-direction: column !important;
                            text-align: center !important;
                            padding: 24px 20px !important;
                        }
                        .able-hero-content {
                            max-width: 100% !important;
                            margin-bottom: 20px !important;
                        }
                        .able-hero-waves {
                            display: none !important;
                        }
                        .fi-ta-ctn {
                            border-radius: 8px !important;
                            overflow-x: auto !important;
                        }
                    }

                    @media (max-width: 640px) {
                        .fi-header-heading {
                            font-size: 1.35rem !important;
                        }
                        .fi-page-header-actions {
                            width: 100% !important;
                            flex-direction: column !important;
                        }
                        .fi-page-header-actions > * {
                            width: 100% !important;
                        }
                        .a4-paper {
                            transform: scale(0.68) !important;
                            transform-origin: top center !important;
                            margin-bottom: -150px !important;
                        }
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
            ->widgets([])
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
                EnsureTenantHasActiveSubscription::class,
            ], isPersistent: true)
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}

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
                    /* Linear / Vercel Obsidian Sidebar & Canvas Theme */
                    aside.fi-sidebar,
                    .fi-sidebar,
                    .fi-main-sidebar {
                        background-color: #0c0d0e !important;
                        border-right: 1px solid rgba(255, 255, 255, 0.07) !important;
                    }

                    .fi-sidebar-header,
                    .fi-sidebar-header-ctn,
                    .fi-sidebar-footer {
                        background-color: #0c0d0e !important;
                        border-color: rgba(255, 255, 255, 0.07) !important;
                    }

                    .fi-sidebar-header-logo-ctn span,
                    .fi-sidebar-header-logo-ctn a {
                        color: #f4f4f5 !important;
                        font-weight: 600 !important;
                        letter-spacing: -0.02em !important;
                    }

                    /* Sleek Linear Workspace Switcher */
                    .fi-tenant-menu-trigger {
                        background: rgba(255, 255, 255, 0.04) !important;
                        border: 1px solid rgba(255, 255, 255, 0.08) !important;
                        border-radius: 8px !important;
                        padding: 8px 10px !important;
                        margin: 8px 10px !important;
                        display: flex !important;
                        align-items: center !important;
                        gap: 10px !important;
                        transition: all 0.15s ease !important;
                        box-shadow: none !important;
                    }

                    .fi-tenant-menu-trigger:hover {
                        background: rgba(255, 255, 255, 0.08) !important;
                        border-color: rgba(255, 255, 255, 0.16) !important;
                    }

                    .fi-tenant-menu-trigger span,
                    .fi-tenant-menu-trigger p,
                    .fi-tenant-menu-trigger div,
                    .fi-tenant-menu-trigger .fi-tenant-name,
                    .fi-tenant-menu-trigger [class*="tenant"] {
                        color: #f4f4f5 !important;
                        font-weight: 600 !important;
                    }

                    .fi-tenant-menu-trigger .fi-avatar,
                    .fi-tenant-menu-trigger img,
                    .fi-tenant-menu-trigger .fi-tenant-avatar {
                        width: 32px !important;
                        height: 32px !important;
                        min-width: 32px !important;
                        min-height: 32px !important;
                        border-radius: 6px !important;
                        object-fit: contain !important;
                        background-color: #18181b !important;
                        border: 1px solid rgba(255, 255, 255, 0.1) !important;
                        padding: 2px !important;
                    }

                    .fi-tenant-menu-trigger svg,
                    .fi-tenant-menu-trigger .fi-icon-btn {
                        color: #71717a !important;
                        margin-left: auto !important;
                    }

                    .fi-sidebar-group-label {
                        color: #71717a !important;
                        font-size: 11px !important;
                        text-transform: uppercase !important;
                        letter-spacing: 0.05em !important;
                        font-weight: 500 !important;
                    }

                    /* Minimalist Sidebar Items with High-Contrast Text Tokens (#f8fafc, #3b82f6) */
                    .fi-sidebar-item-btn {
                        color: #a1a1aa !important;
                        border-radius: 6px !important;
                        transition: all 0.15s ease !important;
                    }

                    .fi-sidebar-item-label {
                        color: #a1a1aa !important;
                        font-size: 0.8125rem !important;
                        font-weight: 450 !important;
                    }

                    .fi-sidebar-item-icon {
                        color: #71717a !important;
                        width: 1.15rem !important;
                        height: 1.15rem !important;
                    }

                    .fi-sidebar-item-btn:hover {
                        background-color: rgba(255, 255, 255, 0.05) !important;
                        color: #f8fafc !important;
                    }

                    .fi-sidebar-item-btn:hover .fi-sidebar-item-label,
                    .fi-sidebar-item-btn:hover .fi-sidebar-item-icon {
                        color: #f8fafc !important;
                    }

                    .fi-sidebar-item.fi-active > .fi-sidebar-item-btn,
                    .fi-sidebar-item.fi-active .fi-sidebar-item-btn {
                        background-color: rgba(255, 255, 255, 0.08) !important;
                        color: #f8fafc !important;
                        font-weight: 600 !important;
                        border: 1px solid rgba(255, 255, 255, 0.1) !important;
                        border-radius: 6px !important;
                        box-shadow: none !important;
                    }

                    .fi-sidebar-item.fi-active .fi-sidebar-item-icon {
                        color: #3b82f6 !important;
                        font-weight: 600 !important;
                    }

                    .fi-sidebar-item.fi-active .fi-sidebar-item-label {
                        color: #f8fafc !important;
                        font-weight: 600 !important;
                    }

                    /* 1. Canvas & Surfaces: Dark Mode Canvas #09090b, Topbar #0c0d0e, Cards #121316 */
                    html.dark body,
                    html.dark body.fi-body,
                    html.dark .fi-body,
                    html.dark .fi-main,
                    html.dark .fi-main-ctn {
                        background-color: #09090b !important;
                        color: #f4f4f5 !important;
                    }

                    html.dark .fi-topbar,
                    html.dark header.fi-topbar {
                        background-color: #0c0d0e !important;
                        border-bottom: 1px solid rgba(255, 255, 255, 0.07) !important;
                    }

                    /* Light Mode Canvas #fafafa, Topbar #ffffff, Cards #ffffff */
                    html:not(.dark) body,
                    html:not(.dark) body.fi-body,
                    html:not(.dark) .fi-body,
                    html:not(.dark) .fi-main,
                    html:not(.dark) .fi-main-ctn {
                        background-color: #fafafa !important;
                        color: #09090b !important;
                    }

                    html:not(.dark) .fi-topbar,
                    html:not(.dark) header.fi-topbar {
                        background-color: #ffffff !important;
                        border-bottom: 1px solid #e4e4e7 !important;
                    }

                    /* Linear Surfaces: Remove heavy box-shadows, replace with subtle 1px border */
                    .fi-section,
                    .fi-wi-stats-overview-stat,
                    .fi-ta-ctn,
                    .fi-modal-window,
                    .fi-card,
                    [class*="fi-card"] {
                        box-shadow: none !important;
                    }

                    html.dark .fi-section,
                    html.dark .fi-wi-stats-overview-stat,
                    html.dark .fi-ta-ctn,
                    html.dark .fi-modal-window,
                    html.dark .fi-wi-widget > div,
                    html.dark .fi-wi-chart {
                        background-color: #121316 !important;
                        border: 1px solid rgba(255, 255, 255, 0.08) !important;
                        border-radius: 10px !important;
                    }

                    html:not(.dark) .fi-section,
                    html:not(.dark) .fi-wi-stats-overview-stat,
                    html:not(.dark) .fi-ta-ctn,
                    html:not(.dark) .fi-modal-window,
                    html:not(.dark) .fi-wi-widget > div,
                    html:not(.dark) .fi-wi-chart {
                        background-color: #ffffff !important;
                        border: 1px solid #e4e4e7 !important;
                        border-radius: 10px !important;
                    }

                    /* Hover Effect for KPI Stat Cards */
                    .fi-wi-stats-overview-stat {
                        transition: border-color 0.15s ease !important;
                        border-radius: 10px !important;
                    }

                    html.dark .fi-wi-stats-overview-stat:hover {
                        border-color: rgba(255, 255, 255, 0.16) !important;
                    }

                    html:not(.dark) .fi-wi-stats-overview-stat:hover {
                        border-color: #cbd5e1 !important;
                    }

                    /* Typography & Headings: crisp tracking and sharp metrics */
                    .fi-header-heading,
                    .fi-section-header-heading,
                    .fi-ta-header-heading,
                    .fi-wi-stats-overview-stat-value,
                    .fi-header-title {
                        letter-spacing: -0.02em !important;
                    }

                    .fi-wi-stats-overview-stat-label {
                        font-size: 11px !important;
                        font-weight: 500 !important;
                        color: #a1a1aa !important;
                        text-transform: uppercase !important;
                        letter-spacing: 0.05em !important;
                    }

                    .fi-wi-stats-overview-stat-value {
                        font-size: 24px !important;
                        font-weight: 600 !important;
                        letter-spacing: -0.02em !important;
                    }

                    html.dark .fi-wi-stats-overview-stat-value {
                        color: #f4f4f5 !important;
                    }

                    html:not(.dark) .fi-wi-stats-overview-stat-value {
                        color: #09090b !important;
                    }

                    html.dark .fi-header-heading,
                    html.dark .fi-section-header-heading,
                    html.dark .fi-ta-header-heading {
                        color: #f4f4f5 !important;
                    }

                    html.dark .fi-header-subheading,
                    html.dark .fi-section-header-description,
                    html.dark .fi-ta-header-description {
                        color: #71717a !important;
                    }

                    /* Linear Subtle Tables */
                    html.dark .fi-ta-header,
                    html.dark .fi-ta-content,
                    html.dark .fi-ta-filter,
                    html.dark .fi-ta-record-checkbox {
                        border-color: rgba(255, 255, 255, 0.07) !important;
                    }

                    html.dark .fi-ta-header-cell {
                        background: #121316 !important;
                        color: #a1a1aa !important;
                        font-size: 11px !important;
                        text-transform: uppercase !important;
                        letter-spacing: 0.05em !important;
                        font-weight: 500 !important;
                        border-bottom: 1px solid rgba(255, 255, 255, 0.07) !important;
                    }

                    html.dark .fi-ta-row:hover {
                        background-color: rgba(255, 255, 255, 0.02) !important;
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
                EnsureTenantSubscriptionIsActive::class,
            ], isPersistent: true)
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}

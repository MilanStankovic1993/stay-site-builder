<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\AdminDashboard;
use App\Filament\Resources\BillingSubscriptionResource;
use App\Filament\Resources\BillingTransactionResource;
use App\Filament\Pages\PlatformSettingsPage;
use App\Filament\Resources\AccommodationInquiryResource;
use App\Filament\Resources\AccommodationResource;
use App\Filament\Resources\AmenityResource;
use App\Filament\Resources\ThemePresetResource;
use App\Filament\Resources\UserResource;
use App\Filament\Widgets\AdminPendingApprovalsWidget;
use App\Filament\Widgets\AdminPlatformStatsOverviewWidget;
use App\Filament\Widgets\AdminThemeShowcaseWidget;
use App\Http\Middleware\SetLocale;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
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
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class SuperAdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->login()
            ->passwordReset()
            ->profile(EditProfile::class, isSimple: false)
            ->brandName('StaySite Builder Admin')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->plugin(FilamentShieldPlugin::make())
            ->resources([
                UserResource::class,
                BillingSubscriptionResource::class,
                BillingTransactionResource::class,
                AccommodationResource::class,
                AccommodationInquiryResource::class,
                AmenityResource::class,
                ThemePresetResource::class,
            ])
            ->pages([
                AdminDashboard::class,
                PlatformSettingsPage::class,
            ])
            ->widgets([
                AccountWidget::class,
                AdminPlatformStatsOverviewWidget::class,
                AdminPendingApprovalsWidget::class,
                AdminThemeShowcaseWidget::class,
            ])
            ->renderHook(PanelsRenderHook::HEAD_END, fn (): string => $this->renderPanelButtonStyles(
                accent: '#b45309',
                accentDark: '#92400e',
                accentSoft: 'rgba(180, 83, 9, 0.12)',
            ))
            ->renderHook(PanelsRenderHook::USER_MENU_PROFILE_AFTER, fn (): string => $this->renderPanelLocaleSwitcher())
            ->renderHook(PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, fn (): string => $this->renderPanelLocaleSwitcher())
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                SetLocale::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    protected function renderPanelLocaleSwitcher(): string
    {
        $currentLocale = app()->getLocale();
        $redirectUrl = request()->fullUrl();
        $srUrl = route('locale.switch', ['locale' => 'sr', 'redirect' => $redirectUrl]);
        $enUrl = route('locale.switch', ['locale' => 'en', 'redirect' => $redirectUrl]);

        return <<<HTML
            <details class="panel-locale-menu panel-locale-menu--inline">
                <summary>
                    <span class="panel-locale-menu__icon" aria-hidden="true"></span>
                    <span class="panel-locale-menu__value">{$this->displayLocale($currentLocale)}</span>
                </summary>
                <div class="panel-locale-menu__dropdown">
                    <a href="{$srUrl}" class="panel-locale-menu__item {$this->localeItemClass($currentLocale === 'sr')}">SR</a>
                    <a href="{$enUrl}" class="panel-locale-menu__item {$this->localeItemClass($currentLocale === 'en')}">EN</a>
                </div>
            </details>
        HTML;
    }

    protected function renderPanelButtonStyles(string $accent, string $accentDark, string $accentSoft): string
    {
        return <<<HTML
            <style>
                .fi-btn,
                .fi-ac-action,
                .fi-tabs-item-btn,
                .fi-link {
                    transition: transform 180ms ease, box-shadow 180ms ease, background-color 180ms ease, border-color 180ms ease !important;
                }

                .fi-topbar {
                    border-bottom: 1px solid rgba(226, 232, 240, 0.78);
                    background:
                        radial-gradient(circle at top right, rgba(251, 191, 36, 0.15), transparent 22%),
                        rgba(255, 255, 255, 0.86);
                    backdrop-filter: blur(18px);
                }

                .fi-topbar-ctn {
                    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
                }

                .fi-sidebar {
                    background:
                        radial-gradient(circle at top left, rgba(217, 119, 6, 0.12), transparent 24%),
                        linear-gradient(180deg, rgba(255, 251, 235, 0.98), rgba(255, 255, 255, 0.98));
                    border-right: 1px solid rgba(226, 232, 240, 0.82);
                }

                .fi-sidebar-header {
                    border-bottom: 1px solid rgba(226, 232, 240, 0.72);
                }

                .fi-sidebar-group-label {
                    letter-spacing: 0.02em;
                    font-weight: 700;
                }

                .fi-sidebar-item-btn {
                    border-radius: 1rem !important;
                    min-height: 2.9rem;
                    transition: background-color 180ms ease, color 180ms ease, transform 180ms ease, box-shadow 180ms ease !important;
                }

                .fi-sidebar-item-btn:hover {
                    transform: translateX(2px);
                    background: rgba(255, 255, 255, 0.96);
                    box-shadow: 0 12px 25px rgba(15, 23, 42, 0.05);
                }

                .fi-sidebar-item.fi-active .fi-sidebar-item-btn {
                    background: linear-gradient(135deg, rgba(251, 191, 36, 0.18), rgba(245, 158, 11, 0.12));
                    box-shadow: inset 0 0 0 1px rgba(180, 83, 9, 0.14);
                }

                .fi-page-header-main-ctn {
                    padding-bottom: 0.25rem;
                }

                .fi-btn {
                    border-radius: 999px !important;
                    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
                }

                .fi-btn:hover,
                .fi-ac-action:hover,
                .fi-tabs-item-btn:hover {
                    transform: translateY(-1px);
                }

                .fi-btn-color-primary {
                    box-shadow: 0 16px 32px {$accentSoft} !important;
                }

                .fi-btn-color-gray {
                    background: rgba(255, 255, 255, 0.92) !important;
                }

                .fi-section-header-heading,
                .fi-ta-header-heading {
                    letter-spacing: -0.02em;
                }

                .fi-ta-record-action-btn,
                .fi-ac-btn-action,
                .fi-icon-btn {
                    border-radius: 999px !important;
                }

                .fi-sidebar-nav .fi-sidebar-item-button:hover,
                .fi-topbar-open-sidebar-btn:hover {
                    color: {$accentDark} !important;
                }

                .fi-input-wrp,
                .fi-select-input,
                .fi-textarea {
                    border-radius: 1rem !important;
                }

                .fi-wi-stats-overview {
                    gap: 1rem !important;
                }

                .fi-wi-stats-overview-stat {
                    border-radius: 1.55rem !important;
                    border: 1px solid rgba(253, 230, 138, 0.75) !important;
                    background: linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(255, 251, 235, 0.96)) !important;
                    box-shadow: 0 20px 45px rgba(146, 64, 14, 0.08) !important;
                    padding: 1.25rem 1.35rem !important;
                }

                .fi-wi-stats-overview-stat-label {
                    font-weight: 700 !important;
                    color: #78350f !important;
                }

                .fi-wi-stats-overview-stat-value {
                    font-size: 2rem !important;
                    font-weight: 800 !important;
                    letter-spacing: -0.03em;
                    color: #111827 !important;
                }

                .fi-wi-stats-overview-stat-description {
                    line-height: 1.6 !important;
                    color: #92400e !important;
                }

                .fi-user-menu-trigger {
                    border-radius: 999px !important;
                    padding-inline: 0.75rem !important;
                    background: rgba(255, 251, 235, 0.92);
                    border: 1px solid rgba(253, 230, 138, 0.85);
                    box-shadow: 0 12px 28px rgba(146, 64, 14, 0.08);
                }

                .panel-locale-menu {
                    position: relative;
                }

                .panel-locale-menu summary {
                    list-style: none;
                    display: inline-flex;
                    align-items: center;
                    gap: 0.55rem;
                    padding: 0.55rem 0.85rem;
                    border: 1px solid rgba(226, 232, 240, 0.9);
                    border-radius: 999px;
                    background: rgba(255, 255, 255, 0.96);
                    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
                    cursor: pointer;
                    color: #475569;
                    font-size: 0.75rem;
                    font-weight: 700;
                    text-transform: uppercase;
                    letter-spacing: 0.18em;
                }

                .panel-locale-menu summary::-webkit-details-marker {
                    display: none;
                }

                .panel-locale-menu--inline summary {
                    width: 100%;
                    justify-content: space-between;
                    padding: 0.75rem 0.9rem;
                    border-radius: 1rem;
                    box-shadow: none;
                    background: rgba(255, 251, 235, 0.78);
                }

                .panel-locale-menu--inline .panel-locale-menu__dropdown {
                    left: 0;
                    right: auto;
                    min-width: 100%;
                }

                .panel-locale-menu__icon {
                    width: 0.6rem;
                    height: 0.6rem;
                    border-radius: 999px;
                    background: {$accent};
                    box-shadow: 0 0 0 4px rgba(180, 83, 9, 0.12);
                }

                .panel-locale-menu__value {
                    color: {$accentDark};
                }

                .panel-locale-menu__dropdown {
                    position: absolute;
                    right: 0;
                    top: calc(100% + 0.55rem);
                    min-width: 7rem;
                    padding: 0.45rem;
                    border: 1px solid rgba(226, 232, 240, 0.92);
                    border-radius: 1rem;
                    background: rgba(255, 255, 255, 0.98);
                    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
                    display: grid;
                    gap: 0.35rem;
                    z-index: 30;
                }

                .panel-locale-menu__item {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    border-radius: 999px;
                    padding: 0.65rem 0.8rem;
                    text-decoration: none;
                    font-size: 0.75rem;
                    font-weight: 700;
                    text-transform: uppercase;
                    letter-spacing: 0.16em;
                    color: #475569;
                }

                .panel-locale-menu__item:hover {
                    background: rgba(248, 250, 252, 1);
                }

                .panel-locale-menu__item.is-active {
                    background: {$accent};
                    color: white;
                }

                .dark .fi-topbar {
                    border-bottom-color: rgba(120, 53, 15, 0.42);
                    background:
                        radial-gradient(circle at top right, rgba(251, 191, 36, 0.16), transparent 22%),
                        rgba(18, 13, 7, 0.9);
                }

                .dark .fi-topbar-ctn {
                    box-shadow: 0 14px 40px rgba(2, 6, 23, 0.34);
                }

                .dark .fi-sidebar {
                    background:
                        radial-gradient(circle at top left, rgba(251, 191, 36, 0.12), transparent 24%),
                        linear-gradient(180deg, rgba(24, 16, 8, 0.98), rgba(15, 23, 42, 0.98));
                    border-right-color: rgba(120, 53, 15, 0.35);
                }

                .dark .fi-sidebar-header {
                    border-bottom-color: rgba(120, 53, 15, 0.28);
                }

                .dark .fi-sidebar-group-label {
                    color: #fcd34d;
                }

                .dark .fi-sidebar-item-btn:hover {
                    background: rgba(30, 23, 15, 0.94);
                    box-shadow: 0 16px 34px rgba(2, 6, 23, 0.28);
                }

                .dark .fi-sidebar-item.fi-active .fi-sidebar-item-btn {
                    background: linear-gradient(135deg, rgba(217, 119, 6, 0.28), rgba(251, 191, 36, 0.18));
                    box-shadow: inset 0 0 0 1px rgba(251, 191, 36, 0.22);
                }

                .dark .fi-btn-color-gray {
                    background: rgba(30, 23, 15, 0.92) !important;
                    border-color: rgba(120, 53, 15, 0.48) !important;
                    color: #f8fafc !important;
                }

                .dark .fi-section-header-heading,
                .dark .fi-ta-header-heading {
                    color: #fff7ed !important;
                }

                .dark .fi-sidebar-nav .fi-sidebar-item-button:hover,
                .dark .fi-topbar-open-sidebar-btn:hover {
                    color: #fcd34d !important;
                }

                .dark .fi-input-wrp,
                .dark .fi-select-input,
                .dark .fi-textarea {
                    background: rgba(30, 23, 15, 0.88) !important;
                    border-color: rgba(120, 53, 15, 0.42) !important;
                }

                .dark .fi-wi-stats-overview-stat {
                    border-color: rgba(251, 191, 36, 0.14) !important;
                    background:
                        radial-gradient(circle at top right, rgba(251, 191, 36, 0.12), transparent 30%),
                        rgba(30, 23, 15, 0.92) !important;
                    box-shadow: 0 22px 48px rgba(2, 6, 23, 0.34) !important;
                }

                .dark .fi-wi-stats-overview-stat-label {
                    color: #fcd34d !important;
                }

                .dark .fi-wi-stats-overview-stat-value {
                    color: #fff7ed !important;
                }

                .dark .fi-wi-stats-overview-stat-description {
                    color: #fed7aa !important;
                }

                .dark .fi-user-menu-trigger {
                    background: rgba(30, 23, 15, 0.94);
                    border-color: rgba(120, 53, 15, 0.45);
                    box-shadow: 0 14px 34px rgba(2, 6, 23, 0.3);
                }

                .dark .panel-locale-menu summary {
                    border-color: rgba(120, 53, 15, 0.45);
                    background: rgba(30, 23, 15, 0.96);
                    box-shadow: 0 14px 34px rgba(2, 6, 23, 0.28);
                    color: #fed7aa;
                }

                .dark .panel-locale-menu--inline summary {
                    background: rgba(41, 28, 15, 0.9);
                }

                .dark .panel-locale-menu__value {
                    color: #fcd34d;
                }

                .dark .panel-locale-menu__dropdown {
                    border-color: rgba(120, 53, 15, 0.42);
                    background: rgba(24, 16, 8, 0.98);
                    box-shadow: 0 20px 42px rgba(2, 6, 23, 0.38);
                }

                .dark .panel-locale-menu__item {
                    color: #fed7aa;
                }

                .dark .panel-locale-menu__item:hover {
                    background: rgba(68, 45, 18, 0.96);
                }
            </style>
        HTML;
    }

    protected function localeItemClass(bool $active): string
    {
        return $active ? 'is-active' : '';
    }

    protected function displayLocale(string $locale): string
    {
        return strtoupper($locale);
    }
}

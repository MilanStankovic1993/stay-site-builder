<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Auth\OwnerRegister;
use App\Http\Middleware\SetLocale;
use App\Filament\Pages\OwnerDashboard;
use App\Filament\Resources\AccommodationInquiryResource;
use App\Filament\Resources\AccommodationResource;
use App\Filament\Widgets\OwnerBuilderStepsWidget;
use App\Filament\Widgets\OwnerStatsOverviewWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
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

class OwnerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('dashboard')
            ->path('dashboard')
            ->login()
            ->registration(OwnerRegister::class)
            ->passwordReset()
            ->profile(EditProfile::class, isSimple: false)
            ->brandName('StaySite Builder')
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->resources([
                AccommodationResource::class,
                AccommodationInquiryResource::class,
            ])
            ->pages([
                OwnerDashboard::class,
            ])
            ->widgets([
                AccountWidget::class,
                OwnerStatsOverviewWidget::class,
                OwnerBuilderStepsWidget::class,
            ])
            ->renderHook(PanelsRenderHook::HEAD_END, fn (): string => $this->renderPanelButtonStyles(
                accent: '#0f766e',
                accentDark: '#115e59',
                accentSoft: 'rgba(15, 118, 110, 0.10)',
            ))
            ->renderHook(PanelsRenderHook::USER_MENU_PROFILE_AFTER, fn (): string => $this->renderPanelLocaleSwitcher())
            ->renderHook(PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, fn (): string => $this->renderPanelLocaleSwitcher())
            ->renderHook(PanelsRenderHook::AUTH_REGISTER_FORM_BEFORE, fn (): string => $this->renderPanelLocaleSwitcher())
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
                    background: rgba(255, 255, 255, 0.82);
                    backdrop-filter: blur(18px);
                }

                .fi-topbar-ctn {
                    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
                }

                .fi-sidebar {
                    background:
                        radial-gradient(circle at top left, rgba(15, 118, 110, 0.08), transparent 26%),
                        linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.98));
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
                    background: rgba(255, 255, 255, 0.94);
                    box-shadow: 0 12px 25px rgba(15, 23, 42, 0.05);
                }

                .fi-sidebar-item.fi-active .fi-sidebar-item-btn {
                    background: linear-gradient(135deg, rgba(15, 118, 110, 0.12), rgba(16, 185, 129, 0.10));
                    box-shadow: inset 0 0 0 1px rgba(15, 118, 110, 0.12);
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
                    border: 1px solid rgba(226, 232, 240, 0.92) !important;
                    background: rgba(255, 255, 255, 0.98) !important;
                    box-shadow: 0 20px 45px rgba(15, 23, 42, 0.07) !important;
                    padding: 1.25rem 1.35rem !important;
                }

                .fi-wi-stats-overview-stat-label {
                    font-weight: 700 !important;
                    color: #475569 !important;
                }

                .fi-wi-stats-overview-stat-value {
                    font-size: 2rem !important;
                    font-weight: 800 !important;
                    letter-spacing: -0.03em;
                    color: #0f172a !important;
                }

                .fi-wi-stats-overview-stat-description {
                    line-height: 1.6 !important;
                }

                .fi-user-menu-trigger {
                    border-radius: 999px !important;
                    padding-inline: 0.75rem !important;
                    background: rgba(255, 255, 255, 0.86);
                    border: 1px solid rgba(226, 232, 240, 0.9);
                    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
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
                    background: rgba(248, 250, 252, 0.92);
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
                    box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.12);
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
                    border-bottom-color: rgba(71, 85, 105, 0.45);
                    background:
                        radial-gradient(circle at top right, rgba(16, 185, 129, 0.16), transparent 24%),
                        rgba(9, 15, 20, 0.88);
                }

                .dark .fi-topbar-ctn {
                    box-shadow: 0 14px 40px rgba(2, 6, 23, 0.32);
                }

                .dark .fi-sidebar {
                    background:
                        radial-gradient(circle at top left, rgba(16, 185, 129, 0.16), transparent 24%),
                        linear-gradient(180deg, rgba(8, 15, 20, 0.98), rgba(15, 23, 42, 0.98));
                    border-right-color: rgba(71, 85, 105, 0.4);
                }

                .dark .fi-sidebar-header {
                    border-bottom-color: rgba(71, 85, 105, 0.38);
                }

                .dark .fi-sidebar-group-label {
                    color: #94a3b8;
                }

                .dark .fi-sidebar-item-btn:hover {
                    background: rgba(15, 23, 42, 0.92);
                    box-shadow: 0 16px 34px rgba(2, 6, 23, 0.28);
                }

                .dark .fi-sidebar-item.fi-active .fi-sidebar-item-btn {
                    background: linear-gradient(135deg, rgba(15, 118, 110, 0.32), rgba(16, 185, 129, 0.18));
                    box-shadow: inset 0 0 0 1px rgba(45, 212, 191, 0.22);
                }

                .dark .fi-btn-color-gray {
                    background: rgba(15, 23, 42, 0.9) !important;
                    border-color: rgba(71, 85, 105, 0.5) !important;
                    color: #e2e8f0 !important;
                }

                .dark .fi-section-header-heading,
                .dark .fi-ta-header-heading {
                    color: #f8fafc !important;
                }

                .dark .fi-sidebar-nav .fi-sidebar-item-button:hover,
                .dark .fi-topbar-open-sidebar-btn:hover {
                    color: #5eead4 !important;
                }

                .dark .fi-input-wrp,
                .dark .fi-select-input,
                .dark .fi-textarea {
                    background: rgba(15, 23, 42, 0.84) !important;
                    border-color: rgba(71, 85, 105, 0.45) !important;
                }

                .dark .fi-wi-stats-overview-stat {
                    border-color: rgba(45, 212, 191, 0.14) !important;
                    background:
                        radial-gradient(circle at top right, rgba(16, 185, 129, 0.12), transparent 30%),
                        rgba(15, 23, 42, 0.9) !important;
                    box-shadow: 0 22px 48px rgba(2, 6, 23, 0.34) !important;
                }

                .dark .fi-wi-stats-overview-stat-label {
                    color: #94a3b8 !important;
                }

                .dark .fi-wi-stats-overview-stat-value {
                    color: #f8fafc !important;
                }

                .dark .fi-wi-stats-overview-stat-description {
                    color: #cbd5e1 !important;
                }

                .dark .fi-user-menu-trigger {
                    background: rgba(15, 23, 42, 0.92);
                    border-color: rgba(71, 85, 105, 0.5);
                    box-shadow: 0 14px 34px rgba(2, 6, 23, 0.28);
                }

                .dark .panel-locale-menu summary {
                    border-color: rgba(71, 85, 105, 0.48);
                    background: rgba(15, 23, 42, 0.94);
                    box-shadow: 0 14px 34px rgba(2, 6, 23, 0.28);
                    color: #cbd5e1;
                }

                .dark .panel-locale-menu--inline summary {
                    background: rgba(15, 23, 42, 0.88);
                }

                .dark .panel-locale-menu__value {
                    color: #99f6e4;
                }

                .dark .panel-locale-menu__dropdown {
                    border-color: rgba(71, 85, 105, 0.45);
                    background: rgba(8, 15, 20, 0.98);
                    box-shadow: 0 20px 42px rgba(2, 6, 23, 0.38);
                }

                .dark .panel-locale-menu__item {
                    color: #cbd5e1;
                }

                .dark .panel-locale-menu__item:hover {
                    background: rgba(30, 41, 59, 0.96);
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

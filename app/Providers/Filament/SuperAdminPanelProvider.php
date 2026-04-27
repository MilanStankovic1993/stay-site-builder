<?php

namespace App\Providers\Filament;

use App\Filament\Pages\AdminDashboard;
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
            ->profile()
            ->brandName('StaySite Builder Admin')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->plugin(FilamentShieldPlugin::make())
            ->resources([
                UserResource::class,
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
            ->renderHook(PanelsRenderHook::TOPBAR_END, fn (): string => $this->renderPanelLocaleSwitcher())
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

        $srClass = $currentLocale === 'sr'
            ? 'rounded-full bg-amber-900 px-3 py-1.5 text-white shadow-sm'
            : 'rounded-full px-3 py-1.5 text-slate-500 transition hover:text-amber-900';
        $enClass = $currentLocale === 'en'
            ? 'rounded-full bg-amber-900 px-3 py-1.5 text-white shadow-sm'
            : 'rounded-full px-3 py-1.5 text-slate-500 transition hover:text-amber-900';

        return <<<HTML
            <div class="inline-flex items-center gap-2 rounded-full border border-stone-200 bg-white/95 px-2 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-slate-500 shadow-[0_10px_30px_rgba(15,23,42,0.08)] backdrop-blur">
                <span class="px-2 text-[10px] tracking-[0.22em] text-slate-400">Lang</span>
                <a href="{$srUrl}" class="{$srClass}">SR</a>
                <a href="{$enUrl}" class="{$enClass}">EN</a>
            </div>
        HTML;
    }
}

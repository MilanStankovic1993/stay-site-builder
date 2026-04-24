<?php

namespace App\Providers\Filament;

use App\Filament\Pages\PlatformSettingsPage;
use App\Filament\Resources\AccommodationInquiryResource;
use App\Filament\Resources\AccommodationResource;
use App\Filament\Resources\AmenityResource;
use App\Filament\Resources\ThemePresetResource;
use App\Filament\Resources\UserResource;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
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
                Dashboard::class,
                PlatformSettingsPage::class,
            ])
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
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
}

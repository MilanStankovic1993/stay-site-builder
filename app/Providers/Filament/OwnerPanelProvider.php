<?php

namespace App\Providers\Filament;

use App\Filament\Auth\OwnerRegister;
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
            ->profile()
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

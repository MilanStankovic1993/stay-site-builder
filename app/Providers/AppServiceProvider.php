<?php

namespace App\Providers;

use App\Http\Responses\Auth\PanelLoginResponse;
use App\Models\Accommodation;
use App\Models\AccommodationInquiry;
use App\Models\Amenity;
use App\Models\ThemePreset;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use App\Policies\AccommodationInquiryPolicy;
use App\Policies\AccommodationPolicy;
use App\Policies\AmenityPolicy;
use App\Policies\ThemePresetPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LoginResponse::class, PanelLoginResponse::class);
    }

    public function boot(): void
    {
        Gate::policy(Accommodation::class, AccommodationPolicy::class);
        Gate::policy(AccommodationInquiry::class, AccommodationInquiryPolicy::class);
        Gate::policy(Amenity::class, AmenityPolicy::class);
        Gate::policy(ThemePreset::class, ThemePresetPolicy::class);

        Gate::before(function ($user, string $ability): ?bool {
            if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
                return true;
            }

            return null;
        });

        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}

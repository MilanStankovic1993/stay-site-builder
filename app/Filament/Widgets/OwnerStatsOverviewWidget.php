<?php

namespace App\Filament\Widgets;

use App\Enums\AccommodationStatus;
use App\Filament\Resources\AccommodationInquiryResource;
use App\Filament\Resources\AccommodationResource;
use App\Models\Accommodation;
use App\Models\AccommodationInquiry;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OwnerStatsOverviewWidget extends StatsOverviewWidget
{
    protected function getHeading(): ?string
    {
        return app()->getLocale() === 'en'
            ? 'Status of your website builder account'
            : 'Status vaseg website builder naloga';
    }

    protected function getDescription(): ?string
    {
        return app()->getLocale() === 'en'
            ? 'The most important information about accommodation, publishing and direct inquiries.'
            : 'Najvaznije informacije o smestaju, objavi i direktnim upitima.';
    }

    protected function getStats(): array
    {
        $user = auth()->user();

        $accommodationsCount = Accommodation::query()
            ->where('user_id', $user->id)
            ->count();

        $publishedCount = Accommodation::query()
            ->where('user_id', $user->id)
            ->where('status', AccommodationStatus::Published)
            ->count();
        $canPublish = $user?->canPublishSites() ?? false;
        $hasBillingPlan = $user?->currentPublishingPlan() !== null;
        $siteLimit = $user?->publishingSiteLimit() ?? 0;

        $inquiriesCount = AccommodationInquiry::query()
            ->where('user_id', $user->id)
            ->count();

        $latestAccommodation = Accommodation::query()
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        return [
            Stat::make(app()->getLocale() === 'en' ? 'Accommodations' : 'Smestaji', $accommodationsCount)
                ->description($accommodationsCount
                    ? (app()->getLocale() === 'en' ? 'You can continue editing your website.' : 'Mozete da nastavite uredjivanje sajta.')
                    : (app()->getLocale() === 'en' ? 'Add your first accommodation.' : 'Dodajte prvi smestaj.'))
                ->descriptionIcon(Heroicon::OutlinedHomeModern)
                ->color(Color::Emerald)
                ->url(AccommodationResource::getUrl(panel: 'dashboard')),

            Stat::make(app()->getLocale() === 'en' ? 'Published websites' : 'Objavljeni sajtovi', $publishedCount)
                ->description($publishedCount
                    ? (app()->getLocale() === 'en'
                        ? "You are using {$publishedCount} of {$siteLimit} publishing slots."
                        : "Koristite {$publishedCount} od {$siteLimit} publish slotova.")
                    : ($canPublish
                        ? (app()->getLocale() === 'en' ? 'There is no publicly published website yet.' : 'Jos nema javno objavljenog sajta.')
                        : (app()->getLocale() === 'en' ? 'Activate billing to unlock publishing.' : 'Aktivirajte naplatu da otkljucate objavu.')))
                ->descriptionIcon(Heroicon::OutlinedGlobeAlt)
                ->color($publishedCount ? Color::Emerald : Color::Amber)
                ->url($hasBillingPlan
                    ? route('dashboard.billing')
                    : ($latestAccommodation?->status === AccommodationStatus::Published ? $latestAccommodation->publicUrl() : AccommodationResource::getUrl(panel: 'dashboard')), shouldOpenInNewTab: ! $hasBillingPlan && $publishedCount > 0),

            Stat::make(app()->getLocale() === 'en' ? 'Received inquiries' : 'Primljeni upiti', $inquiriesCount)
                ->description($inquiriesCount
                    ? (app()->getLocale() === 'en' ? 'You have new or existing inquiries.' : 'Imate nove ili postojece upite.')
                    : (app()->getLocale() === 'en' ? 'Inquiries will appear here.' : 'Upiti ce se pojaviti ovde.'))
                ->descriptionIcon(Heroicon::OutlinedInboxStack)
                ->color(Color::Amber)
                ->url(AccommodationInquiryResource::getUrl(panel: 'dashboard')),
        ];
    }
}

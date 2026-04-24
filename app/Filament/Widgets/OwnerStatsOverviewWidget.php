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
    protected ?string $heading = 'Status vaseg website builder naloga';

    protected ?string $description = 'Najvaznije informacije o smestaju, objavi i direktnim upitima.';

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

        $inquiriesCount = AccommodationInquiry::query()
            ->where('user_id', $user->id)
            ->count();

        $latestAccommodation = Accommodation::query()
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        return [
            Stat::make('Smestaji', $accommodationsCount)
                ->description($accommodationsCount ? 'Mozete da nastavite uredjivanje sajta.' : 'Dodajte prvi smestaj.')
                ->descriptionIcon(Heroicon::OutlinedHomeModern)
                ->color(Color::Emerald)
                ->url(AccommodationResource::getUrl(panel: 'dashboard')),

            Stat::make('Objavljeni sajtovi', $publishedCount)
                ->description($publishedCount ? 'Bar jedan sajt je javno dostupan.' : 'Jos nema javno objavljenog sajta.')
                ->descriptionIcon(Heroicon::OutlinedGlobeAlt)
                ->color($publishedCount ? Color::Emerald : Color::Amber)
                ->url($latestAccommodation?->status === AccommodationStatus::Published ? $latestAccommodation->publicUrl() : AccommodationResource::getUrl(panel: 'dashboard'), shouldOpenInNewTab: $publishedCount > 0),

            Stat::make('Primljeni upiti', $inquiriesCount)
                ->description($inquiriesCount ? 'Imate nove ili postojece upite.' : 'Upiti ce se pojaviti ovde.')
                ->descriptionIcon(Heroicon::OutlinedInboxStack)
                ->color(Color::Amber)
                ->url(AccommodationInquiryResource::getUrl(panel: 'dashboard')),
        ];
    }
}

<?php

namespace App\Filament\Widgets;

use App\Enums\AccommodationStatus;
use App\Filament\Resources\AccommodationInquiryResource;
use App\Filament\Resources\AccommodationResource;
use App\Filament\Resources\UserResource;
use App\Models\Accommodation;
use App\Models\AccommodationInquiry;
use App\Models\User;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminPlatformStatsOverviewWidget extends StatsOverviewWidget
{
    protected function getHeading(): ?string
    {
        return __('admin.stats.heading');
    }

    protected function getDescription(): ?string
    {
        return __('admin.stats.description');
    }

    protected function getStats(): array
    {
        $usersCount = User::query()->count();
        $pendingUsersCount = User::query()->where('is_active', false)->count();
        $publishedSitesCount = Accommodation::query()->where('status', AccommodationStatus::Published)->count();
        $inquiriesCount = AccommodationInquiry::query()->count();

        return [
            Stat::make(__('admin.stats.users'), $usersCount)
                ->description(__('admin.stats.users_text'))
                ->descriptionIcon(Heroicon::OutlinedUsers)
                ->color(Color::Slate)
                ->url(UserResource::getUrl(panel: 'admin')),

            Stat::make(__('admin.stats.pending'), $pendingUsersCount)
                ->description($pendingUsersCount ? __('admin.stats.pending_waiting') : __('admin.stats.pending_clear'))
                ->descriptionIcon(Heroicon::OutlinedClock)
                ->color($pendingUsersCount ? Color::Amber : Color::Emerald)
                ->url(UserResource::getUrl(panel: 'admin')),

            Stat::make(__('admin.stats.published'), $publishedSitesCount)
                ->description(__('admin.stats.published_text'))
                ->descriptionIcon(Heroicon::OutlinedGlobeAlt)
                ->color(Color::Emerald)
                ->url(AccommodationResource::getUrl(panel: 'admin')),

            Stat::make(__('admin.stats.inquiries'), $inquiriesCount)
                ->description(__('admin.stats.inquiries_text'))
                ->descriptionIcon(Heroicon::OutlinedInboxStack)
                ->color(Color::Amber)
                ->url(AccommodationInquiryResource::getUrl(panel: 'admin')),
        ];
    }
}

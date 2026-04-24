<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\InteractsWithPanelContext;
use App\Filament\Widgets\OwnerBuilderStepsWidget;
use App\Filament\Widgets\OwnerStatsOverviewWidget;
use Filament\Pages\Dashboard;
use Illuminate\Contracts\Support\Htmlable;

class OwnerDashboard extends Dashboard
{
    use InteractsWithPanelContext;

    protected static ?string $title = 'Moj sajt';

    protected static ?string $navigationLabel = 'Moj sajt';

    public static function canAccess(): bool
    {
        return static::isOwnerPanel() && (auth()->user()?->canAccessPanel(filament()->getCurrentPanel()) ?? false);
    }

    public function getTitle(): string | Htmlable
    {
        return 'Izgradi svoj sajt';
    }

    public function getColumns(): int | array
    {
        return 1;
    }

    public function getWidgets(): array
    {
        return [
            OwnerStatsOverviewWidget::class,
            OwnerBuilderStepsWidget::class,
        ];
    }
}

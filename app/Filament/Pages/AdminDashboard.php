<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AdminPendingApprovalsWidget;
use App\Filament\Widgets\AdminPlatformStatsOverviewWidget;
use App\Filament\Widgets\AdminThemeShowcaseWidget;
use Filament\Pages\Dashboard;
use Illuminate\Contracts\Support\Htmlable;

class AdminDashboard extends Dashboard
{
    public function getTitle(): string | Htmlable
    {
        return __('admin.dashboard.admin_title');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.nav.dashboard_admin');
    }

    public function getColumns(): int | array
    {
        return 1;
    }

    public function getWidgets(): array
    {
        return [
            AdminPlatformStatsOverviewWidget::class,
            AdminPendingApprovalsWidget::class,
            AdminThemeShowcaseWidget::class,
        ];
    }
}

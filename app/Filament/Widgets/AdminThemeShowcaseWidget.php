<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ThemePresetResource;
use App\Models\ThemePreset;
use Filament\Widgets\Widget;

class AdminThemeShowcaseWidget extends Widget
{
    protected string $view = 'filament.widgets.admin-theme-showcase';

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'themes' => ThemePreset::query()->orderBy('name')->get(),
            'themesUrl' => ThemePresetResource::getUrl(panel: 'admin'),
        ];
    }
}

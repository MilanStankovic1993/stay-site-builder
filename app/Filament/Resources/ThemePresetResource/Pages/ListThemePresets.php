<?php

namespace App\Filament\Resources\ThemePresetResource\Pages;

use App\Filament\Resources\ThemePresetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListThemePresets extends ListRecords
{
    protected static string $resource = ThemePresetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Nova tema'),
        ];
    }
}

<?php

namespace App\Filament\Resources\ThemePresetResource\Pages;

use App\Filament\Resources\ThemePresetResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditThemePreset extends EditRecord
{
    protected static string $resource = ThemePresetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label('Obrisi'),
        ];
    }
}

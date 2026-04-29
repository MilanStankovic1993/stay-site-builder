<?php

namespace App\Filament\Resources\AccommodationResource\Pages;

use App\Filament\Resources\AccommodationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAccommodations extends ListRecords
{
    protected static string $resource = AccommodationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label(__('admin.accommodations.add')),
        ];
    }
}

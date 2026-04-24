<?php

namespace App\Filament\Resources\AccommodationResource\Pages;

use App\Enums\AccommodationStatus;
use App\Filament\Resources\AccommodationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAccommodation extends CreateRecord
{
    protected static string $resource = AccommodationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! auth()->user()?->isSuperAdmin()) {
            $data['user_id'] = auth()->id();
        }

        if (($data['status'] ?? null) === AccommodationStatus::Published->value) {
            $data['published_at'] = now();
        }

        return $data;
    }
}

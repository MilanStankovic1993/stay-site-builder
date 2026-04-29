<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['manual_billing_activated_at'] = filled($data['manual_billing_plan_key'] ?? null)
            ? now()
            : null;

        return $data;
    }
}

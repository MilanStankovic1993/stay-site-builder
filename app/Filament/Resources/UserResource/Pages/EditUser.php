<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $newPlan = $data['manual_billing_plan_key'] ?? null;
        $currentPlan = $this->getRecord()->manual_billing_plan_key;

        if (filled($newPlan) && $newPlan !== $currentPlan) {
            $data['manual_billing_activated_at'] = now();
        }

        if (blank($newPlan)) {
            $data['manual_billing_activated_at'] = null;
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label('Obrisi'),
        ];
    }
}

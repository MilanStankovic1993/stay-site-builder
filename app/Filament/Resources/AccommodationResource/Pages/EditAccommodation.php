<?php

namespace App\Filament\Resources\AccommodationResource\Pages;

use App\Enums\AccommodationStatus;
use App\Filament\Concerns\InteractsWithPanelContext;
use App\Filament\Resources\AccommodationResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditAccommodation extends EditRecord
{
    use InteractsWithPanelContext;

    protected static string $resource = AccommodationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('Preview sajta')
                ->icon(Heroicon::OutlinedEye)
                ->url(fn (): string => $this->getRecord()->previewUrl(), shouldOpenInNewTab: true),
            Action::make('publish')
                ->label(static::isOwnerPanel() ? 'Build my site' : 'Objavi')
                ->color('success')
                ->visible(fn (): bool => $this->getRecord()->status !== AccommodationStatus::Published)
                ->action(function (): void {
                    $this->getRecord()->update([
                        'status' => AccommodationStatus::Published,
                        'published_at' => now(),
                    ]);

                    $this->refreshFormData(['status', 'published_at']);
                }),
            Action::make('unpublish')
                ->label(static::isOwnerPanel() ? 'Sakrij sajt' : 'Povuci objavu')
                ->color('gray')
                ->visible(fn (): bool => $this->getRecord()->status === AccommodationStatus::Published)
                ->action(function (): void {
                    $this->getRecord()->update([
                        'status' => AccommodationStatus::Draft,
                        'published_at' => null,
                    ]);

                    $this->refreshFormData(['status', 'published_at']);
                }),
            DeleteAction::make()->label('Obrisi'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! auth()->user()?->isSuperAdmin()) {
            $data['user_id'] = $this->getRecord()->user_id;
            $data['status'] = $this->getRecord()->status->value;
        }

        $data['published_at'] = ($data['status'] ?? null) === AccommodationStatus::Published->value
            ? ($this->getRecord()->published_at ?? now())
            : null;

        return $data;
    }
}

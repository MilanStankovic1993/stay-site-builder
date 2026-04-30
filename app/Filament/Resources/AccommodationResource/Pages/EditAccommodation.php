<?php

namespace App\Filament\Resources\AccommodationResource\Pages;

use App\Actions\PublishAccommodation;
use App\Enums\AccommodationStatus;
use App\Filament\Concerns\InteractsWithPanelContext;
use App\Filament\Resources\AccommodationResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
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
                ->label(__('admin.accommodations.preview_site'))
                ->icon(Heroicon::OutlinedEye)
                ->url(fn (): string => $this->getRecord()->previewUrl(), shouldOpenInNewTab: true),
            Action::make('publish')
                ->label(static::isOwnerPanel() ? __('admin.accommodations.build_site') : __('admin.accommodations.publish'))
                ->color('success')
                ->visible(fn (): bool => $this->getRecord()->status !== AccommodationStatus::Published)
                ->disabled(fn (): bool => static::isOwnerPanel() && ! (auth()->user()?->hasAvailablePublishingSlot($this->getRecord()) ?? false))
                ->tooltip(fn (): ?string => static::isOwnerPanel() && ! (auth()->user()?->hasAvailablePublishingSlot($this->getRecord()) ?? false)
                    ? __('admin.accommodations.publish_locked_tooltip')
                    : null)
                ->action(function (): void {
                    $result = app(PublishAccommodation::class)->handle(
                        user: auth()->user(),
                        accommodation: $this->getRecord(),
                        enforceBillingRules: static::isOwnerPanel(),
                    );

                    if ($result === PublishAccommodation::RESULT_LOCKED) {
                        Notification::make()
                            ->title(__('admin.accommodations.publish_locked_title'))
                            ->body(__('admin.accommodations.publish_locked_body'))
                            ->warning()
                            ->send();

                        return;
                    }

                    if ($result === PublishAccommodation::RESULT_BILLING_FAILED) {
                        Notification::make()
                            ->title(__('admin.accommodations.publish_charge_failed_title'))
                            ->body(__('admin.accommodations.publish_charge_failed_body'))
                            ->danger()
                            ->send();

                        return;
                    }

                    $this->refreshFormData(['status', 'published_at']);
                }),
            Action::make('upgrade_plan')
                ->label(__('admin.accommodations.upgrade_plan'))
                ->icon(Heroicon::OutlinedRocketLaunch)
                ->color('warning')
                ->visible(fn (): bool => static::isOwnerPanel()
                    && $this->getRecord()->status !== AccommodationStatus::Published
                    && (auth()->user()?->hasReachedPublishingLimit($this->getRecord()) ?? false))
                ->url(fn (): string => route('dashboard.billing')),
            Action::make('unpublish')
                ->label(static::isOwnerPanel() ? __('admin.accommodations.hide_site') : __('admin.accommodations.unpublish'))
                ->color('gray')
                ->visible(fn (): bool => $this->getRecord()->status === AccommodationStatus::Published)
                ->action(function (): void {
                    $this->getRecord()->update([
                        'status' => AccommodationStatus::Draft,
                        'published_at' => null,
                    ]);

                    $this->refreshFormData(['status', 'published_at']);
                }),
            DeleteAction::make()->label(__('admin.accommodations.delete')),
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

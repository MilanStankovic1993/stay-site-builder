<?php

namespace App\Filament\Widgets;

use App\Enums\AccommodationStatus;
use App\Filament\Resources\AccommodationInquiryResource;
use App\Filament\Resources\AccommodationResource;
use App\Models\Accommodation;
use Filament\Widgets\Widget;

class OwnerBuilderStepsWidget extends Widget
{
    protected string $view = 'filament.widgets.owner-builder-steps';

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $user = auth()->user();

        $accommodation = Accommodation::query()
            ->with(['media', 'amenities'])
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        $hasAccommodation = (bool) $accommodation;
        $hasContent = $hasAccommodation && filled($accommodation->title) && filled($accommodation->short_description);
        $hasImages = $hasAccommodation && (filled($accommodation->getFirstMediaUrl('hero')) || $accommodation->getMedia('gallery')->count() > 0);
        $hasContact = $hasAccommodation && (filled($accommodation->contact_phone) || filled($accommodation->contact_email));
        $isPublished = $hasAccommodation && $accommodation->status === AccommodationStatus::Published;
        $canPublish = $user?->canPublishSites() ?? false;

        return [
            'accommodation' => $accommodation,
            'user' => $user,
            'canPublish' => $canPublish,
            'steps' => [
                [
                    'title' => __('admin.builder.step_1_title'),
                    'description' => __('admin.builder.step_1_text'),
                    'done' => $hasAccommodation,
                ],
                [
                    'title' => __('admin.builder.step_2_title'),
                    'description' => __('admin.builder.step_2_text'),
                    'done' => $hasContent && $hasImages,
                ],
                [
                    'title' => __('admin.builder.step_3_title'),
                    'description' => __('admin.builder.step_3_text'),
                    'done' => $hasContact,
                ],
                [
                    'title' => __('admin.builder.step_4_title'),
                    'description' => $canPublish
                        ? __('admin.builder.step_4_ready')
                        : __('admin.builder.step_4_waiting'),
                    'done' => $isPublished,
                ],
            ],
            'createUrl' => AccommodationResource::getUrl('create', panel: 'dashboard'),
            'manageUrl' => AccommodationResource::getUrl(panel: 'dashboard'),
            'inquiriesUrl' => AccommodationInquiryResource::getUrl(panel: 'dashboard'),
        ];
    }
}

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
        $hasAvailableSlot = $user?->hasAvailablePublishingSlot($accommodation) ?? false;
        $hasReachedLimit = $user?->hasReachedPublishingLimit($accommodation) ?? false;
        $hasPublishingSubscription = $user?->hasPublishingSubscription() ?? false;
        $hasBillingPlan = $user?->currentPublishingPlan() !== null;
        $siteLimit = $user?->publishingSiteLimit() ?? 0;
        $publishedSitesCount = $user?->publishedSitesCount() ?? 0;
        $billingCtaLabel = $hasReachedLimit
            ? __('admin.billing.upgrade_cta')
            : __('admin.billing.activate_cta');
        $accommodationStatusText = $accommodation
                ? ($accommodation->status === AccommodationStatus::Published
                    ? __('admin.builder.published_text')
                    : ($hasAvailableSlot
                        ? __('admin.builder.draft_ready_text')
                        : ($hasReachedLimit
                            ? __('admin.builder.publish_limit_text')
                            : __('admin.builder.draft_waiting_text'))))
            : null;

        return [
            'accommodation' => $accommodation,
            'user' => $user,
            'canPublish' => $canPublish,
            'hasAvailableSlot' => $hasAvailableSlot,
            'hasReachedLimit' => $hasReachedLimit,
            'hasPublishingSubscription' => $hasPublishingSubscription,
            'hasBillingPlan' => $hasBillingPlan,
            'siteLimit' => $siteLimit,
            'publishedSitesCount' => $publishedSitesCount,
            'billingCtaLabel' => $billingCtaLabel,
            'accommodationStatusText' => $accommodationStatusText,
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
                    'description' => $hasAvailableSlot
                        ? __('admin.builder.step_4_ready')
                        : __('admin.builder.step_4_waiting'),
                    'done' => $isPublished,
                ],
            ],
            'billingUrl' => route('dashboard.billing'),
            'createUrl' => AccommodationResource::getUrl('create', panel: 'dashboard'),
            'manageUrl' => AccommodationResource::getUrl(panel: 'dashboard'),
            'inquiriesUrl' => AccommodationInquiryResource::getUrl(panel: 'dashboard'),
        ];
    }
}

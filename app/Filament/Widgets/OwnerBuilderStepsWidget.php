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
                    'title' => 'Dodajte smestaj',
                    'description' => 'Unesite osnovne informacije o objektu koji zelite da predstavite.',
                    'done' => $hasAccommodation,
                ],
                [
                    'title' => 'Popunite sadrzaj i slike',
                    'description' => 'Opis, galerija, lokacija i sadrzaji cine sajt profesionalnim.',
                    'done' => $hasContent && $hasImages,
                ],
                [
                    'title' => 'Dodajte kontakt podatke',
                    'description' => 'Telefon, email i WhatsApp omogucavaju direktne upite.',
                    'done' => $hasContact,
                ],
                [
                    'title' => 'Preview i objava',
                    'description' => $canPublish
                        ? 'Pregledajte izgled i kliknite Build my site kada ste spremni.'
                        : 'Mozete pripremiti sajt, ali objava ceka odobrenje super admina nakon uplate.',
                    'done' => $isPublished,
                ],
            ],
            'createUrl' => AccommodationResource::getUrl('create', panel: 'dashboard'),
            'manageUrl' => AccommodationResource::getUrl(panel: 'dashboard'),
            'inquiriesUrl' => AccommodationInquiryResource::getUrl(panel: 'dashboard'),
        ];
    }
}

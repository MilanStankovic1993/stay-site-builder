<?php

namespace App\Http\Controllers\Storefront;

use App\Enums\AccommodationStatus;
use App\Enums\InquirySource;
use App\Enums\InquiryStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAccommodationInquiryRequest;
use App\Models\Accommodation;
use App\Settings\PlatformSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AccommodationController extends Controller
{
    protected const DEMO_THEMES = [
        'default' => [
            'title' => 'Villa Lavanda Tara',
            'location_name' => 'Planina Tara',
            'short_description' => 'Elegantna prezentacija za smestaje koji prodaju atmosferu, fotografije i direktan kontakt.',
            'description' => 'Default tema je namenjena vlasnicima koji zele cist, premium i profesionalan nastup bez komplikacija. Velika hero fotografija, jasna struktura i direktan upit daju sajtu ozbiljan hotelski utisak, a i dalje ostaje jednostavan za upravljanje.',
            'primary_color' => '#244338',
            'secondary_color' => '#c7a669',
        ],
        'luxury' => [
            'title' => 'Villa Aurelia Resort',
            'location_name' => 'Kosmaj Hills',
            'short_description' => 'Premium tema za vile, ekskluzivne kuce za odmor i smestaje sa jacim vizuelnim identitetom.',
            'description' => 'Luxury tema stavlja akcenat na ekskluzivnost, cist ritam sekcija i bogatiji editorial izgled. Idealna je za vlasnike koji zele da njihov smestaj odmah deluje skuplje, elegantnije i vise boutique.',
            'primary_color' => '#16261f',
            'secondary_color' => '#d8b06a',
        ],
        'nature' => [
            'title' => 'Brvnara Zelenika',
            'location_name' => 'Golija Forest Retreat',
            'short_description' => 'Topla i moderna tema za vikendice, brvnare i objekte u prirodi.',
            'description' => 'Nature tema kombinije toplu atmosferu, prirodne tonove i opusten raspored sekcija. Napravljena je za vlasnike koji prodaju mir, prirodu, vikend beg i autenticni dozivljaj destinacije.',
            'primary_color' => '#365446',
            'secondary_color' => '#b98b4d',
        ],
    ];

    public function show(string $slug, PlatformSettings $settings): View
    {
        $accommodation = Accommodation::query()
            ->with(['amenities', 'media'])
            ->where('slug', $slug)
            ->where('status', AccommodationStatus::Published)
            ->firstOrFail();

        return $this->renderStorefront($accommodation, $settings, false);
    }

    public function preview(Accommodation $accommodation, PlatformSettings $settings): View
    {
        abort_unless(request()->hasValidSignature(), 403);

        $accommodation->loadMissing(['amenities', 'media']);

        return $this->renderStorefront($accommodation, $settings, true);
    }

    public function demoTheme(string $theme, PlatformSettings $settings): View
    {
        abort_unless(array_key_exists($theme, self::DEMO_THEMES), 404);

        $accommodation = Accommodation::query()
            ->with(['amenities', 'media'])
            ->published()
            ->firstOrFail();

        $demoAccommodation = $this->buildDemoAccommodation($accommodation, $theme);

        return $this->renderStorefront($demoAccommodation, $settings, true, [
            'isThemeDemo' => true,
            'selectedTheme' => $theme,
        ]);
    }

    public function storeInquiry(StoreAccommodationInquiryRequest $request, string $slug): RedirectResponse
    {
        $accommodation = Accommodation::query()
            ->where('slug', $slug)
            ->where('status', AccommodationStatus::Published)
            ->firstOrFail();

        $accommodation->inquiries()->create([
            ...$request->validated(),
            'user_id' => $accommodation->user_id,
            'status' => InquiryStatus::New,
            'source' => InquirySource::Website,
        ]);

        return redirect()
            ->route('storefront.show', $accommodation->slug)
            ->with('status', 'Upit je uspesno poslat.');
    }

    protected function renderStorefront(
        Accommodation $accommodation,
        PlatformSettings $settings,
        bool $isPreview,
        array $extra = [],
    ): View
    {
        return view($accommodation->themeView(), [
            'accommodation' => $accommodation,
            'settings' => $settings,
            'isPreview' => $isPreview,
            'metaTitle' => $accommodation->meta_title ?: $settings->default_meta_title,
            'metaDescription' => $accommodation->meta_description ?: $settings->default_meta_description,
            ...$extra,
        ]);
    }

    protected function buildDemoAccommodation(Accommodation $accommodation, string $theme): Accommodation
    {
        $themeData = self::DEMO_THEMES[$theme];

        $demoAccommodation = $accommodation->replicate();
        $demoAccommodation->slug = $accommodation->slug;
        $demoAccommodation->theme_key = $theme;
        $demoAccommodation->title = $themeData['title'];
        $demoAccommodation->location_name = $themeData['location_name'];
        $demoAccommodation->short_description = $themeData['short_description'];
        $demoAccommodation->description = $themeData['description'];
        $demoAccommodation->primary_color = $themeData['primary_color'];
        $demoAccommodation->secondary_color = $themeData['secondary_color'];
        $demoAccommodation->meta_title = $themeData['title'].' | Demo tema';
        $demoAccommodation->meta_description = $themeData['short_description'];

        $demoAccommodation->setRelation('amenities', $accommodation->amenities);
        $demoAccommodation->setRelation('media', $accommodation->media);

        return $demoAccommodation;
    }
}

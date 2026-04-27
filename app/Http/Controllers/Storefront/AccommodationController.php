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
            'sr' => [
                'title' => 'Villa Lavanda Tara',
                'location_name' => 'Planina Tara',
                'short_description' => 'Elegantna prezentacija za smestaje koji prodaju atmosferu, fotografije i direktan kontakt.',
                'description' => 'Default tema je namenjena vlasnicima koji zele cist, premium i profesionalan nastup bez komplikacija. Velika hero fotografija, jasna struktura i direktan upit daju sajtu ozbiljan hotelski utisak, a i dalje ostaje jednostavan za upravljanje.',
            ],
            'en' => [
                'title' => 'Lavanda Tara Villa',
                'location_name' => 'Tara Mountain',
                'short_description' => 'An elegant presentation for stays that sell atmosphere, photography and direct contact.',
                'description' => 'The Default theme is made for owners who want a clean, premium and professional presence without complexity. A large hero image, clear structure and direct inquiry flow give the website a polished hospitality feel while keeping it easy to manage.',
            ],
            'primary_color' => '#244338',
            'secondary_color' => '#c7a669',
        ],
        'luxury' => [
            'sr' => [
                'title' => 'Villa Aurelia Resort',
                'location_name' => 'Kosmaj Hills',
                'short_description' => 'Premium tema za vile, ekskluzivne kuce za odmor i smestaje sa jacim vizuelnim identitetom.',
                'description' => 'Luxury tema stavlja akcenat na ekskluzivnost, cist ritam sekcija i bogatiji editorial izgled. Idealna je za vlasnike koji zele da njihov smestaj odmah deluje skuplje, elegantnije i vise boutique.',
            ],
            'en' => [
                'title' => 'Aurelia Villa Resort',
                'location_name' => 'Kosmaj Hills',
                'short_description' => 'A premium theme for villas, exclusive holiday homes and stays that need a stronger visual identity.',
                'description' => 'The Luxury theme focuses on exclusivity, refined section rhythm and a richer editorial feel. It is ideal for owners who want their accommodation to look more valuable, elegant and boutique from the first second.',
            ],
            'primary_color' => '#16261f',
            'secondary_color' => '#d8b06a',
        ],
        'nature' => [
            'sr' => [
                'title' => 'Brvnara Zelenika',
                'location_name' => 'Golija Forest Retreat',
                'short_description' => 'Topla i moderna tema za vikendice, brvnare i objekte u prirodi.',
                'description' => 'Nature tema kombinije toplu atmosferu, prirodne tonove i opusten raspored sekcija. Napravljena je za vlasnike koji prodaju mir, prirodu, vikend beg i autenticni dozivljaj destinacije.',
            ],
            'en' => [
                'title' => 'Zelenika Cabin',
                'location_name' => 'Golija Forest Retreat',
                'short_description' => 'A warm and modern theme for cabins, cottages and properties surrounded by nature.',
                'description' => 'The Nature theme blends warmth, natural tones and an easy-flowing layout. It is built for owners who sell calm, nature, weekend escape and the authentic feeling of the destination.',
            ],
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
            ->with('status', __('site.storefront.inquiry_success'));
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
            'metaTitle' => $accommodation->display_meta_title ?: $settings->default_meta_title,
            'metaDescription' => $accommodation->display_meta_description ?: $settings->default_meta_description,
            ...$extra,
        ]);
    }

    protected function buildDemoAccommodation(Accommodation $accommodation, string $theme): Accommodation
    {
        $locale = app()->getLocale();
        $themeData = self::DEMO_THEMES[$theme];
        $localizedThemeData = $themeData[$locale] ?? $themeData['sr'];

        $demoAccommodation = $accommodation->replicate();
        $demoAccommodation->slug = $accommodation->slug;
        $demoAccommodation->theme_key = $theme;
        $demoAccommodation->title = $localizedThemeData['title'];
        $demoAccommodation->location_name = $localizedThemeData['location_name'];
        $demoAccommodation->short_description = $localizedThemeData['short_description'];
        $demoAccommodation->description = $localizedThemeData['description'];
        $demoAccommodation->primary_color = $themeData['primary_color'];
        $demoAccommodation->secondary_color = $themeData['secondary_color'];
        $demoAccommodation->meta_title = $localizedThemeData['title'].' | '.(__('site.nav.theme_preview'));
        $demoAccommodation->meta_description = $localizedThemeData['short_description'];

        $demoAccommodation->setRelation('amenities', $accommodation->amenities);
        $demoAccommodation->setRelation('media', $accommodation->media);

        return $demoAccommodation;
    }
}

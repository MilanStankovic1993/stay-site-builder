<?php

namespace Tests\Feature;

use App\Models\Accommodation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccommodationLocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_english_content_is_used_when_locale_is_en(): void
    {
        app()->setLocale('en');

        $accommodation = new Accommodation([
            'title' => 'Brvnara Jelka',
            'title_en' => 'Jelka Cabin',
            'short_description' => 'Srpski opis',
            'short_description_en' => 'English description',
            'city' => 'Kopaonik',
            'city_en' => 'Kopaonik',
            'meta_title' => 'SR meta',
            'meta_title_en' => 'EN meta',
        ]);

        $this->assertSame('Jelka Cabin', $accommodation->display_title);
        $this->assertSame('English description', $accommodation->display_short_description);
        $this->assertSame('EN meta', $accommodation->display_meta_title);
    }

    public function test_serbian_content_is_used_as_fallback(): void
    {
        app()->setLocale('en');

        $accommodation = new Accommodation([
            'title' => 'Vila Tara',
            'short_description' => 'Opis na srpskom',
            'city' => 'Bajina Basta',
        ]);

        $this->assertSame('Vila Tara', $accommodation->display_title);
        $this->assertSame('Opis na srpskom', $accommodation->display_short_description);
        $this->assertSame('Bajina Basta', $accommodation->display_city);
    }
}

<?php

namespace Tests\Feature;

use App\Enums\AccommodationStatus;
use App\Enums\AccommodationType;
use App\Models\Accommodation;
use App\Models\AccommodationInquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('StaySite Builder');
    }

    public function test_published_accommodation_is_visible_on_storefront(): void
    {
        $owner = User::factory()->create();

        $accommodation = Accommodation::query()->create([
            'user_id' => $owner->id,
            'title' => 'Brvnara Jelka',
            'slug' => 'brvnara-jelka',
            'type' => AccommodationType::Cabin,
            'status' => AccommodationStatus::Published,
        ]);

        $this->get("/s/{$accommodation->slug}")
            ->assertOk()
            ->assertSee('Brvnara Jelka');
    }

    public function test_inquiry_can_be_submitted_for_published_accommodation(): void
    {
        $owner = User::factory()->create();

        $accommodation = Accommodation::query()->create([
            'user_id' => $owner->id,
            'title' => 'Apartman Morava',
            'slug' => 'apartman-morava',
            'type' => AccommodationType::Apartment,
            'status' => AccommodationStatus::Published,
            'contact_email' => 'owner@example.com',
        ]);

        $this->post("/s/{$accommodation->slug}/inquiry", [
            'guest_name' => 'Marko Markovic',
            'guest_email' => 'marko@example.com',
            'guest_phone' => '+38160123456',
            'message' => 'Zanima me vikend termin za dve osobe.',
            'guests_count' => 2,
        ])->assertRedirect("/s/{$accommodation->slug}");

        $this->assertDatabaseCount(AccommodationInquiry::class, 1);
        $this->assertDatabaseHas(AccommodationInquiry::class, [
            'accommodation_id' => $accommodation->id,
            'guest_name' => 'Marko Markovic',
            'guest_email' => 'marko@example.com',
        ]);
    }

    public function test_signed_preview_route_is_available_for_draft_site(): void
    {
        $owner = User::factory()->create();

        $accommodation = Accommodation::query()->create([
            'user_id' => $owner->id,
            'title' => 'Vikendica Vista',
            'slug' => 'vikendica-vista',
            'type' => AccommodationType::House,
            'status' => AccommodationStatus::Draft,
        ]);

        $previewUrl = URL::temporarySignedRoute('storefront.preview', now()->addHour(), [
            'accommodation' => $accommodation->slug,
        ]);

        $this->get($previewUrl)
            ->assertOk()
            ->assertSee('Preview rezim')
            ->assertSee('Vikendica Vista');
    }

    public function test_selected_theme_renders_matching_storefront_view(): void
    {
        $owner = User::factory()->create();

        $accommodation = Accommodation::query()->create([
            'user_id' => $owner->id,
            'title' => 'Villa Aurora',
            'slug' => 'villa-aurora',
            'type' => AccommodationType::Villa,
            'status' => AccommodationStatus::Published,
            'theme_key' => 'luxury',
        ]);

        $this->get("/s/{$accommodation->slug}")
            ->assertOk()
            ->assertSee(__('site.storefront.theme_luxury_name'))
            ->assertSee('Villa Aurora');
    }

    public function test_demo_theme_route_works_without_existing_published_accommodation(): void
    {
        $this->get(route('storefront.demo-theme', 'default'))
            ->assertOk()
            ->assertSee(__('site.storefront.demo_banner_default'))
            ->assertSee('Villa Lavanda Tara');
    }
}

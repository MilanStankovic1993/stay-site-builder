<?php

namespace Tests\Feature;

use App\Enums\AccommodationStatus;
use App\Enums\AccommodationType;
use App\Models\Accommodation;
use App\Models\AccommodationInquiry;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OwnerPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_owner_can_access_dashboard(): void
    {
        $owner = $this->createOwner(isActive: true);

        $this->actingAs($owner)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Izgradi svoj sajt');
    }

    public function test_inactive_owner_cannot_access_dashboard(): void
    {
        $owner = $this->createOwner(isActive: false);

        $this->assertFalse($owner->canAccessPanel(Filament::getPanel('dashboard')));

        $response = $this->actingAs($owner)->get('/dashboard');

        $this->assertContains($response->getStatusCode(), [302, 403]);
    }

    public function test_super_admin_cannot_access_owner_dashboard(): void
    {
        Role::findOrCreate('super_admin');

        $admin = User::factory()->create([
            'is_active' => true,
            'can_publish_sites' => true,
        ]);

        $admin->assignRole('super_admin');

        $this->assertFalse($admin->canAccessPanel(Filament::getPanel('dashboard')));

        $response = $this->actingAs($admin)->get('/dashboard');

        $this->assertContains($response->getStatusCode(), [302, 403]);
    }

    public function test_owner_sees_only_own_accommodations(): void
    {
        $owner = $this->createOwner(isActive: true);
        $otherOwner = $this->createOwner(isActive: true);

        Accommodation::query()->create([
            'user_id' => $owner->id,
            'title' => 'Moja Vila',
            'slug' => 'moja-vila',
            'type' => AccommodationType::Villa,
            'status' => AccommodationStatus::Draft,
        ]);

        Accommodation::query()->create([
            'user_id' => $otherOwner->id,
            'title' => 'Tudja Brvnara',
            'slug' => 'tudja-brvnara',
            'type' => AccommodationType::Cabin,
            'status' => AccommodationStatus::Draft,
        ]);

        $this->actingAs($owner)
            ->get('/dashboard/accommodations')
            ->assertOk()
            ->assertSee('Moja Vila')
            ->assertDontSee('Tudja Brvnara');
    }

    public function test_owner_sees_only_own_inquiries(): void
    {
        $owner = $this->createOwner(isActive: true);
        $otherOwner = $this->createOwner(isActive: true);

        $myAccommodation = Accommodation::query()->create([
            'user_id' => $owner->id,
            'title' => 'Apartman Sunce',
            'slug' => 'apartman-sunce',
            'type' => AccommodationType::Apartment,
            'status' => AccommodationStatus::Published,
        ]);

        $otherAccommodation = Accommodation::query()->create([
            'user_id' => $otherOwner->id,
            'title' => 'Kuca Vetar',
            'slug' => 'kuca-vetar',
            'type' => AccommodationType::House,
            'status' => AccommodationStatus::Published,
        ]);

        AccommodationInquiry::query()->create([
            'accommodation_id' => $myAccommodation->id,
            'user_id' => $owner->id,
            'guest_name' => 'Marko Moj',
            'guest_email' => 'marko@example.com',
            'message' => 'Interesuje me termin za sledeci vikend.',
            'status' => 'new',
            'source' => 'website',
        ]);

        AccommodationInquiry::query()->create([
            'accommodation_id' => $otherAccommodation->id,
            'user_id' => $otherOwner->id,
            'guest_name' => 'Petar Tudji',
            'guest_email' => 'petar@example.com',
            'message' => 'Zelim vise informacija o smestaju.',
            'status' => 'new',
            'source' => 'website',
        ]);

        $this->actingAs($owner)
            ->get('/dashboard/accommodation-inquiries')
            ->assertOk()
            ->assertSee('Marko Moj')
            ->assertDontSee('Petar Tudji');
    }

    protected function createOwner(bool $isActive): User
    {
        Role::findOrCreate('owner');

        $user = User::factory()->create([
            'is_active' => $isActive,
            'can_publish_sites' => false,
        ]);

        $user->assignRole('owner');

        return $user;
    }
}

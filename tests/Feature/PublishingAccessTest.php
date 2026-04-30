<?php

namespace Tests\Feature;

use App\Actions\PublishAccommodation;
use App\Enums\AccommodationStatus;
use App\Enums\AccommodationType;
use App\Models\Accommodation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PublishingAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_manual_package_owner_can_publish_accommodation_through_shared_action(): void
    {
        $owner = $this->createOwner();
        $owner->update([
            'manual_billing_plan_key' => 'basic_monthly',
            'manual_billing_activated_at' => now(),
        ]);

        $accommodation = Accommodation::query()->create([
            'user_id' => $owner->id,
            'title' => 'Test smestaj',
            'slug' => 'test-smestaj',
            'type' => AccommodationType::House->value,
            'status' => AccommodationStatus::Draft->value,
            'currency' => 'EUR',
            'theme_key' => 'default',
        ]);

        $result = app(PublishAccommodation::class)->handle($owner, $accommodation, true);

        $this->assertSame(PublishAccommodation::RESULT_PUBLISHED, $result);
        $this->assertSame(AccommodationStatus::Published, $accommodation->refresh()->status);
        $this->assertNotNull($accommodation->published_at);
    }

    public function test_locked_owner_cannot_publish_accommodation_through_shared_action(): void
    {
        $owner = $this->createOwner();

        $accommodation = Accommodation::query()->create([
            'user_id' => $owner->id,
            'title' => 'Zakljucan smestaj',
            'slug' => 'zakljucan-smestaj',
            'type' => AccommodationType::House->value,
            'status' => AccommodationStatus::Draft->value,
            'currency' => 'EUR',
            'theme_key' => 'default',
        ]);

        $result = app(PublishAccommodation::class)->handle($owner, $accommodation, true);

        $this->assertSame(PublishAccommodation::RESULT_LOCKED, $result);
        $this->assertSame(AccommodationStatus::Draft, $accommodation->refresh()->status);
        $this->assertNull($accommodation->published_at);
    }

    public function test_legacy_manual_publish_flag_no_longer_unlocks_publishing_without_package(): void
    {
        $owner = $this->createOwner();

        $accommodation = Accommodation::query()->create([
            'user_id' => $owner->id,
            'title' => 'Legacy override smestaj',
            'slug' => 'legacy-override-smestaj',
            'type' => AccommodationType::House->value,
            'status' => AccommodationStatus::Draft->value,
            'currency' => 'EUR',
            'theme_key' => 'default',
        ]);

        $result = app(PublishAccommodation::class)->handle($owner->fresh(), $accommodation, true);

        $this->assertSame(PublishAccommodation::RESULT_LOCKED, $result);
        $this->assertFalse($owner->fresh()->canPublishSites());
    }

    public function test_publish_stays_blocked_when_setup_fee_charge_cannot_be_prepared(): void
    {
        config()->set('site-billing.setup_fee', []);

        $owner = $this->createOwner();
        $owner->subscriptions()->create([
            'type' => User::PUBLISHING_SUBSCRIPTION_TYPE,
            'paddle_id' => 'sub_setup_guard',
            'status' => 'active',
        ]);

        $accommodation = Accommodation::query()->create([
            'user_id' => $owner->id,
            'title' => 'Setup fee guard smestaj',
            'slug' => 'setup-fee-guard-smestaj',
            'type' => AccommodationType::House->value,
            'status' => AccommodationStatus::Draft->value,
            'currency' => 'EUR',
            'theme_key' => 'default',
        ]);

        $result = app(PublishAccommodation::class)->handle($owner->fresh(), $accommodation, true);

        $this->assertSame(PublishAccommodation::RESULT_BILLING_FAILED, $result);
        $this->assertSame(AccommodationStatus::Draft, $accommodation->refresh()->status);
        $this->assertNull($accommodation->published_at);
    }

    protected function createOwner(): User
    {
        Role::findOrCreate('owner');

        $user = User::factory()->create([
            'is_active' => true,
        ]);

        $user->assignRole('owner');

        return $user;
    }
}

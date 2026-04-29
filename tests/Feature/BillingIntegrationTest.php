<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Paddle\Subscription as PaddleSubscription;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BillingIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_owner_can_open_billing_page(): void
    {
        $owner = $this->createOwner();

        $this->actingAs($owner)
            ->get(route('dashboard.billing'))
            ->assertOk()
            ->assertSee('Aktivirajte objavu sajta');
    }

    public function test_active_publishing_subscription_unlocks_publish_access(): void
    {
        $owner = $this->createOwner();

        PaddleSubscription::query()->create([
            'billable_type' => User::class,
            'billable_id' => $owner->id,
            'type' => User::PUBLISHING_SUBSCRIPTION_TYPE,
            'paddle_id' => 'sub_123',
            'status' => PaddleSubscription::STATUS_ACTIVE,
        ]);

        $owner->refresh();

        $this->assertTrue($owner->canPublishSites());
        $this->assertTrue($owner->hasPublishingSubscription());
        $this->assertSame(1, $owner->publishingSiteLimit());
    }

    public function test_site_limit_is_derived_from_subscription_quantity(): void
    {
        $owner = $this->createOwner();

        $subscription = PaddleSubscription::query()->create([
            'billable_type' => User::class,
            'billable_id' => $owner->id,
            'type' => User::PUBLISHING_SUBSCRIPTION_TYPE,
            'paddle_id' => 'sub_quantity',
            'status' => PaddleSubscription::STATUS_ACTIVE,
        ]);

        $subscription->items()->create([
            'product_id' => 'pro_product',
            'price_id' => 'pro_price',
            'status' => 'active',
            'quantity' => 10,
        ]);

        $owner->refresh()->load('subscriptions.items');

        $this->assertSame(10, $owner->publishingSiteLimit());
    }

    public function test_manual_billing_plan_unlocks_publish_access_with_package_limit(): void
    {
        $owner = $this->createOwner();

        $owner->update([
            'manual_billing_plan_key' => 'advanced_monthly',
            'manual_billing_activated_at' => now(),
        ]);

        $owner->refresh();

        $this->assertTrue($owner->canPublishSites());
        $this->assertSame(3, $owner->publishingSiteLimit());
        $this->assertSame('StaySite Builder Advanced Monthly', $owner->currentPublishingPlanLabel());
    }

    public function test_owner_with_active_subscription_sees_self_service_billing_actions(): void
    {
        $owner = $this->createOwner();

        PaddleSubscription::query()->create([
            'billable_type' => User::class,
            'billable_id' => $owner->id,
            'type' => User::PUBLISHING_SUBSCRIPTION_TYPE,
            'paddle_id' => 'sub_456',
            'status' => PaddleSubscription::STATUS_ACTIVE,
        ]);

        $this->actingAs($owner)
            ->get(route('dashboard.billing'))
            ->assertOk()
            ->assertSee('Promeni karticu')
            ->assertSee('Otkazi pretplatu');
    }

    public function test_cancel_route_falls_back_with_message_when_billing_is_not_configured(): void
    {
        $owner = $this->createOwner();

        PaddleSubscription::query()->create([
            'billable_type' => User::class,
            'billable_id' => $owner->id,
            'type' => User::PUBLISHING_SUBSCRIPTION_TYPE,
            'paddle_id' => 'sub_789',
            'status' => PaddleSubscription::STATUS_ACTIVE,
        ]);

        config()->set('cashier.api_key', null);
        config()->set('cashier.client_side_token', null);
        config()->set('cashier.seller_id', null);

        $this->actingAs($owner)
            ->post(route('dashboard.billing.cancel'))
            ->assertRedirect(route('dashboard.billing'))
            ->assertSessionHas('billing_error');
    }

    protected function createOwner(): User
    {
        Role::findOrCreate('owner');

        $user = User::factory()->create([
            'is_active' => true,
            'can_publish_sites' => false,
        ]);

        $user->assignRole('owner');

        return $user;
    }
}

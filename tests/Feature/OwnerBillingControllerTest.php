<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Paddle\Subscription as PaddleSubscription;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OwnerBillingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_redirects_back_when_billing_is_not_configured(): void
    {
        $owner = $this->createOwner();

        config()->set('cashier.api_key', null);
        config()->set('cashier.client_side_token', null);
        config()->set('cashier.seller_id', null);

        $this->actingAs($owner)
            ->get(route('dashboard.billing.checkout', ['plan' => 'basic_monthly']))
            ->assertRedirect(route('dashboard.billing'))
            ->assertSessionHas('billing_error');
    }

    public function test_checkout_returns_not_found_for_unknown_plan(): void
    {
        $owner = $this->createOwner();

        $this->actingAs($owner)
            ->get(route('dashboard.billing.checkout', ['plan' => 'missing-plan']))
            ->assertNotFound();
    }

    public function test_change_plan_redirects_to_checkout_when_owner_has_no_subscription(): void
    {
        $owner = $this->createOwner();

        $this->actingAs($owner)
            ->post(route('dashboard.billing.change-plan', ['plan' => 'advanced_monthly']))
            ->assertRedirect(route('dashboard.billing.checkout', ['plan' => 'advanced_monthly']));
    }

    public function test_change_plan_falls_back_when_billing_is_not_configured_for_active_subscription(): void
    {
        $owner = $this->createOwnerWithSubscription('basic_monthly');

        config()->set('cashier.api_key', null);
        config()->set('cashier.client_side_token', null);
        config()->set('cashier.seller_id', null);

        $this->actingAs($owner)
            ->post(route('dashboard.billing.change-plan', ['plan' => 'basic_monthly']))
            ->assertRedirect(route('dashboard.billing'))
            ->assertSessionHas('billing_error');
    }

    public function test_change_plan_requires_catalog_prices_for_active_subscription_swaps(): void
    {
        $owner = $this->createOwnerWithSubscription('basic_monthly');

        $this->configureCatalogPrices();
        config()->set('cashier.api_key', 'test_api_key');
        config()->set('cashier.client_side_token', 'test_client_token');
        config()->set('site-billing.plans.advanced_monthly.price_id', null);

        $this->actingAs($owner)
            ->post(route('dashboard.billing.change-plan', ['plan' => 'advanced_monthly']))
            ->assertRedirect(route('dashboard.billing'))
            ->assertSessionHas('billing_error');
    }

    public function test_update_payment_method_falls_back_when_subscription_is_missing(): void
    {
        $owner = $this->createOwner();

        $this->actingAs($owner)
            ->post(route('dashboard.billing.update-payment-method'))
            ->assertRedirect(route('dashboard.billing'))
            ->assertSessionHas('billing_error');
    }

    public function test_resume_falls_back_when_subscription_is_missing(): void
    {
        $owner = $this->createOwner();

        $this->actingAs($owner)
            ->post(route('dashboard.billing.resume'))
            ->assertRedirect(route('dashboard.billing'))
            ->assertSessionHas('billing_error');
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

    protected function createOwnerWithSubscription(string $planKey): User
    {
        $owner = $this->createOwner();
        $this->configureCatalogPrices();
        $plan = config("site-billing.plans.{$planKey}");

        $subscription = PaddleSubscription::query()->create([
            'billable_type' => User::class,
            'billable_id' => $owner->id,
            'type' => User::PUBLISHING_SUBSCRIPTION_TYPE,
            'paddle_id' => 'sub_'.$planKey,
            'status' => PaddleSubscription::STATUS_ACTIVE,
        ]);

        $subscription->items()->create([
            'product_id' => $planKey.'_product',
            'price_id' => (string) ($plan['price_id'] ?? $planKey.'_price'),
            'status' => 'active',
            'quantity' => (int) ($plan['site_limit'] ?? 1),
        ]);

        return $owner->fresh()->load('subscriptions.items');
    }

    protected function configureCatalogPrices(): void
    {
        foreach (array_keys((array) config('site-billing.plans', [])) as $planKey) {
            config()->set("site-billing.plans.{$planKey}.price_id", "price_{$planKey}");
        }
    }
}

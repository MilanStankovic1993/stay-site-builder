<?php

namespace Tests\Feature;

use Tests\TestCase;

class BillingCheckCommandTest extends TestCase
{
    public function test_billing_check_reports_missing_required_credentials(): void
    {
        config()->set('cashier.api_key', null);
        config()->set('cashier.client_side_token', null);
        config()->set('cashier.seller_id', null);
        config()->set('cashier.webhook_secret', null);
        config()->set('app.url', 'http://localhost');

        $this->artisan('billing:check')
            ->expectsOutputToContain('Missing CASHIER/PADDLE API key.')
            ->expectsOutputToContain('Missing Paddle client-side token or seller ID.')
            ->expectsOutputToContain('Missing Paddle webhook secret.')
            ->expectsOutputToContain('APP_URL still points to a local address.')
            ->assertExitCode(1);
    }

    public function test_billing_check_passes_with_complete_configuration(): void
    {
        config()->set('app.url', 'https://quadify-web-builder.test');
        config()->set('cashier.api_key', 'test_api_key');
        config()->set('cashier.client_side_token', 'test_client_token');
        config()->set('cashier.webhook_secret', 'whsec_test');
        config()->set('cashier.sandbox', true);

        foreach (array_keys((array) config('site-billing.plans', [])) as $planKey) {
            config()->set("site-billing.plans.{$planKey}.price_id", "price_{$planKey}");
        }

        config()->set('site-billing.setup_fee.price_id', 'pri_setup_fee');

        $this->artisan('billing:check')
            ->expectsOutputToContain('Billing configuration looks ready.')
            ->assertExitCode(0);
    }
}

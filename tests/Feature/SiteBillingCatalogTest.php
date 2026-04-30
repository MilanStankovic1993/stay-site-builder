<?php

namespace Tests\Feature;

use App\Support\SiteBillingCatalog;
use Tests\TestCase;

class SiteBillingCatalogTest extends TestCase
{
    public function test_billing_catalog_requires_webhook_secret_for_ready_state(): void
    {
        config()->set('cashier.api_key', 'test_api_key');
        config()->set('cashier.client_side_token', 'test_client_token');
        config()->set('cashier.webhook_secret', null);

        $this->assertFalse(app(SiteBillingCatalog::class)->isConfigured());
    }

    public function test_billing_catalog_is_ready_when_checkout_and_webhook_credentials_exist(): void
    {
        config()->set('cashier.api_key', 'test_api_key');
        config()->set('cashier.client_side_token', 'test_client_token');
        config()->set('cashier.webhook_secret', 'whsec_test');

        $this->assertTrue(app(SiteBillingCatalog::class)->isConfigured());
    }

    public function test_cashier_webhook_route_is_registered(): void
    {
        $this->assertSame(
            'http://localhost/paddle/webhook',
            route('cashier.webhook'),
        );
    }
}

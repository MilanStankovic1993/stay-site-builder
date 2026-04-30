<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

class SiteBillingHealthCheck
{
    public function results(): array
    {
        $results = [];

        if (! filled(config('cashier.api_key'))) {
            $results[] = $this->error('Missing CASHIER/PADDLE API key.');
        }

        if (! filled(config('cashier.client_side_token')) && ! filled(config('cashier.seller_id'))) {
            $results[] = $this->error('Missing Paddle client-side token or seller ID.');
        }

        if (! filled(config('cashier.webhook_secret'))) {
            $results[] = $this->error('Missing Paddle webhook secret.');
        }

        if (! Route::has('cashier.webhook')) {
            $results[] = $this->error('Cashier webhook route is not registered.');
        }

        $appUrl = (string) config('app.url');
        if (! filled($appUrl)) {
            $results[] = $this->error('APP_URL is missing.');
        } elseif ($this->isLocalUrl($appUrl)) {
            $results[] = $this->warning('APP_URL still points to a local address.');
        }

        $recommendedPlan = (string) config('site-billing.recommended_plan');
        $plans = (array) config('site-billing.plans', []);

        if (! array_key_exists($recommendedPlan, $plans)) {
            $results[] = $this->error("Recommended billing plan [{$recommendedPlan}] does not exist in site-billing.plans.");
        }

        $missingPlanPriceIds = collect($plans)
            ->filter(fn (array $plan): bool => ! filled($plan['price_id'] ?? null))
            ->keys()
            ->values()
            ->all();

        if ($missingPlanPriceIds !== []) {
            $results[] = $this->warning('Missing Paddle price IDs for plans: '.implode(', ', $missingPlanPriceIds));
        }

        $setupFeeAmount = (int) config('site-billing.setup_fee.amount', 0);
        $setupFeePriceId = config('site-billing.setup_fee.price_id');

        if ($setupFeeAmount > 0 && ! filled($setupFeePriceId)) {
            $results[] = $this->warning('Setup fee amount is enabled, but SITE_BILLING_SETUP_FEE_PRICE_ID is missing.');
        }

        if (app()->environment('production') && (bool) config('cashier.sandbox')) {
            $results[] = $this->error('Production environment cannot run with PADDLE_SANDBOX enabled.');
        }

        return $results;
    }

    public function hasErrors(): bool
    {
        return collect($this->results())
            ->contains(fn (array $result): bool => $result['level'] === 'error');
    }

    protected function error(string $message): array
    {
        return [
            'level' => 'error',
            'message' => $message,
        ];
    }

    protected function warning(string $message): array
    {
        return [
            'level' => 'warning',
            'message' => $message,
        ];
    }

    protected function isLocalUrl(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);

        return in_array($host, ['localhost', '127.0.0.1'], true);
    }
}

<?php

namespace App\Support;

class SiteBillingCatalog
{
    public function all(): array
    {
        return collect(config('site-billing.plans', []))
            ->filter(fn (array $plan): bool => filled($plan['name'] ?? null) && filled($plan['amount'] ?? null))
            ->all();
    }

    public function find(string $key): ?array
    {
        $plans = $this->all();

        return isset($plans[$key]) ? array_merge($plans[$key], ['key' => $key]) : null;
    }

    public function options(): array
    {
        return collect($this->all())
            ->mapWithKeys(fn (array $plan, string $key): array => [$key => (string) ($plan['name'] ?? $key)])
            ->all();
    }

    public function recommendedPlanKey(): string
    {
        return (string) config('site-billing.recommended_plan', 'yearly');
    }

    public function isConfigured(): bool
    {
        return filled(config('cashier.api_key'))
            && (filled(config('cashier.client_side_token')) || filled(config('cashier.seller_id')));
    }

    public function hasCatalogPrices(): bool
    {
        $plans = collect($this->all());

        return $plans->isNotEmpty() && $plans->every(fn (array $plan): bool => filled($plan['price_id'] ?? null));
    }
}

<?php

use App\Support\SiteBillingHealthCheck;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('billing:check', function (SiteBillingHealthCheck $healthCheck): int {
    $this->components->info('Checking billing readiness...');

    $results = $healthCheck->results();

    if ($results === []) {
        $this->components->info('Billing configuration looks ready.');

        return self::SUCCESS;
    }

    foreach ($results as $result) {
        if ($result['level'] === 'error') {
            $this->components->error($result['message']);
        } else {
            $this->components->warn($result['message']);
        }
    }

    $this->newLine();
    $this->line('Webhook endpoint: '.route('cashier.webhook'));

    return $healthCheck->hasErrors() ? self::FAILURE : self::SUCCESS;
})->purpose('Validate Paddle and site billing configuration before staging or production deploys.');

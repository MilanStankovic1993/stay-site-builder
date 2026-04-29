<?php

return [
    'recommended_plan' => env('SITE_BILLING_RECOMMENDED_PLAN', 'advanced_yearly'),

    'setup_fee' => [
        'name' => env('SITE_BILLING_SETUP_FEE_NAME', 'StaySite Builder Setup Fee'),
        'description' => env('SITE_BILLING_SETUP_FEE_DESCRIPTION', 'One-time setup and launch fee for the first published accommodation website.'),
        'amount' => (int) env('SITE_BILLING_SETUP_FEE_AMOUNT', 14900),
        'price_id' => env('SITE_BILLING_SETUP_FEE_PRICE_ID'),
    ],

    'plans' => [
        'basic_monthly' => [
            'name' => env('SITE_BILLING_BASIC_MONTHLY_NAME', 'StaySite Builder Basic Monthly'),
            'description' => env('SITE_BILLING_BASIC_MONTHLY_DESCRIPTION', '1 published accommodation website slot with monthly billing.'),
            'amount' => (int) env('SITE_BILLING_BASIC_MONTHLY_AMOUNT', 4900),
            'price_id' => env('SITE_BILLING_BASIC_MONTHLY_PRICE_ID'),
            'interval' => 'month',
            'site_limit' => 1,
            'tier' => 'basic',
        ],
        'basic_yearly' => [
            'name' => env('SITE_BILLING_BASIC_YEARLY_NAME', 'StaySite Builder Basic Yearly'),
            'description' => env('SITE_BILLING_BASIC_YEARLY_DESCRIPTION', '1 published accommodation website slot with yearly billing.'),
            'amount' => (int) env('SITE_BILLING_BASIC_YEARLY_AMOUNT', 49000),
            'price_id' => env('SITE_BILLING_BASIC_YEARLY_PRICE_ID'),
            'interval' => 'year',
            'site_limit' => 1,
            'tier' => 'basic',
        ],
        'advanced_monthly' => [
            'name' => env('SITE_BILLING_ADVANCED_MONTHLY_NAME', 'StaySite Builder Advanced Monthly'),
            'description' => env('SITE_BILLING_ADVANCED_MONTHLY_DESCRIPTION', '3 published accommodation website slots with monthly billing.'),
            'amount' => (int) env('SITE_BILLING_ADVANCED_MONTHLY_AMOUNT', 12900),
            'price_id' => env('SITE_BILLING_ADVANCED_MONTHLY_PRICE_ID'),
            'interval' => 'month',
            'site_limit' => 3,
            'tier' => 'advanced',
        ],
        'advanced_yearly' => [
            'name' => env('SITE_BILLING_ADVANCED_YEARLY_NAME', 'StaySite Builder Advanced Yearly'),
            'description' => env('SITE_BILLING_ADVANCED_YEARLY_DESCRIPTION', '3 published accommodation website slots with yearly billing.'),
            'amount' => (int) env('SITE_BILLING_ADVANCED_YEARLY_AMOUNT', 129000),
            'price_id' => env('SITE_BILLING_ADVANCED_YEARLY_PRICE_ID'),
            'interval' => 'year',
            'site_limit' => 3,
            'tier' => 'advanced',
        ],
        'pro_monthly' => [
            'name' => env('SITE_BILLING_PRO_MONTHLY_NAME', 'StaySite Builder Pro Monthly'),
            'description' => env('SITE_BILLING_PRO_MONTHLY_DESCRIPTION', '10 published accommodation website slots with monthly billing.'),
            'amount' => (int) env('SITE_BILLING_PRO_MONTHLY_AMOUNT', 34900),
            'price_id' => env('SITE_BILLING_PRO_MONTHLY_PRICE_ID'),
            'interval' => 'month',
            'site_limit' => 10,
            'tier' => 'pro',
        ],
        'pro_yearly' => [
            'name' => env('SITE_BILLING_PRO_YEARLY_NAME', 'StaySite Builder Pro Yearly'),
            'description' => env('SITE_BILLING_PRO_YEARLY_DESCRIPTION', '10 published accommodation website slots with yearly billing.'),
            'amount' => (int) env('SITE_BILLING_PRO_YEARLY_AMOUNT', 349000),
            'price_id' => env('SITE_BILLING_PRO_YEARLY_PRICE_ID'),
            'interval' => 'year',
            'site_limit' => 10,
            'tier' => 'pro',
        ],
    ],
];

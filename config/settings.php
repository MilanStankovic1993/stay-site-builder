<?php

use App\Settings\PlatformSettings;
use Spatie\LaravelSettings\SettingsRepositories\DatabaseSettingsRepository;

return [
    'settings' => [
        PlatformSettings::class,
    ],

    'setting_class_path' => app_path('Settings'),

    'migrations_paths' => [
        database_path('settings'),
    ],

    'default_repository' => 'database',

    'repositories' => [
        'database' => [
            'type' => DatabaseSettingsRepository::class,
            'model' => null,
            'table' => 'settings',
            'connection' => null,
        ],
    ],

    'encoder' => null,
    'decoder' => null,

    'cache' => [
        'enabled' => env('SETTINGS_CACHE_ENABLED', false),
        'store' => null,
        'prefix' => null,
        'ttl' => null,
    ],

    'global_casts' => [],

    'auto_discover_settings' => [
        app_path('Settings'),
    ],

    'discovered_settings_cache_path' => base_path('bootstrap/cache'),
];

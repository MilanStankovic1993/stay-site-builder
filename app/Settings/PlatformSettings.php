<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PlatformSettings extends Settings
{
    public string $platform_name;

    public string $platform_contact_email;

    public string $default_meta_title;

    public string $default_meta_description;

    public string $default_theme;

    public static function group(): string
    {
        return 'platform';
    }
}

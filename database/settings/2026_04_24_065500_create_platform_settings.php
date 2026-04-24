<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('platform.platform_name', 'StaySite Builder');
        $this->migrator->add('platform.platform_contact_email', 'hello@example.com');
        $this->migrator->add('platform.default_meta_title', 'StaySite Builder');
        $this->migrator->add('platform.default_meta_description', 'Premium mini-sajtovi za privatne smeštaje.');
        $this->migrator->add('platform.default_theme', 'default');
    }
};

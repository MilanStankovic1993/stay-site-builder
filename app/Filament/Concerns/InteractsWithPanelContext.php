<?php

namespace App\Filament\Concerns;

use Filament\Facades\Filament;

trait InteractsWithPanelContext
{
    public static function currentPanelId(): ?string
    {
        return Filament::getCurrentPanel()?->getId();
    }

    public static function isAdminPanel(): bool
    {
        return static::currentPanelId() === 'admin';
    }

    public static function isOwnerPanel(): bool
    {
        return static::currentPanelId() === 'dashboard';
    }
}

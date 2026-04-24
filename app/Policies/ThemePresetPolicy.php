<?php

namespace App\Policies;

use App\Models\ThemePreset;
use App\Models\User;

class ThemePresetPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, ThemePreset $themePreset): bool
    {
        return $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, ThemePreset $themePreset): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, ThemePreset $themePreset): bool
    {
        return $user->isSuperAdmin();
    }
}

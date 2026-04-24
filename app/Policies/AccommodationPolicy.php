<?php

namespace App\Policies;

use App\Models\Accommodation;
use App\Models\User;

class AccommodationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAccessPanel(filament()->getCurrentPanel());
    }

    public function view(User $user, Accommodation $accommodation): bool
    {
        return $user->isSuperAdmin() || $accommodation->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'owner', 'staff']);
    }

    public function update(User $user, Accommodation $accommodation): bool
    {
        return $user->isSuperAdmin() || $accommodation->user_id === $user->id;
    }

    public function delete(User $user, Accommodation $accommodation): bool
    {
        return $user->isSuperAdmin() || $accommodation->user_id === $user->id;
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'owner']);
    }
}

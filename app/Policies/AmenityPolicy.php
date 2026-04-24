<?php

namespace App\Policies;

use App\Models\Amenity;
use App\Models\User;

class AmenityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, Amenity $amenity): bool
    {
        return $user->isSuperAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Amenity $amenity): bool
    {
        return $user->isSuperAdmin();
    }

    public function delete(User $user, Amenity $amenity): bool
    {
        return $user->isSuperAdmin();
    }
}

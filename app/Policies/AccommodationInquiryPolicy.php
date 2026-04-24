<?php

namespace App\Policies;

use App\Models\AccommodationInquiry;
use App\Models\User;

class AccommodationInquiryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAccessPanel(filament()->getCurrentPanel());
    }

    public function view(User $user, AccommodationInquiry $inquiry): bool
    {
        return $user->isSuperAdmin() || $inquiry->accommodation?->user_id === $user->id;
    }

    public function update(User $user, AccommodationInquiry $inquiry): bool
    {
        return $user->isSuperAdmin() || $inquiry->accommodation?->user_id === $user->id;
    }
}

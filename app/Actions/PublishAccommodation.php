<?php

namespace App\Actions;

use App\Enums\AccommodationStatus;
use App\Models\Accommodation;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Throwable;

class PublishAccommodation
{
    public const RESULT_PUBLISHED = 'published';

    public const RESULT_LOCKED = 'locked';

    public const RESULT_BILLING_FAILED = 'billing_failed';

    public function handle(?User $user, Accommodation $accommodation, bool $enforceBillingRules = true): string
    {
        if ($enforceBillingRules && ! ($user?->hasAvailablePublishingSlot($accommodation) ?? false)) {
            return self::RESULT_LOCKED;
        }

        if ($enforceBillingRules && $user?->requiresPublishingSetupFee()) {
            try {
                $chargeResult = $user->chargePublishingSetupFee();

                if ($chargeResult === null) {
                    Log::warning('Publishing setup fee could not be charged because billing charge data is unavailable.', [
                        'user_id' => $user->id,
                        'accommodation_id' => $accommodation->id,
                    ]);

                    return self::RESULT_BILLING_FAILED;
                }
            } catch (Throwable $exception) {
                Log::warning('Unable to charge publishing setup fee.', [
                    'user_id' => $user->id,
                    'accommodation_id' => $accommodation->id,
                    'message' => $exception->getMessage(),
                ]);

                return self::RESULT_BILLING_FAILED;
            }
        }

        $accommodation->update([
            'status' => AccommodationStatus::Published,
            'published_at' => now(),
        ]);

        return self::RESULT_PUBLISHED;
    }
}

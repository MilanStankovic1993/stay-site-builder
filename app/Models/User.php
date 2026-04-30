<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Paddle\Billable;
use Laravel\Paddle\Subscription;
use Laravel\Paddle\Transaction;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use Billable;
    use HasFactory, HasRoles, Notifiable;

    public const PUBLISHING_SUBSCRIPTION_TYPE = 'publishing';

    public const PUBLISHING_PLAN_SOURCE_ADMIN = 'admin';

    public const PUBLISHING_PLAN_SOURCE_SUBSCRIPTION = 'subscription';

    public const PUBLISHING_PLAN_SOURCE_MANUAL_PLAN = 'manual_plan';

    public const PUBLISHING_PLAN_SOURCE_LOCKED = 'locked';

    public const PUBLISHING_SETUP_FEE_NOT_REQUIRED = 'not_required';

    public const PUBLISHING_SETUP_FEE_PENDING_FIRST_PUBLISH = 'pending_first_publish';

    public const PUBLISHING_SETUP_FEE_CHARGED = 'charged';

    public const PUBLISHING_SETUP_FEE_PENDING = 'pending';

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'manual_billing_plan_key',
        'manual_billing_activated_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'manual_billing_activated_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function accommodations(): HasMany
    {
        return $this->hasMany(Accommodation::class);
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(AccommodationInquiry::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function isOwner(): bool
    {
        return $this->hasRole('owner');
    }

    public function canPublishSites(): bool
    {
        return $this->isSuperAdmin()
            || $this->hasManualBillingPlan()
            || $this->hasPublishingSubscription();
    }

    public function hasPublishingSubscription(): bool
    {
        return $this->subscribed(self::PUBLISHING_SUBSCRIPTION_TYPE);
    }

    public function hasManualBillingPlan(): bool
    {
        return filled($this->manual_billing_plan_key) && is_array($this->manualBillingPlan());
    }

    public function manualBillingPlan(): ?array
    {
        $planKey = (string) ($this->manual_billing_plan_key ?? '');

        if ($planKey === '') {
            return null;
        }

        $plan = config("site-billing.plans.{$planKey}");

        return is_array($plan) ? array_merge($plan, ['key' => $planKey]) : null;
    }

    public function publishedSitesCount(): int
    {
        return $this->accommodations()
            ->where('status', \App\Enums\AccommodationStatus::Published)
            ->count();
    }

    public function publishingSiteLimit(): int
    {
        if ($this->isSuperAdmin()) {
            return PHP_INT_MAX;
        }

        if ($this->hasPublishingSubscription()) {
            $subscription = $this->publishingSubscription();
            $plan = $this->currentPublishingPlan();

            if (is_array($plan) && filled($plan['site_limit'] ?? null)) {
                return (int) $plan['site_limit'];
            }

            return max((int) $subscription?->items->sum('quantity'), 1);
        }

        if ($this->hasManualBillingPlan()) {
            return (int) (($this->manualBillingPlan()['site_limit'] ?? 0));
        }

        return 0;
    }

    public function hasAvailablePublishingSlot(?Accommodation $accommodation = null): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (! ($this->hasPublishingSubscription() || $this->hasManualBillingPlan())) {
            return false;
        }

        if ($accommodation && $accommodation->status === \App\Enums\AccommodationStatus::Published) {
            return true;
        }

        return $this->publishedSitesCount() < $this->publishingSiteLimit();
    }

    public function requiresPublishingSetupFee(): bool
    {
        if ($this->isSuperAdmin()) {
            return false;
        }

        if ($this->hasManualBillingPlan()) {
            return false;
        }

        if ($this->publishingSetupFeeCharged()) {
            return false;
        }

        return $this->publishedSitesCount() === 0;
    }

    public function publishingSetupFeeCharged(): bool
    {
        return $this->publishingTransactions()
            ->where('invoice_number', 'like', 'setup-%')
            ->exists();
    }

    public function publishingSubscription(): ?Subscription
    {
        return $this->subscription(self::PUBLISHING_SUBSCRIPTION_TYPE);
    }

    public function publishingTransactions(): MorphMany
    {
        return $this->transactions()
            ->whereNotNull('paddle_subscription_id')
            ->orderByDesc('billed_at');
    }

    public function chargePublishingSetupFee(): ?array
    {
        $subscription = $this->publishingSubscription();
        $setupFee = config('site-billing.setup_fee');

        if (! $subscription || ! is_array($setupFee) || empty($setupFee['amount'])) {
            return null;
        }

        $response = filled($setupFee['price_id'] ?? null)
            ? $subscription->chargeAndInvoice((string) $setupFee['price_id'])
            : $subscription->chargeAndInvoice([
                [
                    'price' => [
                        'name' => (string) ($setupFee['name'] ?? 'Setup fee'),
                        'description' => (string) ($setupFee['description'] ?? 'Initial setup fee'),
                        'unit_price' => [
                            'amount' => (string) ((int) $setupFee['amount']),
                            'currency_code' => strtoupper((string) config('cashier.currency', 'EUR')),
                        ],
                        'billing_cycle' => null,
                        'tax_mode' => 'account_setting',
                    ],
                    'quantity' => 1,
                ],
            ]);

        Transaction::query()
            ->where('billable_type', self::class)
            ->where('billable_id', $this->id)
            ->where('paddle_subscription_id', $subscription->paddle_id)
            ->latest('created_at')
            ->first()?->update([
                'invoice_number' => 'setup-'.($subscription->paddle_id ?: $this->id),
            ]);

        return $response;
    }

    public function currentPublishingPlan(): ?array
    {
        $subscription = $this->publishingSubscription();

        if ($subscription) {
            $subscriptionPlan = collect(config('site-billing.plans', []))
                ->first(function (array $plan) use ($subscription): bool {
                    $priceId = (string) ($plan['price_id'] ?? '');

                    if ($priceId !== '' && $subscription->items->contains(fn ($item): bool => $item->price_id === $priceId)) {
                        return true;
                    }

                    return (int) ($plan['site_limit'] ?? 0) === max((int) $subscription->items->sum('quantity'), 1);
                });

            if (is_array($subscriptionPlan)) {
                return $subscriptionPlan;
            }
        }

        if ($manualPlan = $this->manualBillingPlan()) {
            return $manualPlan;
        }

        return null;
    }

    public function currentPublishingPlanKey(): ?string
    {
        return $this->currentPublishingPlan()['key'] ?? null;
    }

    public function currentPublishingPlanLabel(): string
    {
        $plan = $this->currentPublishingPlan();

        return (string) ($plan['name'] ?? (app()->getLocale() === 'en' ? 'No plan' : 'Bez paketa'));
    }

    public function publishingUsageLabel(): string
    {
        if ($this->isSuperAdmin()) {
            return app()->getLocale() === 'en' ? 'Unlimited' : 'Neograniceno';
        }

        if (! $this->canPublishSites()) {
            return '0 / 0';
        }

        return $this->publishedSitesCount().' / '.$this->publishingSiteLimit();
    }

    public function hasReachedPublishingLimit(?Accommodation $accommodation = null): bool
    {
        if ($this->isSuperAdmin()) {
            return false;
        }

        if ($accommodation && $accommodation->status === \App\Enums\AccommodationStatus::Published) {
            return false;
        }

        return $this->canPublishSites() && ! $this->hasAvailablePublishingSlot($accommodation);
    }

    public function publishingPlanSourceLabel(): string
    {
        return match ($this->publishingPlanSourceKey()) {
            self::PUBLISHING_PLAN_SOURCE_ADMIN => app()->getLocale() === 'en' ? 'Admin access' : 'Admin pristup',
            self::PUBLISHING_PLAN_SOURCE_SUBSCRIPTION => app()->getLocale() === 'en' ? 'Paddle subscription' : 'Paddle pretplata',
            self::PUBLISHING_PLAN_SOURCE_MANUAL_PLAN => app()->getLocale() === 'en' ? 'Manual package' : 'Rucni paket',
            default => app()->getLocale() === 'en' ? 'Locked' : 'Zakljucano',
        };
    }

    public function publishingPlanSourceKey(): string
    {
        if ($this->isSuperAdmin()) {
            return self::PUBLISHING_PLAN_SOURCE_ADMIN;
        }

        if ($this->hasPublishingSubscription()) {
            return self::PUBLISHING_PLAN_SOURCE_SUBSCRIPTION;
        }

        if ($this->hasManualBillingPlan()) {
            return self::PUBLISHING_PLAN_SOURCE_MANUAL_PLAN;
        }

        return self::PUBLISHING_PLAN_SOURCE_LOCKED;
    }

    public function publishingAccessLabel(): string
    {
        return match ($this->publishingAccessKey()) {
            self::PUBLISHING_PLAN_SOURCE_ADMIN => app()->getLocale() === 'en' ? 'Admin access' : 'Admin pristup',
            self::PUBLISHING_PLAN_SOURCE_MANUAL_PLAN => app()->getLocale() === 'en' ? 'Manual package' : 'Rucni paket',
            self::PUBLISHING_PLAN_SOURCE_SUBSCRIPTION => app()->getLocale() === 'en' ? 'Paid subscription' : 'Placena pretplata',
            default => app()->getLocale() === 'en' ? 'Locked' : 'Zakljucano',
        };
    }

    public function publishingAccessKey(): string
    {
        if ($this->isSuperAdmin()) {
            return self::PUBLISHING_PLAN_SOURCE_ADMIN;
        }

        if ($this->hasManualBillingPlan()) {
            return self::PUBLISHING_PLAN_SOURCE_MANUAL_PLAN;
        }

        if ($this->hasPublishingSubscription()) {
            return self::PUBLISHING_PLAN_SOURCE_SUBSCRIPTION;
        }

        return self::PUBLISHING_PLAN_SOURCE_LOCKED;
    }

    public function publishingAccessColor(): string
    {
        return match ($this->publishingAccessKey()) {
            self::PUBLISHING_PLAN_SOURCE_ADMIN,
            self::PUBLISHING_PLAN_SOURCE_SUBSCRIPTION => 'success',
            self::PUBLISHING_PLAN_SOURCE_MANUAL_PLAN => 'info',
            default => 'gray',
        };
    }

    public function latestPublishingTransaction(): ?Transaction
    {
        return $this->publishingTransactions()->first();
    }

    public function latestPublishingTransactionLabel(): string
    {
        $transaction = $this->latestPublishingTransaction();

        if (! $transaction) {
            return app()->getLocale() === 'en' ? 'No payments yet' : 'Jos nema uplata';
        }

        $date = $transaction->billed_at?->format('d.m.Y H:i') ?? $transaction->created_at?->format('d.m.Y H:i');

        return trim($transaction->total().' '.($date ? '- '.$date : ''));
    }

    public function publishingSetupFeeStatusLabel(): string
    {
        return match ($this->publishingSetupFeeStatusKey()) {
            self::PUBLISHING_SETUP_FEE_NOT_REQUIRED => app()->getLocale() === 'en' ? 'Not required' : 'Nije potrebno',
            self::PUBLISHING_SETUP_FEE_PENDING_FIRST_PUBLISH => app()->getLocale() === 'en' ? 'Pending first publish' : 'Ceka prvi publish',
            self::PUBLISHING_SETUP_FEE_CHARGED => app()->getLocale() === 'en' ? 'Charged' : 'Naplaceno',
            default => app()->getLocale() === 'en' ? 'Pending' : 'Na cekanju',
        };
    }

    public function publishingSetupFeeStatusKey(): string
    {
        if ($this->isSuperAdmin() || $this->hasManualBillingPlan()) {
            return self::PUBLISHING_SETUP_FEE_NOT_REQUIRED;
        }

        if (! ($this->hasPublishingSubscription() || $this->publishedSitesCount() > 0)) {
            return self::PUBLISHING_SETUP_FEE_PENDING_FIRST_PUBLISH;
        }

        if ($this->publishingSetupFeeCharged()) {
            return self::PUBLISHING_SETUP_FEE_CHARGED;
        }

        return self::PUBLISHING_SETUP_FEE_PENDING;
    }

    public function publishingSetupFeeStatusColor(): string
    {
        return match ($this->publishingSetupFeeStatusKey()) {
            self::PUBLISHING_SETUP_FEE_CHARGED,
            self::PUBLISHING_SETUP_FEE_NOT_REQUIRED => 'success',
            self::PUBLISHING_SETUP_FEE_PENDING_FIRST_PUBLISH => 'warning',
            default => 'gray',
        };
    }

    public function isDemoAccount(): bool
    {
        return $this->email === 'owner@example.com';
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->is_active) {
            return false;
        }

        return match ($panel->getId()) {
            'admin' => $this->isSuperAdmin(),
            'dashboard' => $this->hasAnyRole(['owner', 'staff']),
            default => false,
        };
    }
}

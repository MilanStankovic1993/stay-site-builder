<?php

namespace App\Support;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Laravel\Paddle\Subscription;
use Laravel\Paddle\Transaction;

class AdminBillingResourceSupport
{
    public static function ownerOptions(): array
    {
        return User::query()
            ->role('owner')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    public static function openUserUrl(int|string|null $billableId): string
    {
        return UserResource::getUrl('edit', ['record' => $billableId], panel: 'admin');
    }

    public static function subscriptionStatusOptions(): array
    {
        return [
            Subscription::STATUS_ACTIVE => __('admin.billing_resources.status_active'),
            Subscription::STATUS_TRIALING => __('admin.billing_resources.status_trialing'),
            Subscription::STATUS_PAST_DUE => __('admin.billing_resources.status_past_due'),
            Subscription::STATUS_PAUSED => __('admin.billing_resources.status_paused'),
            Subscription::STATUS_CANCELED => __('admin.billing_resources.status_canceled'),
        ];
    }

    public static function subscriptionStatusColor(string $state): string
    {
        return match ($state) {
            Subscription::STATUS_ACTIVE, Subscription::STATUS_TRIALING => 'success',
            Subscription::STATUS_PAST_DUE => 'warning',
            Subscription::STATUS_CANCELED => 'danger',
            Subscription::STATUS_PAUSED => 'gray',
            default => 'gray',
        };
    }

    public static function transactionStatusOptions(): array
    {
        return [
            Transaction::STATUS_COMPLETED => __('admin.billing_resources.transaction_completed'),
            Transaction::STATUS_PAID => __('admin.billing_resources.transaction_paid'),
            Transaction::STATUS_BILLED => __('admin.billing_resources.transaction_billed'),
            Transaction::STATUS_PAST_DUE => __('admin.billing_resources.status_past_due'),
            Transaction::STATUS_CANCELED => __('admin.billing_resources.status_canceled'),
        ];
    }

    public static function transactionStatusColor(string $state): string
    {
        return match ($state) {
            Transaction::STATUS_PAID, Transaction::STATUS_COMPLETED, Transaction::STATUS_BILLED => 'success',
            Transaction::STATUS_READY, Transaction::STATUS_DRAFT => 'gray',
            Transaction::STATUS_PAST_DUE => 'warning',
            Transaction::STATUS_CANCELED => 'danger',
            default => 'gray',
        };
    }
}

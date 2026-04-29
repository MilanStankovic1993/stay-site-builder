<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Widgets\Widget;
use Laravel\Paddle\Subscription;

class AdminBillingOverviewWidget extends Widget
{
    protected string $view = 'filament.widgets.admin-billing-overview';

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $publishingSubscription = fn ($query) => $query
            ->where('type', User::PUBLISHING_SUBSCRIPTION_TYPE)
            ->where(function ($statusQuery): void {
                $statusQuery
                    ->where('status', Subscription::STATUS_ACTIVE)
                    ->orWhere('status', Subscription::STATUS_TRIALING);
            });

        $ownerQuery = fn () => User::query()
            ->whereHas('roles', fn ($roleQuery) => $roleQuery->where('name', 'owner'));

        $lockedOwners = $ownerQuery()
            ->where('is_active', true)
            ->where('can_publish_sites', false)
            ->whereDoesntHave('subscriptions', $publishingSubscription)
            ->count();

        return [
            'setupReady' => filled(config('cashier.api_key'))
                && (filled(config('cashier.client_side_token')) || filled(config('cashier.seller_id'))),
            'stats' => [
                [
                    'label' => __('admin.billing.admin_stats.paid'),
                    'value' => $ownerQuery()->whereHas('subscriptions', $publishingSubscription)->count(),
                    'tone' => 'success',
                ],
                [
                    'label' => __('admin.billing.admin_stats.manual'),
                    'value' => $ownerQuery()->where('can_publish_sites', true)->count(),
                    'tone' => 'warning',
                ],
                [
                    'label' => __('admin.billing.admin_stats.locked'),
                    'value' => $lockedOwners,
                    'tone' => $lockedOwners > 0 ? 'danger' : 'neutral',
                ],
                [
                    'label' => __('admin.billing.admin_stats.pending_activation'),
                    'value' => $ownerQuery()->where('is_active', false)->count(),
                    'tone' => 'neutral',
                ],
            ],
            'recentOwners' => $ownerQuery()
                ->where(function ($query) use ($publishingSubscription): void {
                    $query
                        ->where('can_publish_sites', true)
                        ->orWhereHas('subscriptions', $publishingSubscription);
                })
                ->with([
                    'subscriptions' => fn ($query) => $query
                        ->where('type', User::PUBLISHING_SUBSCRIPTION_TYPE)
                        ->orderByDesc('created_at'),
                    'transactions' => fn ($query) => $query
                        ->whereNotNull('paddle_subscription_id')
                        ->orderByDesc('billed_at'),
                ])
                ->latest()
                ->limit(6)
                ->get(),
            'usersUrl' => UserResource::getUrl(panel: 'admin'),
        ];
    }
}

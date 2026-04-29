<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Paddle\Checkout;
use Laravel\Paddle\Payment;
use Laravel\Paddle\Subscription;
use Throwable;

class OwnerBillingController extends Controller
{
    public function index(Request $request): View
    {
        $user = $this->resolveOwner($request);
        $activeSubscription = $user->publishingSubscription();

        return view('billing.index', [
            'user' => $user,
            'plans' => $this->plans(),
            'recommendedPlan' => config('site-billing.recommended_plan', 'yearly'),
            'setupReady' => $this->isBillingConfigured(),
            'catalogReady' => $this->hasCatalogPrices(),
            'activeSubscription' => $activeSubscription,
            'latestTransaction' => $user->publishingTransactions()->first(),
            'nextPayment' => $this->resolveNextPayment($activeSubscription),
            'checkoutState' => (string) $request->query('checkout', ''),
            'billingError' => session('billing_error'),
            'billingStatus' => session('billing_status'),
        ]);
    }

    public function checkout(Request $request, string $plan): View|RedirectResponse
    {
        $user = $this->resolveOwner($request);

        $plans = $this->plans();
        abort_unless(isset($plans[$plan]), 404);

        if (! $this->isBillingConfigured()) {
            return redirect()->route('dashboard.billing')
                ->with('billing_error', __('admin.billing.setup_missing'));
        }

        try {
            $checkout = $this->buildCheckout($user, $plans[$plan], $plan);
        } catch (Throwable $exception) {
            Log::error('Unable to initialize Paddle checkout.', [
                'user_id' => $user->id,
                'plan' => $plan,
                'message' => $exception->getMessage(),
            ]);

            return redirect()->route('dashboard.billing')
                ->with('billing_error', __('admin.billing.checkout_unavailable'));
        }

        return view('billing.checkout', [
            'user' => $user,
            'plan' => $plans[$plan],
            'planKey' => $plan,
            'checkout' => $checkout,
        ]);
    }

    public function updatePaymentMethod(Request $request): RedirectResponse
    {
        $user = $this->resolveOwner($request);
        $subscription = $user->publishingSubscription();

        if (! $subscription || ! $this->isBillingConfigured()) {
            return redirect()->route('dashboard.billing')
                ->with('billing_error', __('admin.billing.manage_unavailable'));
        }

        try {
            return $subscription->redirectToUpdatePaymentMethod();
        } catch (Throwable $exception) {
            Log::warning('Unable to open Paddle update payment method page.', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'message' => $exception->getMessage(),
            ]);

            return redirect()->route('dashboard.billing')
                ->with('billing_error', __('admin.billing.manage_unavailable'));
        }
    }

    public function cancel(Request $request): RedirectResponse
    {
        $user = $this->resolveOwner($request);
        $subscription = $user->publishingSubscription();

        if (! $subscription || ! $this->isBillingConfigured()) {
            return redirect()->route('dashboard.billing')
                ->with('billing_error', __('admin.billing.cancel_unavailable'));
        }

        try {
            $subscription->cancel();
        } catch (Throwable $exception) {
            Log::warning('Unable to cancel Paddle subscription.', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'message' => $exception->getMessage(),
            ]);

            return redirect()->route('dashboard.billing')
                ->with('billing_error', __('admin.billing.cancel_unavailable'));
        }

        return redirect()->route('dashboard.billing')
            ->with('billing_status', __('admin.billing.cancel_success'));
    }

    public function resume(Request $request): RedirectResponse
    {
        $user = $this->resolveOwner($request);
        $subscription = $user->publishingSubscription();

        if (! $subscription || ! $this->isBillingConfigured()) {
            return redirect()->route('dashboard.billing')
                ->with('billing_error', __('admin.billing.resume_unavailable'));
        }

        try {
            $subscription->stopCancelation();
        } catch (Throwable $exception) {
            Log::warning('Unable to resume Paddle subscription cancellation.', [
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'message' => $exception->getMessage(),
            ]);

            return redirect()->route('dashboard.billing')
                ->with('billing_error', __('admin.billing.resume_unavailable'));
        }

        return redirect()->route('dashboard.billing')
            ->with('billing_status', __('admin.billing.resume_success'));
    }

    protected function buildCheckout(User $user, array $plan, string $planKey): Checkout
    {
        if (filled($plan['price_id'] ?? null)) {
            return $user
                ->checkout((string) $plan['price_id'])
                ->customData([
                    'subscription_type' => User::PUBLISHING_SUBSCRIPTION_TYPE,
                    'site_limit' => (int) ($plan['site_limit'] ?? 1),
                    'plan_key' => $planKey,
                ])
                ->returnTo(route('dashboard.billing', [
                    'checkout' => 'success',
                    'plan' => $planKey,
                ]));
        }

        $builder = $user->newSubscription(
            amount: (int) $plan['amount'],
            name: (string) $plan['name'],
            type: User::PUBLISHING_SUBSCRIPTION_TYPE,
        )->quantity((int) ($plan['site_limit'] ?? 1));

        $builder = match ($plan['interval']) {
            'day' => $builder->daily(),
            'week' => $builder->weekly(),
            'month' => $builder->monthly(),
            default => $builder->yearly(),
        };

        return $builder
            ->checkout([
                'price' => [
                    'description' => (string) ($plan['description'] ?? $plan['name']),
                    'product' => [
                        'name' => (string) $plan['name'],
                        'tax_category' => 'standard',
                    ],
                ],
                'custom_data' => [
                    'subscription_type' => User::PUBLISHING_SUBSCRIPTION_TYPE,
                    'site_limit' => (int) ($plan['site_limit'] ?? 1),
                    'plan_key' => $planKey,
                ],
            ])
            ->returnTo(route('dashboard.billing', [
                'checkout' => 'success',
                'plan' => $planKey,
            ]));
    }

    protected function plans(): array
    {
        return collect(config('site-billing.plans', []))
            ->filter(fn (array $plan): bool => filled($plan['name'] ?? null) && filled($plan['amount'] ?? null))
            ->all();
    }

    protected function isBillingConfigured(): bool
    {
        return filled(config('cashier.api_key'))
            && (filled(config('cashier.client_side_token')) || filled(config('cashier.seller_id')));
    }

    protected function hasCatalogPrices(): bool
    {
        $plans = collect(config('site-billing.plans', []));

        return $plans->isNotEmpty() && $plans->every(fn (array $plan): bool => filled($plan['price_id'] ?? null));
    }

    protected function resolveNextPayment(?Subscription $subscription): ?Payment
    {
        if (! $subscription || ! $this->isBillingConfigured() || $subscription->onGracePeriod()) {
            return null;
        }

        try {
            return $subscription->nextPayment();
        } catch (Throwable $exception) {
            Log::info('Unable to resolve next Paddle payment.', [
                'subscription_id' => $subscription->id,
                'message' => $exception->getMessage(),
            ]);

            return null;
        }
    }

    protected function resolveOwner(Request $request): User
    {
        $user = $request->user();

        abort_unless(
            $user instanceof User
                && $user->is_active
                && ($user->isOwner() || $user->isSuperAdmin()),
            403,
        );

        return $user;
    }
}

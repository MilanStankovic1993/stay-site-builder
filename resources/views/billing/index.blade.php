<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('admin.billing.title') }}</title>
    @vite(['resources/css/app.css'])
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(circle at top right, rgba(16, 185, 129, 0.18), transparent 20%),
                linear-gradient(180deg, #081018, #0f172a 60%, #0b1220);
            color: #e2e8f0;
            font-family: ui-sans-serif, system-ui, sans-serif;
        }

        .billing-shell {
            max-width: 1120px;
            margin: 0 auto;
            padding: 2rem 1.25rem 3rem;
        }

        .billing-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #99f6e4;
            text-decoration: none;
            font-weight: 700;
        }

        .billing-hero,
        .billing-card {
            border: 1px solid rgba(71, 85, 105, 0.45);
            border-radius: 1.75rem;
            background: rgba(15, 23, 42, 0.84);
            box-shadow: 0 24px 60px rgba(2, 6, 23, 0.34);
        }

        .billing-hero {
            margin-top: 1.25rem;
            padding: 1.8rem;
        }

        .billing-eyebrow {
            margin: 0;
            color: #99f6e4;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.28em;
            text-transform: uppercase;
        }

        .billing-title {
            margin: 0.85rem 0 0;
            color: #f8fafc;
            font-size: clamp(2rem, 4vw, 3.4rem);
            line-height: 1;
        }

        .billing-copy {
            margin: 0.9rem 0 0;
            max-width: 46rem;
            color: #cbd5e1;
            font-size: 1rem;
            line-height: 1.75;
        }

        .billing-alert {
            margin-top: 1.1rem;
            padding: 1rem 1.1rem;
            border-radius: 1.1rem;
            font-size: 0.95rem;
            line-height: 1.65;
        }

        .billing-alert--success {
            border: 1px solid rgba(16, 185, 129, 0.38);
            background: rgba(6, 78, 59, 0.28);
            color: #a7f3d0;
        }

        .billing-alert--warning {
            border: 1px solid rgba(251, 191, 36, 0.34);
            background: rgba(120, 53, 15, 0.2);
            color: #fde68a;
        }

        .billing-alert--info {
            border: 1px solid rgba(59, 130, 246, 0.34);
            background: rgba(30, 64, 175, 0.18);
            color: #bfdbfe;
        }

        .billing-grid {
            margin-top: 1.5rem;
            display: grid;
            gap: 1rem;
        }

        @media (min-width: 900px) {
            .billing-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        .billing-card {
            padding: 1.5rem;
        }

        .billing-card.is-recommended {
            border-color: rgba(16, 185, 129, 0.38);
            background:
                radial-gradient(circle at top right, rgba(16, 185, 129, 0.14), transparent 28%),
                rgba(15, 23, 42, 0.9);
        }

        .billing-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.4rem 0.78rem;
            border-radius: 999px;
            background: rgba(16, 185, 129, 0.16);
            border: 1px solid rgba(45, 212, 191, 0.3);
            color: #99f6e4;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .billing-plan {
            margin: 1rem 0 0;
            color: #f8fafc;
            font-size: 1.65rem;
            font-weight: 800;
        }

        .billing-price {
            margin: 0.5rem 0 0;
            color: #99f6e4;
            font-size: 2.4rem;
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        .billing-desc,
        .billing-meta {
            margin: 0.75rem 0 0;
            color: #cbd5e1;
            line-height: 1.7;
        }

        .billing-meta {
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .billing-list {
            margin: 0.9rem 0 0;
            padding-left: 1.15rem;
            color: #cbd5e1;
            line-height: 1.75;
        }

        .billing-kicker {
            margin: 0;
            color: #99f6e4;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.2em;
            text-transform: uppercase;
        }

        .billing-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.8rem;
            margin-top: 1.2rem;
        }

        .billing-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 3rem;
            margin-top: 1.2rem;
            padding: 0.8rem 1.2rem;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 800;
            transition: transform 180ms ease, box-shadow 180ms ease;
        }

        .billing-btn:hover {
            transform: translateY(-1px);
        }

        .billing-btn--primary {
            background: linear-gradient(135deg, #0f766e, #10b981);
            color: white;
            box-shadow: 0 16px 34px rgba(16, 185, 129, 0.18);
        }

        .billing-btn--secondary {
            background: rgba(30, 41, 59, 0.9);
            border: 1px solid rgba(71, 85, 105, 0.5);
            color: #e2e8f0;
        }

        .billing-btn--danger {
            background: rgba(127, 29, 29, 0.38);
            border: 1px solid rgba(248, 113, 113, 0.38);
            color: #fecaca;
        }

        .billing-btn--ghost {
            background: rgba(8, 145, 178, 0.16);
            border: 1px solid rgba(34, 211, 238, 0.28);
            color: #a5f3fc;
        }

        .billing-form {
            margin: 0;
        }
    </style>
</head>
<body>
    <main class="billing-shell">
        <a href="{{ url('/dashboard') }}" class="billing-back">{{ __('admin.billing.back_to_dashboard') }}</a>

        <section class="billing-hero">
            <p class="billing-eyebrow">{{ __('admin.billing.eyebrow') }}</p>
            <h1 class="billing-title">{{ __('admin.billing.title') }}</h1>
            <p class="billing-copy">{{ __('admin.billing.description') }}</p>
            <p class="billing-copy" style="margin-top: 0.6rem;">{{ __('admin.billing.setup_fee_note') }}</p>

            @if ($checkoutState === 'success')
                <div class="billing-alert billing-alert--success">{{ __('admin.billing.processing_success') }}</div>
            @endif

            @if ($billingStatus)
                <div class="billing-alert billing-alert--info">{{ $billingStatus }}</div>
            @endif

            @if ($billingError)
                <div class="billing-alert billing-alert--warning">{{ $billingError }}</div>
            @endif

            @if ($activeSubscription)
                <div class="billing-alert billing-alert--success">
                    {{ __('admin.billing.active_access') }}
                    @if ($latestTransaction)
                        {{ __('admin.billing.last_payment') }} {{ $latestTransaction->billed_at?->format('d.m.Y H:i') }}.
                    @endif
                </div>

                @if ($activeSubscription->onGracePeriod())
                    <div class="billing-alert billing-alert--warning">
                        {{ __('admin.billing.cancel_pending') }}
                        @if ($activeSubscription->ends_at)
                            {{ __('admin.billing.access_until') }} {{ $activeSubscription->ends_at->format('d.m.Y H:i') }}.
                        @endif
                    </div>
                @endif
            @elseif (! $setupReady)
                <div class="billing-alert billing-alert--warning">{{ __('admin.billing.setup_missing') }}</div>
            @elseif (! $catalogReady)
                <div class="billing-alert billing-alert--info">{{ __('admin.billing.catalog_fallback_note') }}</div>
            @endif
        </section>

        @if (! $activeSubscription)
            <section class="billing-grid">
                @foreach ($plans as $planKey => $plan)
                    <article class="billing-card {{ $planKey === $recommendedPlan ? 'is-recommended' : '' }}">
                        @if ($planKey === $recommendedPlan)
                            <span class="billing-badge">{{ __('admin.billing.recommended') }}</span>
                        @endif

                        <h2 class="billing-plan">{{ $plan['name'] }}</h2>
                        <p class="billing-price">
                            {{ number_format(((int) $plan['amount']) / 100, 2) }} {{ config('cashier.currency', 'EUR') }}
                        </p>
                        <p class="billing-desc">{{ $plan['description'] }}</p>
                        <p class="billing-meta">
                            {{ $plan['interval'] === 'year' ? __('admin.billing.interval_yearly') : __('admin.billing.interval_monthly') }}
                        </p>
                        <ul class="billing-list">
                            <li>{{ trans_choice('admin.billing.site_slots', $plan['site_limit'], ['count' => $plan['site_limit']]) }}</li>
                            <li>{{ __('admin.billing.draft_unlimited') }}</li>
                        </ul>

                        @if ($setupReady)
                            <a href="{{ route('dashboard.billing.checkout', ['plan' => $planKey]) }}" class="billing-btn billing-btn--primary">
                                {{ __('admin.billing.activate_plan') }}
                            </a>
                        @endif
                    </article>
                @endforeach
            </section>
        @else
            <section class="billing-grid">
                <article class="billing-card">
                    <span class="billing-badge">{{ __('admin.billing.active_badge') }}</span>
                    <p class="billing-kicker">{{ __('admin.billing.current_plan') }}</p>
                    <h2 class="billing-plan">{{ __('admin.billing.publish_enabled_title') }}</h2>
                    <p class="billing-desc">{{ __('admin.billing.publish_enabled_text') }}</p>
                    <p class="billing-meta">{{ __('admin.billing.site_capacity') }} {{ $user->publishedSitesCount() }} / {{ $user->publishingSiteLimit() }}</p>
                    <p class="billing-meta">{{ __('admin.billing.subscription_status') }} {{ $activeSubscription->status }}</p>
                    <p class="billing-meta">{{ __('admin.billing.started_at') }} {{ $activeSubscription->created_at?->format('d.m.Y H:i') ?? '—' }}</p>

                    @if ($nextPayment)
                        <p class="billing-meta">
                            {{ __('admin.billing.next_payment') }} {{ $nextPayment->amount() }} / {{ $nextPayment->date()?->format('d.m.Y') }}
                        </p>
                    @endif

                    @if ($activeSubscription->ends_at)
                        <p class="billing-meta">{{ __('admin.billing.ends_at') }} {{ $activeSubscription->ends_at->format('d.m.Y H:i') }}</p>
                    @endif

                    <div class="billing-actions">
                        <form method="POST" action="{{ route('dashboard.billing.update-payment-method') }}" class="billing-form">
                            @csrf
                            <button type="submit" class="billing-btn billing-btn--ghost">{{ __('admin.billing.update_payment_method') }}</button>
                        </form>

                        @if ($activeSubscription->onGracePeriod())
                            <form method="POST" action="{{ route('dashboard.billing.resume') }}" class="billing-form">
                                @csrf
                                <button type="submit" class="billing-btn billing-btn--primary">{{ __('admin.billing.resume_subscription') }}</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('dashboard.billing.cancel') }}" class="billing-form" onsubmit="return confirm('{{ __('admin.billing.cancel_confirm') }}')">
                                @csrf
                                <button type="submit" class="billing-btn billing-btn--danger">{{ __('admin.billing.cancel_subscription') }}</button>
                            </form>
                        @endif

                        <a href="{{ url('/dashboard') }}" class="billing-btn billing-btn--secondary">{{ __('admin.billing.back_to_dashboard') }}</a>
                    </div>
                </article>

                <article class="billing-card">
                    <p class="billing-kicker">{{ __('admin.billing.manage_title') }}</p>
                    <h2 class="billing-plan">{{ __('admin.billing.manage_heading') }}</h2>
                    <p class="billing-desc">{{ __('admin.billing.manage_copy') }}</p>
                    <p class="billing-meta">{{ __('admin.billing.manage_note') }}</p>
                    <ul class="billing-list">
                        <li>{{ __('admin.billing.manage_slot_note') }}</li>
                        <li>{{ __('admin.billing.manage_setup_note') }}</li>
                    </ul>
                </article>
            </section>
        @endif
    </main>
</body>
</html>

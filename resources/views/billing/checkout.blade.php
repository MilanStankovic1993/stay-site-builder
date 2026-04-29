<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('admin.billing.checkout_title') }}</title>
    @vite(['resources/css/app.css'])
    @paddleJS
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(circle at top right, rgba(16, 185, 129, 0.16), transparent 20%),
                linear-gradient(180deg, #081018, #0f172a 60%, #0b1220);
            color: #e2e8f0;
            font-family: ui-sans-serif, system-ui, sans-serif;
        }

        .checkout-shell {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2rem 1.25rem 3rem;
        }

        .checkout-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #99f6e4;
            text-decoration: none;
            font-weight: 700;
        }

        .checkout-grid {
            margin-top: 1.25rem;
            display: grid;
            gap: 1rem;
        }

        @media (min-width: 980px) {
            .checkout-grid {
                grid-template-columns: minmax(280px, 0.38fr) minmax(0, 0.62fr);
                align-items: start;
            }
        }

        .checkout-card {
            border: 1px solid rgba(71, 85, 105, 0.45);
            border-radius: 1.75rem;
            background: rgba(15, 23, 42, 0.86);
            box-shadow: 0 24px 60px rgba(2, 6, 23, 0.34);
        }

        .checkout-summary {
            padding: 1.5rem;
        }

        .checkout-eyebrow {
            margin: 0;
            color: #99f6e4;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.28em;
            text-transform: uppercase;
        }

        .checkout-title {
            margin: 0.85rem 0 0;
            color: #f8fafc;
            font-size: 2rem;
            line-height: 1.08;
        }

        .checkout-copy,
        .checkout-meta {
            margin: 0.85rem 0 0;
            color: #cbd5e1;
            line-height: 1.7;
        }

        .checkout-price {
            margin: 1rem 0 0;
            color: #99f6e4;
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: -0.04em;
        }

        .checkout-frame {
            padding: 0.8rem;
        }
    </style>
</head>
<body>
    <main class="checkout-shell">
        <a href="{{ route('dashboard.billing') }}" class="checkout-back">{{ __('admin.billing.back_to_billing') }}</a>

        <div class="checkout-grid">
            <section class="checkout-card checkout-summary">
                <p class="checkout-eyebrow">{{ __('admin.billing.checkout_eyebrow') }}</p>
                <h1 class="checkout-title">{{ $plan['name'] }}</h1>
                <p class="checkout-price">{{ number_format(((int) $plan['amount']) / 100, 2) }} {{ config('cashier.currency', 'EUR') }}</p>
                <p class="checkout-copy">{{ $plan['description'] }}</p>
                <p class="checkout-meta">
                    {{ $plan['interval'] === 'year' ? __('admin.billing.interval_yearly') : __('admin.billing.interval_monthly') }}
                </p>
                <p class="checkout-meta">{{ __('admin.billing.checkout_help') }}</p>
            </section>

            <section class="checkout-card checkout-frame">
                <x-paddle-checkout :checkout="$checkout" height="720" />
            </section>
        </div>
    </main>
</body>
</html>

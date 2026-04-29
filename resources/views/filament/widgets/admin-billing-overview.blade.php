<x-filament-widgets::widget>
    <style>
        .admin-billing-card {
            overflow: hidden;
            border: 1px solid rgba(226, 232, 240, 0.92);
            border-radius: 1.75rem;
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 22px 60px rgba(15, 23, 42, 0.08);
        }

        .admin-billing-hero {
            padding: 1.75rem;
            color: white;
            background:
                radial-gradient(circle at top right, rgba(253, 224, 71, 0.22), transparent 34%),
                linear-gradient(135deg, #132d27, #1f3a32 54%, #315245);
        }

        .admin-billing-eyebrow {
            margin: 0;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.32em;
            text-transform: uppercase;
            color: #fde68a;
        }

        .admin-billing-top {
            margin-top: 0.85rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        @media (min-width: 960px) {
            .admin-billing-top {
                flex-direction: row;
                align-items: end;
                justify-content: space-between;
            }
        }

        .admin-billing-title {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .admin-billing-copy {
            margin: 0.65rem 0 0;
            max-width: 46rem;
            font-size: 0.95rem;
            line-height: 1.75;
            color: rgba(255, 255, 255, 0.82);
        }

        .admin-billing-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.72rem 1rem;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            background: rgba(255, 255, 255, 0.08);
            color: white;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 700;
            transition: transform 180ms ease, background-color 180ms ease;
        }

        .admin-billing-link:hover {
            transform: translateY(-1px);
            background: rgba(255, 255, 255, 0.12);
        }

        .admin-billing-warning {
            margin-top: 1rem;
            padding: 0.9rem 1rem;
            border: 1px solid rgba(251, 191, 36, 0.38);
            border-radius: 1rem;
            background: rgba(120, 53, 15, 0.22);
            color: #fef3c7;
            font-size: 0.92rem;
            line-height: 1.65;
        }

        .admin-billing-body {
            padding: 1.5rem;
        }

        .admin-billing-stats {
            display: grid;
            gap: 1rem;
        }

        @media (min-width: 880px) {
            .admin-billing-stats {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        .admin-billing-stat {
            padding: 1rem 1.05rem;
            border: 1px solid #e7e5e4;
            border-radius: 1.25rem;
            background: #fafaf9;
        }

        .admin-billing-statLabel {
            margin: 0;
            font-size: 0.74rem;
            font-weight: 800;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: #78716c;
        }

        .admin-billing-statValue {
            margin: 0.65rem 0 0;
            font-size: 2rem;
            font-weight: 800;
            line-height: 1;
            color: #0f172a;
        }

        .admin-billing-stat.is-success {
            border-color: #bbf7d0;
            background: #f0fdf4;
        }

        .admin-billing-stat.is-warning {
            border-color: #fde68a;
            background: #fffbeb;
        }

        .admin-billing-stat.is-danger {
            border-color: #fecaca;
            background: #fef2f2;
        }

        .admin-billing-sectionTitle {
            margin: 1.5rem 0 0;
            font-size: 1rem;
            font-weight: 800;
            color: #0f172a;
        }

        .admin-billing-list {
            display: grid;
            gap: 0.9rem;
            margin-top: 1rem;
        }

        .admin-billing-item {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            padding: 1.05rem 1.1rem;
            border: 1px solid #e7e5e4;
            border-radius: 1.2rem;
            background: #ffffff;
        }

        @media (min-width: 920px) {
            .admin-billing-item {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }

        .admin-billing-name {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: #0f172a;
        }

        .admin-billing-mail,
        .admin-billing-meta {
            margin: 0.3rem 0 0;
            font-size: 0.92rem;
            color: #475569;
        }

        .admin-billing-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
            align-items: center;
        }

        .admin-billing-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.42rem 0.8rem;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .admin-billing-badge.is-success {
            background: #ecfdf5;
            color: #047857;
        }

        .admin-billing-badge.is-warning {
            background: #fffbeb;
            color: #b45309;
        }

        .admin-billing-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2.75rem;
            padding: 0.72rem 1rem;
            border-radius: 999px;
            background: #1f3a32;
            color: white;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 700;
            box-shadow: 0 14px 30px rgba(31, 58, 50, 0.14);
            transition: transform 180ms ease;
        }

        .admin-billing-btn:hover {
            transform: translateY(-1px);
        }

        .admin-billing-empty {
            margin-top: 1rem;
            padding: 1rem 1.05rem;
            border: 1px solid #bbf7d0;
            border-radius: 1rem;
            background: #f0fdf4;
            color: #047857;
            font-size: 0.94rem;
        }

        .dark .admin-billing-card {
            border-color: rgba(251, 191, 36, 0.14);
            background: rgba(30, 23, 15, 0.92);
            box-shadow: 0 22px 60px rgba(2, 6, 23, 0.34);
        }

        .dark .admin-billing-warning {
            border-color: rgba(251, 191, 36, 0.36);
            background: rgba(120, 53, 15, 0.3);
            color: #fde68a;
        }

        .dark .admin-billing-stat,
        .dark .admin-billing-item {
            border-color: rgba(120, 53, 15, 0.35);
            background: rgba(41, 28, 15, 0.72);
        }

        .dark .admin-billing-stat.is-success {
            background: rgba(6, 78, 59, 0.28);
            border-color: rgba(16, 185, 129, 0.3);
        }

        .dark .admin-billing-stat.is-warning {
            background: rgba(120, 53, 15, 0.35);
            border-color: rgba(251, 191, 36, 0.32);
        }

        .dark .admin-billing-stat.is-danger {
            background: rgba(127, 29, 29, 0.28);
            border-color: rgba(248, 113, 113, 0.3);
        }

        .dark .admin-billing-statLabel {
            color: #fcd34d;
        }

        .dark .admin-billing-statValue,
        .dark .admin-billing-sectionTitle,
        .dark .admin-billing-name {
            color: #fff7ed;
        }

        .dark .admin-billing-mail,
        .dark .admin-billing-meta {
            color: #fed7aa;
        }

        .dark .admin-billing-badge.is-success {
            background: rgba(6, 78, 59, 0.34);
            color: #a7f3d0;
        }

        .dark .admin-billing-badge.is-warning {
            background: rgba(120, 53, 15, 0.38);
            color: #fde68a;
        }

        .dark .admin-billing-empty {
            border-color: rgba(16, 185, 129, 0.35);
            background: rgba(6, 78, 59, 0.3);
            color: #a7f3d0;
        }
    </style>

    <section class="admin-billing-card">
        <div class="admin-billing-hero">
            <p class="admin-billing-eyebrow">{{ __('admin.billing.admin_eyebrow') }}</p>
            <div class="admin-billing-top">
                <div>
                    <h3 class="admin-billing-title">{{ __('admin.billing.admin_title') }}</h3>
                    <p class="admin-billing-copy">{{ __('admin.billing.admin_description') }}</p>
                </div>
                <a href="{{ $usersUrl }}" class="admin-billing-link">{{ __('admin.billing.admin_manage_users') }}</a>
            </div>

            @unless ($setupReady)
                <div class="admin-billing-warning">
                    {{ __('admin.billing.setup_missing') }}
                </div>
            @endunless
        </div>

        <div class="admin-billing-body">
            <div class="admin-billing-stats">
                @foreach ($stats as $stat)
                    <article class="admin-billing-stat is-{{ $stat['tone'] }}">
                        <p class="admin-billing-statLabel">{{ $stat['label'] }}</p>
                        <p class="admin-billing-statValue">{{ $stat['value'] }}</p>
                    </article>
                @endforeach
            </div>

            <h4 class="admin-billing-sectionTitle">{{ __('admin.billing.admin_recent_title') }}</h4>

            @if ($recentOwners->isEmpty())
                <div class="admin-billing-empty">
                    {{ __('admin.billing.admin_recent_empty') }}
                </div>
            @else
                <div class="admin-billing-list">
                    @foreach ($recentOwners as $owner)
                        @php
                            $latestTransaction = $owner->transactions->first();
                        @endphp

                        <div class="admin-billing-item">
                            <div>
                                <p class="admin-billing-name">{{ $owner->name }}</p>
                                <p class="admin-billing-mail">{{ $owner->email }}</p>
                                <p class="admin-billing-meta">
                                    {{ __('admin.billing.admin_recent_meta') }}
                                    {{ $latestTransaction?->billed_at?->format('d.m.Y H:i') ?? __('admin.billing.admin_recent_no_payment') }}
                                </p>
                            </div>

                            <div class="admin-billing-badges">
                                <span class="admin-billing-badge is-{{ $owner->hasPublishingSubscription() ? 'success' : 'warning' }}">
                                    {{ $owner->publishingAccessLabel() }}
                                </span>
                                <a href="{{ \App\Filament\Resources\UserResource::getUrl('edit', ['record' => $owner], panel: 'admin') }}" class="admin-billing-btn">
                                    {{ __('admin.billing.admin_open_user') }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</x-filament-widgets::widget>

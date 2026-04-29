<x-filament-widgets::widget>
    <style>
        .admin-pending-card {
            overflow: hidden;
            border: 1px solid rgba(226, 232, 240, 0.92);
            border-radius: 1.75rem;
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 22px 60px rgba(15, 23, 42, 0.08);
        }

        .admin-pending-hero {
            padding: 1.75rem;
            color: white;
            background:
                radial-gradient(circle at top right, rgba(251, 191, 36, 0.18), transparent 32%),
                linear-gradient(135deg, #1f3a32, #2b4a40 62%, #39594d);
        }

        .admin-pending-eyebrow {
            margin: 0;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.32em;
            text-transform: uppercase;
            color: #fde68a;
        }

        .admin-pending-title {
            margin: 0.8rem 0 0;
            font-size: 1.8rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .admin-pending-copy {
            margin: 0.7rem 0 0;
            max-width: 42rem;
            font-size: 0.95rem;
            line-height: 1.75;
            color: rgba(255, 255, 255, 0.8);
        }

        .admin-pending-body {
            padding: 1.5rem;
        }

        .admin-pending-empty {
            padding: 1rem 1.15rem;
            border: 1px solid #a7f3d0;
            border-radius: 1.2rem;
            background: #ecfdf5;
            color: #047857;
            font-size: 0.94rem;
        }

        .admin-pending-list {
            display: grid;
            gap: 0.9rem;
        }

        .admin-pending-item {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            padding: 1.1rem 1.2rem;
            border: 1px solid #e7e5e4;
            border-radius: 1.25rem;
            background: #fafaf9;
        }

        @media (min-width: 768px) {
            .admin-pending-item {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
        }

        .admin-pending-name {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: #0f172a;
        }

        .admin-pending-mail {
            margin: 0.25rem 0 0;
            font-size: 0.94rem;
            color: #475569;
        }

        .admin-pending-meta {
            margin: 0.55rem 0 0;
            font-size: 0.74rem;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #78716c;
        }

        .admin-pending-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.42rem 0.8rem;
            border-radius: 999px;
            border: 1px solid #bae6fd;
            background: #eff6ff;
            color: #0369a1;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .admin-pending-btn,
        .admin-pending-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 700;
            transition: transform 180ms ease, box-shadow 180ms ease, background-color 180ms ease;
        }

        .admin-pending-btn:hover,
        .admin-pending-link:hover {
            transform: translateY(-1px);
        }

        .admin-pending-btn {
            min-height: 2.75rem;
            padding: 0.7rem 1.05rem;
            background: #1f3a32;
            color: white;
            box-shadow: 0 14px 30px rgba(31, 58, 50, 0.14);
            font-size: 0.9rem;
        }

        .admin-pending-footer {
            margin-top: 1rem;
        }

        .admin-pending-link {
            padding: 0.7rem 1rem;
            border: 1px solid rgba(31, 58, 50, 0.12);
            background: rgba(31, 58, 50, 0.05);
            color: #1f3a32;
            font-size: 0.9rem;
        }

        .dark .admin-pending-card {
            border-color: rgba(251, 191, 36, 0.14);
            background: rgba(30, 23, 15, 0.92);
            box-shadow: 0 22px 60px rgba(2, 6, 23, 0.34);
        }

        .dark .admin-pending-empty {
            border-color: rgba(16, 185, 129, 0.35);
            background: rgba(6, 78, 59, 0.3);
            color: #a7f3d0;
        }

        .dark .admin-pending-item {
            border-color: rgba(120, 53, 15, 0.35);
            background: rgba(41, 28, 15, 0.72);
        }

        .dark .admin-pending-name {
            color: #fff7ed;
        }

        .dark .admin-pending-mail {
            color: #fed7aa;
        }

        .dark .admin-pending-meta {
            color: #fcd34d;
        }

        .dark .admin-pending-badge {
            border-color: rgba(56, 189, 248, 0.35);
            background: rgba(8, 47, 73, 0.58);
            color: #7dd3fc;
        }

        .dark .admin-pending-link {
            border-color: rgba(251, 191, 36, 0.24);
            background: rgba(251, 191, 36, 0.1);
            color: #fde68a;
        }
    </style>

    <section class="admin-pending-card">
        <div class="admin-pending-hero">
            <p class="admin-pending-eyebrow">{{ __('admin.pending.eyebrow') }}</p>
            <h3 class="admin-pending-title">{{ __('admin.pending.title') }}</h3>
            <p class="admin-pending-copy">{{ __('admin.pending.description') }}</p>
        </div>

        <div class="admin-pending-body">
            @if ($pendingUsers->isEmpty())
                <div class="admin-pending-empty">
                    {{ __('admin.pending.empty') }}
                </div>
            @else
                <div class="admin-pending-list">
                    @foreach ($pendingUsers as $user)
                        <div class="admin-pending-item">
                            <div>
                                <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem;">
                                    <p class="admin-pending-name">{{ $user->name }}</p>
                                    @if ($user->isDemoAccount())
                                        <span class="admin-pending-badge">Demo ACC</span>
                                    @endif
                                </div>
                                <p class="admin-pending-mail">{{ $user->email }}</p>
                                <p class="admin-pending-meta">
                                    {{ __('admin.pending.registered') }} {{ $user->created_at?->format('d.m.Y H:i') }}
                                </p>
                            </div>

                            <a href="{{ \App\Filament\Resources\UserResource::getUrl('edit', ['record' => $user], panel: 'admin') }}" class="admin-pending-btn">
                                {{ __('admin.pending.open_user') }}
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="admin-pending-footer">
                    <a href="{{ $usersUrl }}" class="admin-pending-link">
                        {{ __('admin.pending.all_users') }}
                    </a>
                </div>
            @endif
        </div>
    </section>
</x-filament-widgets::widget>

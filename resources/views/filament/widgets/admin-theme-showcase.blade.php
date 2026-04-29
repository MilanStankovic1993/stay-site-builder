<x-filament-widgets::widget>
    <style>
        .admin-theme-card {
            overflow: hidden;
            border: 1px solid rgba(226, 232, 240, 0.92);
            border-radius: 1.75rem;
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 22px 60px rgba(15, 23, 42, 0.08);
        }

        .admin-theme-head {
            padding: 1.6rem 1.6rem 1.2rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .admin-theme-eyebrow {
            margin: 0;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.32em;
            text-transform: uppercase;
            color: #78716c;
        }

        .admin-theme-headRow {
            margin-top: 0.85rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        @media (min-width: 768px) {
            .admin-theme-headRow {
                flex-direction: row;
                align-items: end;
                justify-content: space-between;
            }
        }

        .admin-theme-title {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 800;
            color: #0f172a;
        }

        .admin-theme-copy {
            margin: 0.55rem 0 0;
            max-width: 42rem;
            font-size: 0.95rem;
            line-height: 1.75;
            color: #475569;
        }

        .admin-theme-link,
        .admin-theme-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 700;
            transition: transform 180ms ease, box-shadow 180ms ease, background-color 180ms ease;
        }

        .admin-theme-link:hover,
        .admin-theme-btn:hover {
            transform: translateY(-1px);
        }

        .admin-theme-link {
            padding: 0.7rem 1rem;
            border: 1px solid rgba(31, 58, 50, 0.12);
            background: rgba(31, 58, 50, 0.05);
            color: #1f3a32;
            font-size: 0.9rem;
        }

        .admin-theme-grid {
            display: grid;
            gap: 1rem;
            padding: 1.5rem;
        }

        @media (min-width: 1100px) {
            .admin-theme-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        .admin-theme-item {
            padding: 1rem;
            border: 1px solid #e7e5e4;
            border-radius: 1.45rem;
            background: #fafaf9;
        }

        .admin-theme-preview {
            height: 9rem;
            border-radius: 1.15rem;
            background-size: cover;
            background-position: center;
        }

        .admin-theme-row {
            margin-top: 1rem;
            display: flex;
            justify-content: space-between;
            gap: 0.8rem;
            align-items: start;
        }

        .admin-theme-key {
            margin: 0;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.28em;
            text-transform: uppercase;
            color: #78716c;
        }

        .admin-theme-name {
            margin: 0.45rem 0 0;
            font-size: 1.5rem;
            font-weight: 800;
            color: #0f172a;
        }

        .admin-theme-status {
            display: inline-flex;
            align-items: center;
            padding: 0.42rem 0.8rem;
            border-radius: 999px;
            font-size: 0.68rem;
            font-weight: 800;
            letter-spacing: 0.18em;
            text-transform: uppercase;
        }

        .admin-theme-status.is-active {
            background: #ecfdf5;
            color: #047857;
        }

        .admin-theme-status.is-inactive {
            background: #e7e5e4;
            color: #57534e;
        }

        .admin-theme-desc {
            margin: 0.7rem 0 0;
            font-size: 0.92rem;
            line-height: 1.65;
            color: #475569;
        }

        .admin-theme-actions {
            margin-top: 1rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .admin-theme-btn {
            min-height: 2.75rem;
            padding: 0.7rem 1rem;
            font-size: 0.9rem;
        }

        .admin-theme-btn--primary {
            background: #1f3a32;
            color: white;
            box-shadow: 0 14px 30px rgba(31, 58, 50, 0.14);
        }

        .admin-theme-btn--secondary {
            background: white;
            border: 1px solid #d6d3d1;
            color: #334155;
        }

        .dark .admin-theme-card {
            border-color: rgba(251, 191, 36, 0.14);
            background: rgba(30, 23, 15, 0.92);
            box-shadow: 0 22px 60px rgba(2, 6, 23, 0.34);
        }

        .dark .admin-theme-head {
            border-bottom-color: rgba(120, 53, 15, 0.28);
        }

        .dark .admin-theme-eyebrow {
            color: #fcd34d;
        }

        .dark .admin-theme-title {
            color: #fff7ed;
        }

        .dark .admin-theme-copy,
        .dark .admin-theme-desc {
            color: #fed7aa;
        }

        .dark .admin-theme-link {
            border-color: rgba(251, 191, 36, 0.24);
            background: rgba(251, 191, 36, 0.1);
            color: #fde68a;
        }

        .dark .admin-theme-item {
            border-color: rgba(120, 53, 15, 0.35);
            background: rgba(41, 28, 15, 0.72);
        }

        .dark .admin-theme-key {
            color: #fcd34d;
        }

        .dark .admin-theme-name {
            color: #fff7ed;
        }

        .dark .admin-theme-status.is-active {
            background: rgba(6, 78, 59, 0.34);
            color: #a7f3d0;
        }

        .dark .admin-theme-status.is-inactive {
            background: rgba(51, 65, 85, 0.45);
            color: #cbd5e1;
        }

        .dark .admin-theme-btn--secondary {
            background: rgba(30, 41, 59, 0.88);
            border-color: rgba(71, 85, 105, 0.5);
            color: #e2e8f0;
        }
    </style>

    <section class="admin-theme-card">
        <div class="admin-theme-head">
            <p class="admin-theme-eyebrow">{{ __('admin.theme_showcase.eyebrow') }}</p>
            <div class="admin-theme-headRow">
                <div>
                    <h3 class="admin-theme-title">{{ __('admin.theme_showcase.title') }}</h3>
                    <p class="admin-theme-copy">{{ __('admin.theme_showcase.description') }}</p>
                </div>
                <a href="{{ $themesUrl }}" class="admin-theme-link">{{ __('admin.theme_showcase.manage') }}</a>
            </div>
        </div>

        <div class="admin-theme-grid">
            @foreach ($themes as $theme)
                <div class="admin-theme-item">
                    <div
                        class="admin-theme-preview"
                        style="background-image:
                            linear-gradient(135deg, rgba(24, 49, 41, 0.88), rgba(195, 164, 106, 0.38)),
                            url('{{ $theme->preview_image ?: '/demo/placeholders/gallery-lounge.svg' }}')"
                    ></div>

                    <div class="admin-theme-row">
                        <div>
                            <p class="admin-theme-key">{{ $theme->key }}</p>
                            <h4 class="admin-theme-name">{{ $theme->name }}</h4>
                        </div>
                        <span class="admin-theme-status {{ $theme->is_active ? 'is-active' : 'is-inactive' }}">
                            {{ $theme->is_active ? __('admin.theme_showcase.active') : __('admin.theme_showcase.inactive') }}
                        </span>
                    </div>

                    <p class="admin-theme-desc">{{ $theme->description }}</p>

                    <div class="admin-theme-actions">
                        <a href="{{ route('storefront.demo-theme', $theme->key) }}" target="_blank" class="admin-theme-btn admin-theme-btn--primary">
                            {{ __('admin.theme_showcase.preview') }}
                        </a>
                        <a href="{{ \App\Filament\Resources\ThemePresetResource::getUrl('edit', ['record' => $theme], panel: 'admin') }}" class="admin-theme-btn admin-theme-btn--secondary">
                            {{ __('admin.theme_showcase.edit') }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</x-filament-widgets::widget>

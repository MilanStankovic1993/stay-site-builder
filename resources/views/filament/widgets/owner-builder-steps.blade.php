<x-filament-widgets::widget>
    <style>
        .owner-builder-grid {
            display: grid;
            gap: 1.5rem;
        }

        @media (min-width: 1100px) {
            .owner-builder-grid {
                grid-template-columns: minmax(0, 1.08fr) minmax(320px, 0.92fr);
            }
        }

        .owner-builder-card {
            overflow: hidden;
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 1.75rem;
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 22px 60px rgba(15, 23, 42, 0.08);
        }

        .owner-builder-hero {
            padding: 1.75rem;
            color: white;
            background:
                radial-gradient(circle at top right, rgba(251, 191, 36, 0.18), transparent 32%),
                linear-gradient(135deg, #173329, #204638 62%, #2b5b49);
        }

        .owner-builder-eyebrow {
            margin: 0;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.32em;
            text-transform: uppercase;
            color: #fde68a;
        }

        .owner-builder-title {
            margin: 0.8rem 0 0;
            font-size: 1.8rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .owner-builder-copy {
            margin: 0.7rem 0 0;
            max-width: 40rem;
            font-size: 0.95rem;
            line-height: 1.75;
            color: rgba(255, 255, 255, 0.8);
        }

        .owner-builder-body {
            padding: 1.5rem;
            display: grid;
            gap: 1rem;
        }

        .owner-builder-alert {
            padding: 1rem 1.1rem;
            border: 1px solid #fcd34d;
            border-radius: 1.2rem;
            background: #fffbeb;
            color: #92400e;
        }

        .owner-builder-alert p {
            margin: 0;
            line-height: 1.65;
        }

        .owner-builder-alert p + p {
            margin-top: 0.35rem;
        }

        .owner-builder-step {
            display: grid;
            grid-template-columns: 3rem minmax(0, 1fr);
            gap: 1rem;
            align-items: start;
            padding: 1rem;
            border-radius: 1.35rem;
            border: 1px solid #e7e5e4;
            background: #fafaf9;
        }

        .owner-builder-step.is-done {
            border-color: #a7f3d0;
            background: linear-gradient(180deg, #ecfdf5, #f0fdf4);
        }

        .owner-builder-step__bullet {
            width: 3rem;
            height: 3rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: white;
            color: #64748b;
            font-weight: 800;
            box-shadow: inset 0 0 0 1px rgba(226, 232, 240, 0.95);
        }

        .owner-builder-step.is-done .owner-builder-step__bullet {
            background: #059669;
            color: white;
            box-shadow: none;
        }

        .owner-builder-step h4 {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: #0f172a;
        }

        .owner-builder-step p {
            margin: 0.35rem 0 0;
            font-size: 0.92rem;
            line-height: 1.65;
            color: #475569;
        }

        .owner-builder-side {
            display: grid;
            gap: 1rem;
        }

        .owner-builder-panel {
            padding: 1.5rem;
        }

        .owner-builder-panel__eyebrow {
            margin: 0;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.32em;
            text-transform: uppercase;
            color: #78716c;
        }

        .owner-builder-panel__title {
            margin: 0.8rem 0 0;
            font-size: 1.7rem;
            font-weight: 800;
            color: #0f172a;
        }

        .owner-builder-panel__text {
            margin: 0.6rem 0 0;
            font-size: 0.94rem;
            line-height: 1.7;
            color: #475569;
        }

        .owner-builder-actions,
        .owner-builder-links {
            margin-top: 1.15rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .owner-builder-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2.8rem;
            padding: 0.7rem 1.1rem;
            border-radius: 999px;
            border: 1px solid transparent;
            font-size: 0.9rem;
            font-weight: 700;
            text-decoration: none;
            transition: transform 180ms ease, box-shadow 180ms ease, background-color 180ms ease, border-color 180ms ease;
        }

        .owner-builder-btn:hover {
            transform: translateY(-1px);
        }

        .owner-builder-btn--primary {
            background: #173329;
            color: white;
            box-shadow: 0 14px 30px rgba(23, 51, 41, 0.16);
        }

        .owner-builder-btn--secondary {
            background: white;
            border-color: #d6d3d1;
            color: #334155;
        }

        .owner-builder-btn--success {
            background: #ecfdf5;
            border-color: #86efac;
            color: #047857;
        }

        .owner-builder-shortcut {
            display: block;
            padding: 1rem 1.05rem;
            border: 1px solid #e7e5e4;
            border-radius: 1.2rem;
            background: #fafaf9;
            color: #334155;
            text-decoration: none;
            font-size: 0.94rem;
            font-weight: 700;
            transition: transform 180ms ease, background-color 180ms ease, border-color 180ms ease;
        }

        .owner-builder-shortcut:hover {
            transform: translateY(-1px);
            background: white;
            border-color: #d6d3d1;
        }

        .owner-builder-demo {
            border-color: #bae6fd;
            background: linear-gradient(180deg, #f0f9ff, #e0f2fe);
        }

        .owner-builder-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
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

        .dark .owner-builder-card {
            border-color: rgba(45, 212, 191, 0.14);
            background: rgba(15, 23, 42, 0.9);
            box-shadow: 0 22px 60px rgba(2, 6, 23, 0.34);
        }

        .dark .owner-builder-alert {
            border-color: rgba(251, 191, 36, 0.35);
            background: rgba(120, 53, 15, 0.18);
            color: #fde68a;
        }

        .dark .owner-builder-step {
            border-color: rgba(71, 85, 105, 0.45);
            background: rgba(15, 23, 42, 0.72);
        }

        .dark .owner-builder-step.is-done {
            border-color: rgba(16, 185, 129, 0.34);
            background: linear-gradient(180deg, rgba(6, 78, 59, 0.52), rgba(6, 95, 70, 0.32));
        }

        .dark .owner-builder-step__bullet {
            background: rgba(30, 41, 59, 0.94);
            color: #cbd5e1;
            box-shadow: inset 0 0 0 1px rgba(71, 85, 105, 0.8);
        }

        .dark .owner-builder-step h4,
        .dark .owner-builder-panel__title {
            color: #f8fafc;
        }

        .dark .owner-builder-step p,
        .dark .owner-builder-panel__text {
            color: #cbd5e1;
        }

        .dark .owner-builder-panel__eyebrow {
            color: #99f6e4;
        }

        .dark .owner-builder-btn--secondary {
            background: rgba(15, 23, 42, 0.92);
            border-color: rgba(71, 85, 105, 0.55);
            color: #e2e8f0;
        }

        .dark .owner-builder-btn--success {
            background: rgba(6, 78, 59, 0.38);
            border-color: rgba(16, 185, 129, 0.45);
            color: #a7f3d0;
        }

        .dark .owner-builder-shortcut {
            border-color: rgba(71, 85, 105, 0.45);
            background: rgba(15, 23, 42, 0.72);
            color: #e2e8f0;
        }

        .dark .owner-builder-shortcut:hover {
            background: rgba(30, 41, 59, 0.9);
            border-color: rgba(94, 234, 212, 0.28);
        }

        .dark .owner-builder-demo {
            border-color: rgba(56, 189, 248, 0.32);
            background: linear-gradient(180deg, rgba(8, 47, 73, 0.68), rgba(14, 116, 144, 0.26));
        }

        .dark .owner-builder-badge {
            border-color: rgba(56, 189, 248, 0.35);
            background: rgba(8, 47, 73, 0.58);
            color: #7dd3fc;
        }
    </style>

    <div class="owner-builder-grid">
        <section class="owner-builder-card">
            <div class="owner-builder-hero">
                <p class="owner-builder-eyebrow">{{ __('admin.builder.eyebrow') }}</p>
                <h3 class="owner-builder-title">{{ __('admin.builder.title') }}</h3>
                <p class="owner-builder-copy">{{ __('admin.builder.description') }}</p>
            </div>

            <div class="owner-builder-body">
                @if (! $canPublish)
                    <div class="owner-builder-alert">
                        <p><strong>{{ __('admin.builder.publish_locked_title') }}</strong></p>
                        <p>{{ __('admin.builder.publish_locked_text') }}</p>
                    </div>
                @endif

                @foreach ($steps as $index => $step)
                    <div class="owner-builder-step {{ $step['done'] ? 'is-done' : '' }}">
                        <div class="owner-builder-step__bullet">
                            @if ($step['done'])
                                <span>OK</span>
                            @else
                                <span>{{ $index + 1 }}</span>
                            @endif
                        </div>
                        <div>
                            <h4>{{ $step['title'] }}</h4>
                            <p>{{ $step['description'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="owner-builder-side">
            <div class="owner-builder-card owner-builder-panel">
                <p class="owner-builder-panel__eyebrow">{{ __('admin.builder.next_action') }}</p>
                @if ($accommodation)
                    <div class="owner-builder-actions" style="margin-top: 0.8rem; align-items: center;">
                        <h3 class="owner-builder-panel__title" style="margin: 0;">{{ $accommodation->display_title }}</h3>
                        @if ($accommodation->isDemoAccommodation())
                            <span class="owner-builder-badge">{{ __('admin.builder.demo_badge') }}</span>
                        @endif
                    </div>
                    <p class="owner-builder-panel__text">
                        {{
                            $accommodation->status === \App\Enums\AccommodationStatus::Published
                                ? __('admin.builder.published_text')
                                : ($canPublish
                                    ? __('admin.builder.draft_ready_text')
                                    : __('admin.builder.draft_waiting_text'))
                        }}
                    </p>

                    <div class="owner-builder-actions">
                        <a href="{{ $accommodation->previewUrl() }}" target="_blank" class="owner-builder-btn owner-builder-btn--primary">
                            {{ __('admin.builder.preview') }}
                        </a>
                        <a href="{{ \App\Filament\Resources\AccommodationResource::getUrl('edit', ['record' => $accommodation], panel: 'dashboard') }}" class="owner-builder-btn owner-builder-btn--secondary">
                            {{ __('admin.builder.edit') }}
                        </a>
                        @if ($accommodation->status === \App\Enums\AccommodationStatus::Published)
                            <a href="{{ $accommodation->publicUrl() }}" target="_blank" class="owner-builder-btn owner-builder-btn--success">
                                {{ __('admin.builder.open_live') }}
                            </a>
                        @endif
                    </div>
                @else
                    <h3 class="owner-builder-panel__title">{{ __('admin.builder.start_title') }}</h3>
                    <p class="owner-builder-panel__text">{{ __('admin.builder.start_text') }}</p>

                    <div class="owner-builder-actions">
                        <a href="{{ $createUrl }}" class="owner-builder-btn owner-builder-btn--primary">
                            {{ __('admin.builder.add') }}
                        </a>
                    </div>
                @endif
            </div>

            <div class="owner-builder-card owner-builder-panel">
                <p class="owner-builder-panel__eyebrow">{{ __('admin.builder.shortcuts') }}</p>
                <div class="owner-builder-links">
                    <a href="{{ $manageUrl }}" class="owner-builder-shortcut">{{ __('admin.builder.manage') }}</a>
                    <a href="{{ $inquiriesUrl }}" class="owner-builder-shortcut">{{ __('admin.builder.browse_inquiries') }}</a>
                </div>
            </div>

            @if ($user?->isDemoAccount())
                <div class="owner-builder-card owner-builder-panel owner-builder-demo">
                    <span class="owner-builder-badge">{{ __('admin.builder.demo_badge') }}</span>
                    <p class="owner-builder-panel__text">{{ __('admin.builder.demo_text') }}</p>
                </div>
            @endif
        </section>
    </div>
</x-filament-widgets::widget>

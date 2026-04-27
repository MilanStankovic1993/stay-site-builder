<x-filament-widgets::widget>
    <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
        <section class="overflow-hidden rounded-[1.75rem] border border-stone-200 bg-white shadow-sm">
            <div class="border-b border-stone-100 bg-[linear-gradient(135deg,_#173329,_#204638)] px-6 py-6 text-white">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200">{{ __('admin.builder.eyebrow') }}</p>
                <h3 class="mt-3 text-2xl font-semibold">{{ __('admin.builder.title') }}</h3>
                <p class="mt-2 max-w-2xl text-sm leading-7 text-white/75">
                    {{ __('admin.builder.description') }}
                </p>
            </div>

            <div class="space-y-4 p-6">
                @if (! $canPublish)
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4 text-sm text-amber-900">
                        <p class="font-semibold">{{ __('admin.builder.publish_locked_title') }}</p>
                        <p class="mt-1 leading-6">
                            {{ __('admin.builder.publish_locked_text') }}
                        </p>
                    </div>
                @endif

                @foreach ($steps as $index => $step)
                    <div class="flex gap-4 rounded-2xl border px-4 py-4 {{ $step['done'] ? 'border-emerald-200 bg-emerald-50' : 'border-stone-200 bg-stone-50' }}">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $step['done'] ? 'bg-emerald-600 text-white' : 'bg-white text-stone-500' }}">
                            @if ($step['done'])
                                <span class="text-sm font-bold">&#10003;</span>
                            @else
                                <span class="text-sm font-bold">{{ $index + 1 }}</span>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-base font-semibold text-slate-900">{{ $step['title'] }}</h4>
                            <p class="mt-1 text-sm leading-6 text-slate-600">{{ $step['description'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="space-y-4">
            <div class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-500">{{ __('admin.builder.next_action') }}</p>
                @if ($accommodation)
                    <div class="mt-3 flex flex-wrap items-center gap-3">
                        <h3 class="text-2xl font-semibold text-slate-900">{{ $accommodation->display_title }}</h3>
                        @if ($accommodation->isDemoAccommodation())
                            <span class="rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-sky-700">
                                {{ __('admin.builder.demo_badge') }}
                            </span>
                        @endif
                    </div>
                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        {{
                            $accommodation->status === \App\Enums\AccommodationStatus::Published
                                ? __('admin.builder.published_text')
                                : ($canPublish
                                    ? __('admin.builder.draft_ready_text')
                                    : __('admin.builder.draft_waiting_text'))
                        }}
                    </p>

                    <div class="mt-5 flex flex-wrap gap-3">
                        <a href="{{ $accommodation->previewUrl() }}" target="_blank" class="rounded-full bg-[#173329] px-4 py-2 text-sm font-semibold text-white">
                            {{ __('admin.builder.preview') }}
                        </a>
                        <a href="{{ \App\Filament\Resources\AccommodationResource::getUrl('edit', ['record' => $accommodation], panel: 'dashboard') }}" class="rounded-full border border-stone-300 px-4 py-2 text-sm font-semibold text-slate-700">
                            {{ __('admin.builder.edit') }}
                        </a>
                        @if ($accommodation->status === \App\Enums\AccommodationStatus::Published)
                            <a href="{{ $accommodation->publicUrl() }}" target="_blank" class="rounded-full border border-emerald-300 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700">
                                {{ __('admin.builder.open_live') }}
                            </a>
                        @endif
                    </div>
                @else
                    <h3 class="mt-3 text-2xl font-semibold text-slate-900">{{ __('admin.builder.start_title') }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        {{ __('admin.builder.start_text') }}
                    </p>

                    <div class="mt-5">
                        <a href="{{ $createUrl }}" class="rounded-full bg-[#173329] px-4 py-2 text-sm font-semibold text-white">
                            {{ __('admin.builder.add') }}
                        </a>
                    </div>
                @endif
            </div>

            <div class="rounded-[1.75rem] border border-stone-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-500">{{ __('admin.builder.shortcuts') }}</p>
                <div class="mt-4 grid gap-3">
                    <a href="{{ $manageUrl }}" class="rounded-2xl border border-stone-200 px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-stone-50">
                        {{ __('admin.builder.manage') }}
                    </a>
                    <a href="{{ $inquiriesUrl }}" class="rounded-2xl border border-stone-200 px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-stone-50">
                        {{ __('admin.builder.browse_inquiries') }}
                    </a>
                </div>
            </div>

            @if ($user?->isDemoAccount())
                <div class="rounded-[1.75rem] border border-sky-200 bg-sky-50 p-6 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-sky-700">{{ __('admin.builder.demo_badge') }}</p>
                    <p class="mt-3 text-sm leading-6 text-sky-900">
                        {{ __('admin.builder.demo_text') }}
                    </p>
                </div>
            @endif
        </section>
    </div>
</x-filament-widgets::widget>

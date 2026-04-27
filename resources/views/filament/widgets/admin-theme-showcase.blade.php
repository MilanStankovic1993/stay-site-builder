<x-filament-widgets::widget>
    <section class="overflow-hidden rounded-[1.75rem] border border-stone-200 bg-white shadow-sm">
        <div class="border-b border-stone-100 px-6 py-6">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-500">{{ __('admin.theme_showcase.eyebrow') }}</p>
            <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h3 class="text-2xl font-semibold text-slate-900">{{ __('admin.theme_showcase.title') }}</h3>
                    <p class="mt-2 max-w-2xl text-sm leading-7 text-slate-600">
                        {{ __('admin.theme_showcase.description') }}
                    </p>
                </div>
                <a href="{{ $themesUrl }}" class="text-sm font-semibold text-[#1f3a32]">
                    {{ __('admin.theme_showcase.manage') }}
                </a>
            </div>
        </div>

        <div class="grid gap-4 p-6 lg:grid-cols-3">
            @foreach ($themes as $theme)
                <div class="rounded-[1.6rem] border border-stone-200 bg-stone-50 p-4">
                    <div
                        class="h-36 rounded-[1.25rem] bg-cover bg-center"
                        style="background-image:
                            linear-gradient(135deg, rgba(24, 49, 41, 0.88), rgba(195, 164, 106, 0.38)),
                            url('{{ $theme->preview_image ?: '/demo/placeholders/hero-villa.svg' }}')"
                    ></div>

                    <div class="mt-4 flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs uppercase tracking-[0.28em] text-stone-500">{{ $theme->key }}</p>
                            <h4 class="mt-2 text-2xl font-semibold text-slate-900">{{ $theme->name }}</h4>
                        </div>
                        <span class="rounded-full px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] {{ $theme->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-stone-200 text-stone-600' }}">
                            {{ $theme->is_active ? __('admin.theme_showcase.active') : __('admin.theme_showcase.inactive') }}
                        </span>
                    </div>

                    <p class="mt-3 text-sm leading-6 text-slate-600">{{ $theme->description }}</p>

                    <div class="mt-5 flex flex-wrap gap-3">
                        <a
                            href="{{ route('storefront.demo-theme', $theme->key) }}"
                            target="_blank"
                            class="rounded-full bg-[#1f3a32] px-4 py-2 text-sm font-semibold text-white"
                        >
                            {{ __('admin.theme_showcase.preview') }}
                        </a>
                        <a
                            href="{{ \App\Filament\Resources\ThemePresetResource::getUrl('edit', ['record' => $theme], panel: 'admin') }}"
                            class="rounded-full border border-stone-300 px-4 py-2 text-sm font-semibold text-slate-700"
                        >
                            {{ __('admin.theme_showcase.edit') }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</x-filament-widgets::widget>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --site-primary: {{ $accommodation->primary_color ?: '#234136' }};
            --site-secondary: {{ $accommodation->secondary_color ?: '#c6a66b' }};
            --site-cream: #f5efe3;
            --site-ink: #14211d;
        }
    </style>
</head>
<body class="bg-[var(--site-cream)] text-slate-900">
    @php
        $siteTitle = $accommodation->display_title;
        $siteShortDescription = $accommodation->display_short_description;
        $siteDescription = $accommodation->display_description ?: $siteShortDescription;
        $siteLocationName = $accommodation->display_location_name ?: $accommodation->display_city;
        $siteCity = $accommodation->display_city;
        $siteAddressLine = collect([
            $accommodation->display_address,
            $accommodation->display_city,
            $accommodation->display_region,
            $accommodation->display_country,
        ])->filter()->join(', ');
        $externalLinks = collect([
            ['label' => 'Instagram', 'url' => $accommodation->instagram_url],
            ['label' => 'Facebook', 'url' => $accommodation->facebook_url],
            ['label' => 'Booking', 'url' => $accommodation->booking_url],
            ['label' => 'Airbnb', 'url' => $accommodation->airbnb_url],
            ['label' => 'Website', 'url' => $accommodation->website_url],
            ['label' => 'Viber', 'url' => $accommodation->viber_number ? 'viber://chat?number=' . preg_replace('/\D+/', '', $accommodation->viber_number) : null],
        ])->filter(fn (array $link): bool => filled($link['url']));
        $gallery = $accommodation->getMedia('gallery');
        $galleryFallback = [
            asset('demo/placeholders/gallery-lounge.svg'),
            asset('demo/placeholders/gallery-bedroom.svg'),
            asset('demo/placeholders/gallery-wellness.svg'),
        ];
    @endphp
    <div class="storefront-shell bg-[linear-gradient(180deg,_#10211b_0%,_#18362c_38%,_#efe6d7_38%,_#f7f2ea_100%)]">
        @if ($isThemeDemo ?? false)
            <div class="sticky top-0 z-50 border-b border-emerald-200 bg-emerald-50/95 backdrop-blur">
                <div class="mx-auto max-w-7xl px-6 py-3 text-sm font-medium text-emerald-900 sm:px-8 lg:px-10">
                    {{ __('site.storefront.demo_banner_default') }}
                </div>
            </div>
        @elseif ($isPreview ?? false)
            <div class="sticky top-0 z-50 border-b border-amber-200 bg-amber-50/95 backdrop-blur">
                <div class="mx-auto max-w-7xl px-6 py-3 text-sm font-medium text-amber-900 sm:px-8 lg:px-10">
                    {{ __('site.storefront.preview_banner_long') }}
                </div>
            </div>
        @endif

        <header class="relative isolate overflow-hidden">
            <div class="absolute inset-0 bg-black/28"></div>
            <div class="absolute inset-0 bg-[linear-gradient(180deg,_rgba(5,12,10,0.08),_rgba(5,12,10,0.62)_72%,_rgba(5,12,10,0.82)_100%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(198,166,107,0.30),_transparent_34%)]"></div>
            @if ($accommodation->hero_image_url)
                <img src="{{ $accommodation->hero_image_url }}" alt="{{ $siteTitle }}" class="absolute inset-0 h-full w-full object-cover">
            @else
                <img src="{{ $galleryFallback[0] }}" alt="{{ $siteTitle }}" class="absolute inset-0 h-full w-full object-cover">
            @endif

            <div class="relative mx-auto flex min-h-[80svh] max-w-7xl flex-col justify-between px-6 pb-12 pt-8 sm:px-8 lg:px-10">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        @if ($accommodation->logo_url)
                            <img src="{{ $accommodation->logo_url }}" alt="{{ $siteTitle }} logo" class="h-14 w-auto rounded-2xl bg-white/92 p-2 shadow-[0_20px_60px_rgba(0,0,0,0.18)]">
                        @endif
                        <a href="{{ route('home') }}" class="text-xs font-semibold uppercase tracking-[0.4em] text-white/76">{{ __('site.brand') }}</a>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="rounded-full border border-white/15 bg-white/7 px-4 py-2 text-xs uppercase tracking-[0.28em] text-[var(--site-secondary)] backdrop-blur">
                            {{ __('site.storefront.theme_default_name') }}
                        </div>
                        @include('shared.locale-switcher', ['tone' => 'dark'])
                    </div>
                </div>

                <div class="grid gap-10 pb-4 pt-12 lg:grid-cols-[1.15fr_0.85fr] lg:items-end">
                    <div class="max-w-4xl rounded-[2.2rem] border border-white/12 bg-black/28 p-8 shadow-[0_30px_100px_rgba(0,0,0,0.20)] backdrop-blur-md sm:p-10">
                        <p class="text-sm font-semibold uppercase tracking-[0.35em] text-[var(--site-secondary)]">{{ $accommodation->type?->label() }}</p>
                        <h1 class="mt-4 font-serif text-5xl leading-[0.92] text-white sm:text-6xl lg:text-[5.1rem]">{{ $siteTitle }}</h1>
                        <p class="mt-5 text-lg font-medium text-white/88">{{ $siteLocationName }}</p>
                        @if ($siteShortDescription)
                            <p class="mt-6 max-w-2xl text-base leading-8 text-white/84 sm:text-lg">{{ $siteShortDescription }}</p>
                        @endif
                        <div class="mt-8 flex flex-wrap gap-3">
                            <a href="#upit" class="storefront-button-secondary">{{ __('site.storefront.send_inquiry') }}</a>
                            @if ($accommodation->whatsapp_number)
                                <a href="https://wa.me/{{ preg_replace('/\D+/', '', $accommodation->whatsapp_number) }}" class="inline-flex items-center justify-center rounded-full border border-white/24 bg-white/8 px-6 py-3 text-sm font-semibold text-white transition duration-200 hover:-translate-y-0.5 hover:bg-white/14">WhatsApp</a>
                            @endif
                        </div>
                    </div>

                    <div class="storefront-panel-dark p-7 text-white">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-[1.5rem] border border-white/10 bg-black/12 p-5">
                                <p class="text-xs uppercase tracking-[0.25em] text-white/55">{{ __('site.storefront.guests') }}</p>
                                <p class="mt-3 text-3xl font-semibold text-white">{{ $accommodation->max_guests ?: '-' }}</p>
                            </div>
                            <div class="rounded-[1.5rem] border border-white/10 bg-black/12 p-5">
                                <p class="text-xs uppercase tracking-[0.25em] text-white/55">{{ __('site.storefront.bedrooms') }}</p>
                                <p class="mt-3 text-3xl font-semibold text-white">{{ $accommodation->bedrooms ?: '-' }}</p>
                            </div>
                            <div class="rounded-[1.5rem] border border-white/10 bg-black/12 p-5">
                                <p class="text-xs uppercase tracking-[0.25em] text-white/55">{{ __('site.storefront.bathrooms') }}</p>
                                <p class="mt-3 text-3xl font-semibold text-white">{{ $accommodation->bathrooms ?: '-' }}</p>
                            </div>
                            <div class="rounded-[1.5rem] border border-white/10 bg-black/12 p-5">
                                <p class="text-xs uppercase tracking-[0.25em] text-white/55">{{ __('site.storefront.price_from') }}</p>
                                <p class="mt-3 text-3xl font-semibold text-white">{{ $accommodation->price_from ? number_format((float) $accommodation->price_from, 0, ',', '.') . ' ' . $accommodation->currency : __('site.storefront.on_request') }}</p>
                            </div>
                        </div>
                        <div class="mt-5 rounded-[1.5rem] border border-white/10 bg-black/12 p-5 text-sm text-white/78">
                            <p class="storefront-eyebrow text-xs">{{ __('site.storefront.location') }}</p>
                            <p class="mt-3 text-base font-semibold text-white">{{ $siteAddressLine ?: ($siteCity ?: $siteLocationName) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-6 pb-20 sm:px-8 lg:px-10">
            @if (session('status'))
                <div class="-mt-8 mb-8 rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800 shadow-[0_18px_55px_rgba(5,150,105,0.10)]">
                    {{ session('status') }}
                </div>
            @endif

            <section class="grid gap-4 rounded-[2rem] border border-white/70 bg-white/82 p-6 shadow-[0_24px_80px_rgba(15,23,42,0.08)] backdrop-blur sm:grid-cols-2 lg:grid-cols-5 lg:p-8">
                <div><p class="text-xs uppercase tracking-[0.3em] text-stone-500">{{ __('site.storefront.guests') }}</p><p class="mt-2 text-2xl font-semibold text-[var(--site-ink)]">{{ $accommodation->max_guests ?: '-' }}</p></div>
                <div><p class="text-xs uppercase tracking-[0.3em] text-stone-500">{{ __('site.storefront.bedrooms') }}</p><p class="mt-2 text-2xl font-semibold text-[var(--site-ink)]">{{ $accommodation->bedrooms ?: '-' }}</p></div>
                <div><p class="text-xs uppercase tracking-[0.3em] text-stone-500">{{ __('site.storefront.bathrooms') }}</p><p class="mt-2 text-2xl font-semibold text-[var(--site-ink)]">{{ $accommodation->bathrooms ?: '-' }}</p></div>
                <div><p class="text-xs uppercase tracking-[0.3em] text-stone-500">{{ __('site.storefront.size') }}</p><p class="mt-2 text-2xl font-semibold text-[var(--site-ink)]">{{ $accommodation->size_m2 ? $accommodation->size_m2 . ' m2' : '-' }}</p></div>
                <div><p class="text-xs uppercase tracking-[0.3em] text-stone-500">{{ __('site.storefront.price_from') }}</p><p class="mt-2 text-2xl font-semibold text-[var(--site-ink)]">{{ $accommodation->price_from ? number_format((float) $accommodation->price_from, 0, ',', '.') . ' ' . $accommodation->currency : __('site.storefront.on_request') }}</p></div>
            </section>

            <div class="mt-16 grid gap-10 lg:grid-cols-[1.18fr_0.82fr]">
                <section class="space-y-16">
                    <div>
                        <p class="storefront-eyebrow">{{ __('site.storefront.description') }}</p>
                        <h2 class="mt-3 font-serif text-4xl text-white sm:text-5xl">{{ __('site.storefront.default_tagline') }}</h2>
                        <div class="mt-6 rounded-[2rem] border border-white/60 bg-white/86 p-8 text-base leading-8 text-slate-700 shadow-[0_24px_80px_rgba(15,23,42,0.08)] backdrop-blur">
                            {!! nl2br(e($siteDescription)) !!}
                        </div>
                    </div>

                    <div>
                        <p class="storefront-eyebrow">{{ __('site.storefront.gallery') }}</p>
                        <div class="mt-6 grid gap-4 md:grid-cols-2">
                            @forelse ($gallery as $index => $media)
                                <div class="{{ $index === 0 ? 'md:col-span-2' : '' }} overflow-hidden rounded-[1.85rem] border border-white/40 bg-white/85 shadow-[0_24px_80px_rgba(15,23,42,0.08)]">
                                    <img src="{{ $media->getUrl() }}" alt="{{ $siteTitle }}" class="{{ $index === 0 ? 'h-80' : 'h-72' }} w-full object-cover transition duration-500 hover:scale-105">
                                </div>
                            @empty
                                @foreach ($galleryFallback as $index => $fallback)
                                    <div class="{{ $index === 0 ? 'md:col-span-2' : '' }} overflow-hidden rounded-[1.85rem] border border-white/40 bg-white/85 shadow-[0_24px_80px_rgba(15,23,42,0.08)]">
                                        <img src="{{ $fallback }}" alt="{{ $siteTitle }}" class="{{ $index === 0 ? 'h-80' : 'h-72' }} w-full object-cover">
                                    </div>
                                @endforeach
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <p class="storefront-eyebrow">{{ __('site.storefront.amenities') }}</p>
                        <div class="mt-6 flex flex-wrap gap-3">
                            @forelse ($accommodation->amenities as $amenity)
                                <span class="storefront-chip border-white/60 bg-white/84 text-slate-700 backdrop-blur">{{ $amenity->name }}</span>
                            @empty
                                <span class="storefront-chip border-white/60 bg-white/84 text-slate-500 backdrop-blur">{{ __('site.storefront.amenities_soon') }}</span>
                            @endforelse
                        </div>
                    </div>
                </section>

                <aside class="space-y-6">
                    <section class="storefront-panel p-8">
                        <p class="storefront-eyebrow">{{ __('site.storefront.location') }}</p>
                        <h3 class="mt-3 text-2xl font-semibold text-slate-900">{{ $siteCity ?: __('site.storefront.location') }}</h3>
                        <p class="mt-3 leading-7 text-slate-600">{{ $siteAddressLine }}</p>
                        @if ($accommodation->google_maps_url)
                            <a href="{{ $accommodation->google_maps_url }}" target="_blank" rel="noreferrer" class="storefront-button-primary mt-6">
                                {{ __('site.storefront.open_maps') }}
                            </a>
                        @endif
                    </section>

                    <section id="upit" class="storefront-panel p-8">
                        <p class="storefront-eyebrow">{{ __('site.storefront.contact') }}</p>
                        <div class="mt-5 space-y-3 text-sm text-slate-700">
                            @if ($accommodation->contact_phone)<p><span class="font-semibold">{{ __('site.storefront.phone') }}:</span> {{ $accommodation->contact_phone }}</p>@endif
                            @if ($accommodation->contact_email)<p><span class="font-semibold">{{ __('site.storefront.email') }}:</span> {{ $accommodation->contact_email }}</p>@endif
                            @if ($accommodation->whatsapp_number)<p><span class="font-semibold">{{ __('site.storefront.whatsapp') }}:</span> {{ $accommodation->whatsapp_number }}</p>@endif
                            @if ($accommodation->viber_number)<p><span class="font-semibold">{{ __('site.storefront.viber') }}:</span> {{ $accommodation->viber_number }}</p>@endif
                        </div>

                        @if ($externalLinks->isNotEmpty())
                            <div class="mt-5 flex flex-wrap gap-2">
                                @foreach ($externalLinks as $link)
                                    <a href="{{ $link['url'] }}" target="_blank" rel="noreferrer" class="rounded-full border border-stone-200 bg-stone-50 px-3 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-700 transition hover:bg-white">
                                        {{ $link['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ ($isThemeDemo ?? false) ? '#' : route('storefront.inquiry.store', $accommodation->slug) }}" class="mt-8 space-y-4">
                            @csrf
                            @if ($errors->any())
                                <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                                    <p class="font-semibold">{{ __('site.storefront.inquiry_errors_title') }}</p>
                                    <p class="mt-1">{{ $errors->first() }}</p>
                                </div>
                            @endif
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">{{ __('site.storefront.name') }}</label>
                                <input name="guest_name" value="{{ old('guest_name') }}" required class="storefront-input">
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-slate-700">{{ __('site.storefront.email') }}</label>
                                    <input type="email" name="guest_email" value="{{ old('guest_email') }}" required class="storefront-input">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-slate-700">{{ __('site.storefront.phone') }}</label>
                                    <input name="guest_phone" value="{{ old('guest_phone') }}" class="storefront-input">
                                </div>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-slate-700">{{ __('site.storefront.check_in') }}</label>
                                    <input type="date" name="check_in" value="{{ old('check_in') }}" class="storefront-input">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-slate-700">{{ __('site.storefront.check_out') }}</label>
                                    <input type="date" name="check_out" value="{{ old('check_out') }}" class="storefront-input">
                                </div>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">{{ __('site.storefront.guests_count') }}</label>
                                <input type="number" name="guests_count" min="1" value="{{ old('guests_count') }}" class="storefront-input">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">{{ __('site.storefront.message') }}</label>
                                <textarea name="message" rows="5" required class="storefront-input">{{ old('message') }}</textarea>
                            </div>
                            @if ($isThemeDemo ?? false)
                                <div class="rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-slate-600">
                                    {{ __('site.storefront.demo_form_note') }}
                                </div>
                            @else
                                <button type="submit" class="storefront-button-primary w-full">
                                    {{ __('site.storefront.send_inquiry') }}
                                </button>
                            @endif
                        </form>
                    </section>
                </aside>
            </div>
        </main>

        <footer class="border-t border-white/10 bg-[#0c1713]">
            <div class="mx-auto flex max-w-7xl flex-col gap-3 px-6 py-8 text-sm text-white/72 sm:px-8 lg:flex-row lg:items-center lg:justify-between lg:px-10">
                <p>{{ $settings->platform_name }} | {{ __('site.storefront.default_footer') }}</p>
                <p>{{ $siteTitle }} | {{ $siteCity }}</p>
            </div>
        </footer>
    </div>
</body>
</html>

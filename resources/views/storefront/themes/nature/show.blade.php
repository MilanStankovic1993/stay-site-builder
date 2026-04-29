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
            --site-primary: {{ $accommodation->primary_color ?: '#355345' }};
            --site-secondary: {{ $accommodation->secondary_color ?: '#b5894d' }};
            --site-sand: #f6f1e7;
            --site-mist: #dfe6da;
            --site-leaf: #edf4ee;
        }
    </style>
</head>
<body class="bg-[var(--site-sand)] text-slate-900">
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
            asset('demo/placeholders/gallery-bedroom.svg'),
            asset('demo/placeholders/gallery-lounge.svg'),
            asset('demo/placeholders/gallery-wellness.svg'),
        ];
    @endphp

    @if ($isThemeDemo ?? false)
        <div class="sticky top-0 z-50 border-b border-emerald-200 bg-emerald-50/95 backdrop-blur">
            <div class="mx-auto max-w-7xl px-6 py-3 text-sm font-medium text-emerald-900 sm:px-8 lg:px-10">
                {{ __('site.storefront.demo_banner_nature') }}
            </div>
        </div>
    @elseif ($isPreview ?? false)
        <div class="sticky top-0 z-50 border-b border-amber-200 bg-amber-50/95 backdrop-blur">
            <div class="mx-auto max-w-7xl px-6 py-3 text-sm font-medium text-amber-900 sm:px-8 lg:px-10">
                {{ __('site.storefront.preview_banner') }}
            </div>
        </div>
    @endif

    <div class="storefront-shell bg-[radial-gradient(circle_at_top,_rgba(181,137,77,0.16),_transparent_28%),linear-gradient(180deg,_#f7f3ea_0%,_#f2ecde_44%,_#eef3ea_100%)]">
        <header class="relative">
            <div class="absolute inset-x-0 top-0 h-[32rem] bg-[radial-gradient(circle_at_top,_rgba(53,83,69,0.12),_transparent_42%)]"></div>
            <div class="mx-auto max-w-7xl px-6 pb-16 pt-8 sm:px-8 lg:px-10 lg:pb-24">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        @if ($accommodation->logo_url)
                            <img src="{{ $accommodation->logo_url }}" alt="{{ $siteTitle }} logo" class="h-12 w-auto rounded-2xl bg-white/90 p-2 shadow-[0_12px_35px_rgba(53,83,69,0.12)]">
                        @endif
                        <a href="{{ route('home') }}" class="text-xs uppercase tracking-[0.4em] text-[var(--site-primary)]">{{ __('site.brand') }}</a>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="rounded-full border border-white/70 bg-white/84 px-4 py-2 text-xs uppercase tracking-[0.28em] text-[var(--site-secondary)] shadow-[0_12px_35px_rgba(53,83,69,0.08)]">
                            {{ __('site.storefront.theme_nature_name') }}
                        </div>
                        @include('shared.locale-switcher', ['tone' => 'light'])
                    </div>
                </div>

                <div class="mt-12 grid gap-8 lg:grid-cols-[1.04fr_0.96fr] lg:items-center">
                    <div>
                        <p class="text-sm uppercase tracking-[0.35em] text-[var(--site-secondary)]">{{ $siteLocationName }}</p>
                        <h1 class="mt-5 max-w-4xl font-serif text-5xl leading-[0.94] text-[var(--site-primary)] sm:text-6xl lg:text-[5.3rem]">
                            {{ $siteTitle }}
                        </h1>
                        <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-700">
                            {{ $siteShortDescription ?: __('site.storefront.nature_fallback_short') }}
                        </p>

                        <div class="mt-8 flex flex-wrap gap-3">
                            <a href="#upit" class="storefront-button-primary">{{ __('site.storefront.send_inquiry') }}</a>
                            @if ($accommodation->whatsapp_number)
                                <a href="https://wa.me/{{ preg_replace('/\D+/', '', $accommodation->whatsapp_number) }}" class="inline-flex items-center justify-center rounded-full border border-[var(--site-primary)]/18 bg-white px-6 py-3 text-sm font-semibold text-[var(--site-primary)] transition duration-200 hover:-translate-y-0.5">
                                    WhatsApp
                                </a>
                            @endif
                        </div>

                        <div class="mt-8 flex flex-wrap gap-3">
                            <div class="storefront-chip border-white/60 bg-white/88 text-slate-700">{{ $accommodation->max_guests ?: '-' }} {{ __('site.storefront.guests') }}</div>
                            <div class="storefront-chip border-white/60 bg-white/88 text-slate-700">{{ $accommodation->bedrooms ?: '-' }} {{ __('site.storefront.bedrooms') }}</div>
                            <div class="storefront-chip border-white/60 bg-white/88 text-slate-700">{{ $accommodation->bathrooms ?: '-' }} {{ __('site.storefront.bathrooms') }}</div>
                            <div class="storefront-chip border-white/60 bg-white/88 text-slate-700">{{ $accommodation->price_from ? number_format((float) $accommodation->price_from, 0, ',', '.') . ' ' . $accommodation->currency : __('site.storefront.on_request') }}</div>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2 overflow-hidden rounded-[2.2rem] shadow-[0_25px_80px_rgba(53,83,69,0.14)]">
                            @if ($accommodation->hero_image_url)
                                <img src="{{ $accommodation->hero_image_url }}" alt="{{ $siteTitle }}" class="h-80 w-full object-cover">
                            @else
                                <img src="{{ $galleryFallback[0] }}" alt="{{ $siteTitle }}" class="h-80 w-full object-cover">
                            @endif
                        </div>
                        @if ($gallery->isNotEmpty())
                            @foreach ($gallery->take(2) as $media)
                                <div class="overflow-hidden rounded-[1.8rem] shadow-[0_20px_50px_rgba(53,83,69,0.12)]">
                                    <img src="{{ $media->getUrl() }}" alt="{{ $siteTitle }}" class="h-52 w-full object-cover">
                                </div>
                            @endforeach
                        @else
                            @foreach (array_slice($galleryFallback, 1, 2) as $fallback)
                                <div class="overflow-hidden rounded-[1.8rem] shadow-[0_20px_50px_rgba(53,83,69,0.12)]">
                                    <img src="{{ $fallback }}" alt="{{ $siteTitle }}" class="h-52 w-full object-cover">
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-6 pb-24 sm:px-8 lg:px-10">
            <section class="grid gap-6 lg:grid-cols-[1fr_0.82fr]">
                <div class="storefront-panel p-8">
                    <p class="storefront-eyebrow">{{ __('site.storefront.description') }}</p>
                    <h2 class="mt-4 font-serif text-4xl leading-tight text-[var(--site-primary)]">{{ __('site.storefront.nature_title') }}</h2>
                    <div class="mt-5 text-base leading-8 text-slate-700">
                        {!! nl2br(e($siteDescription)) !!}
                    </div>
                </div>

                <div class="rounded-[2rem] border border-white/60 bg-[var(--site-leaf)] p-8 shadow-[0_25px_80px_rgba(53,83,69,0.06)]">
                    <p class="storefront-eyebrow">{{ __('site.storefront.amenities') }}</p>
                    <div class="mt-5 flex flex-wrap gap-3">
                        @forelse ($accommodation->amenities as $amenity)
                            <span class="storefront-chip border-white/70 bg-white text-slate-700">{{ $amenity->name }}</span>
                        @empty
                            <span class="storefront-chip border-white/70 bg-white text-slate-500">{{ __('site.storefront.amenities_soon') }}</span>
                        @endforelse
                    </div>

                    <div class="mt-8 rounded-[1.8rem] border border-white/70 bg-white/90 p-6 shadow-[0_15px_40px_rgba(53,83,69,0.06)]">
                        <p class="storefront-eyebrow">{{ __('site.storefront.gallery') }}</p>
                        <p class="mt-3 text-base leading-7 text-slate-700">
                            {{ __('site.storefront.nature_gallery_note') }}
                        </p>
                    </div>
                </div>
            </section>

            <section class="mt-10 grid gap-6 lg:grid-cols-[0.82fr_1.18fr]">
                <div class="rounded-[2rem] bg-[var(--site-primary)] p-8 text-white shadow-[0_25px_80px_rgba(53,83,69,0.16)]">
                    <p class="storefront-eyebrow">{{ __('site.storefront.location') }}</p>
                    <h3 class="mt-4 text-3xl font-semibold">{{ $siteCity ?: __('site.storefront.location') }}</h3>
                    <p class="mt-4 leading-8 text-white/78">{{ $siteAddressLine }}</p>
                    @if ($accommodation->google_maps_url)
                        <a href="{{ $accommodation->google_maps_url }}" target="_blank" rel="noreferrer" class="storefront-button-secondary mt-6">
                            {{ __('site.storefront.open_maps') }}
                        </a>
                    @endif
                </div>

                <div id="upit" class="storefront-panel p-8">
                    <div class="grid gap-8 lg:grid-cols-[0.52fr_0.48fr]">
                        <div>
                            <p class="storefront-eyebrow">{{ __('site.storefront.contact') }}</p>
                            <h3 class="mt-4 font-serif text-4xl leading-tight text-[var(--site-primary)]">{{ __('site.storefront.nature_contact_title') }}</h3>
                            <div class="mt-6 space-y-3 text-sm text-slate-700">
                                @if ($accommodation->contact_name)<p><span class="font-semibold">{{ __('site.storefront.contact_person') }}:</span> {{ $accommodation->contact_name }}</p>@endif
                                @if ($accommodation->contact_phone)<p><span class="font-semibold">{{ __('site.storefront.phone') }}:</span> {{ $accommodation->contact_phone }}</p>@endif
                                @if ($accommodation->contact_email)<p><span class="font-semibold">{{ __('site.storefront.email') }}:</span> {{ $accommodation->contact_email }}</p>@endif
                                @if ($accommodation->whatsapp_number)<p><span class="font-semibold">{{ __('site.storefront.whatsapp') }}:</span> {{ $accommodation->whatsapp_number }}</p>@endif
                                @if ($accommodation->viber_number)<p><span class="font-semibold">{{ __('site.storefront.viber') }}:</span> {{ $accommodation->viber_number }}</p>@endif
                            </div>

                            @if ($externalLinks->isNotEmpty())
                                <div class="mt-6 flex flex-wrap gap-2">
                                    @foreach ($externalLinks as $link)
                                        <a href="{{ $link['url'] }}" target="_blank" rel="noreferrer" class="rounded-full border border-stone-200 bg-stone-50 px-3 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-700 transition hover:bg-white">
                                            {{ $link['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <form method="POST" action="{{ ($isThemeDemo ?? false) ? '#' : route('storefront.inquiry.store', $accommodation->slug) }}" class="grid gap-4 content-start">
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
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">{{ __('site.storefront.email') }}</label>
                                <input type="email" name="guest_email" value="{{ old('guest_email') }}" required class="storefront-input">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">{{ __('site.storefront.phone') }}</label>
                                <input name="guest_phone" value="{{ old('guest_phone') }}" class="storefront-input">
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
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>

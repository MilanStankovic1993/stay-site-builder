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
            --site-primary: {{ $accommodation->primary_color ?: '#121a17' }};
            --site-secondary: {{ $accommodation->secondary_color ?: '#d3ab69' }};
            --site-cream: #f4ecdf;
        }
    </style>
</head>
<body class="bg-[#0a100f] text-stone-100">
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
    @endphp

    @if ($isThemeDemo ?? false)
        <div class="sticky top-0 z-50 border-b border-amber-300/20 bg-[#111816]/95 backdrop-blur">
            <div class="mx-auto max-w-7xl px-6 py-3 text-sm font-medium text-amber-100 sm:px-8 lg:px-10">
                {{ __('site.storefront.demo_banner_luxury') }}
            </div>
        </div>
    @elseif ($isPreview ?? false)
        <div class="sticky top-0 z-50 border-b border-amber-300/20 bg-[#111816]/95 backdrop-blur">
            <div class="mx-auto max-w-7xl px-6 py-3 text-sm font-medium text-amber-100 sm:px-8 lg:px-10">
                {{ __('site.storefront.preview_banner') }}
            </div>
        </div>
    @endif

    <div class="relative overflow-hidden">
        <div class="absolute inset-x-0 top-0 h-[38rem] bg-[radial-gradient(circle_at_top,_rgba(211,171,105,0.24),_transparent_42%)]"></div>

        <header class="relative border-b border-white/10">
            <div class="absolute inset-0">
                @if ($accommodation->hero_image_url)
                    <img src="{{ $accommodation->hero_image_url }}" alt="{{ $siteTitle }}" class="h-full w-full object-cover">
                @endif
                <div class="absolute inset-0 bg-[linear-gradient(180deg,_rgba(8,11,10,0.24),_rgba(8,11,10,0.72)_58%,_rgba(8,11,10,0.95)_100%)]"></div>
            </div>

            <div class="relative mx-auto max-w-7xl px-6 pb-14 pt-8 sm:px-8 lg:px-10 lg:pb-24">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        @if ($accommodation->logo_url)
                            <img src="{{ $accommodation->logo_url }}" alt="{{ $siteTitle }} logo" class="h-12 w-auto rounded-2xl bg-white/90 p-2 shadow-[0_20px_50px_rgba(0,0,0,0.25)]">
                        @endif
                        <a href="{{ route('home') }}" class="text-xs uppercase tracking-[0.4em] text-white/70">StaySite Builder</a>
                    </div>
                    <div class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs uppercase tracking-[0.28em] text-[var(--site-secondary)] backdrop-blur">
                        Luxury theme
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    @include('shared.locale-switcher', ['tone' => 'dark'])
                </div>

                <div class="mt-16 grid gap-10 lg:grid-cols-[1.08fr_0.92fr] lg:items-end">
                    <div class="max-w-4xl">
                        <p class="text-sm uppercase tracking-[0.38em] text-[var(--site-secondary)]">
                            {{ $siteLocationName }}
                        </p>
                        <h1 class="mt-5 font-serif text-6xl leading-[0.9] text-white sm:text-7xl lg:text-[5.9rem]">
                            {{ $siteTitle }}
                        </h1>
                        <p class="mt-6 max-w-2xl text-lg leading-8 text-white/76">
                            {{ $siteShortDescription ?: 'Elegantna prezentacija smestaja koja ostavlja premium prvi utisak i vodi gosta direktno do upita.' }}
                        </p>

                        <div class="mt-8 flex flex-wrap gap-3">
                                <a href="#upit" class="rounded-full bg-[var(--site-secondary)] px-6 py-3 text-sm font-semibold text-stone-950 transition hover:opacity-90">
                                {{ __('site.storefront.send_inquiry') }}
                            </a>
                            @if ($accommodation->whatsapp_number)
                                <a href="https://wa.me/{{ preg_replace('/\D+/', '', $accommodation->whatsapp_number) }}" class="rounded-full border border-white/20 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                                    WhatsApp
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="rounded-[2.2rem] border border-white/10 bg-[linear-gradient(180deg,_rgba(255,255,255,0.08),_rgba(255,255,255,0.03))] p-8 shadow-[0_30px_90px_rgba(0,0,0,0.28)] backdrop-blur-xl">
                        <div class="flex items-center justify-between">
                            <p class="text-xs uppercase tracking-[0.32em] text-[var(--site-secondary)]">Signature stay</p>
                            <p class="text-xs uppercase tracking-[0.24em] text-white/45">{{ $accommodation->type?->label() }}</p>
                        </div>

                        <div class="mt-6 grid grid-cols-2 gap-4">
                            <div class="rounded-[1.4rem] border border-white/10 bg-black/15 p-5">
                                <p class="text-xs uppercase tracking-[0.25em] text-white/45">{{ __('site.storefront.guests') }}</p>
                                <p class="mt-3 text-3xl font-semibold text-white">{{ $accommodation->max_guests ?: '-' }}</p>
                            </div>
                            <div class="rounded-[1.4rem] border border-white/10 bg-black/15 p-5">
                                <p class="text-xs uppercase tracking-[0.25em] text-white/45">{{ __('site.storefront.bedrooms') }}</p>
                                <p class="mt-3 text-3xl font-semibold text-white">{{ $accommodation->bedrooms ?: '-' }}</p>
                            </div>
                            <div class="rounded-[1.4rem] border border-white/10 bg-black/15 p-5">
                                <p class="text-xs uppercase tracking-[0.25em] text-white/45">{{ __('site.storefront.bathrooms') }}</p>
                                <p class="mt-3 text-3xl font-semibold text-white">{{ $accommodation->bathrooms ?: '-' }}</p>
                            </div>
                            <div class="rounded-[1.4rem] border border-white/10 bg-black/15 p-5">
                                <p class="text-xs uppercase tracking-[0.25em] text-white/45">{{ __('site.storefront.price_from') }}</p>
                                <p class="mt-3 text-3xl font-semibold text-white">{{ $accommodation->price_from ? number_format((float) $accommodation->price_from, 0, ',', '.') . ' ' . $accommodation->currency : __('site.storefront.on_request') }}</p>
                            </div>
                        </div>

                        <div class="mt-6 rounded-[1.6rem] border border-white/10 bg-black/15 p-5">
                            <p class="text-xs uppercase tracking-[0.24em] text-white/45">Kontakt</p>
                            <div class="mt-3 space-y-2 text-sm text-white/75">
                                @if ($accommodation->contact_phone)<p>Telefon: {{ $accommodation->contact_phone }}</p>@endif
                                @if ($accommodation->contact_email)<p>Email: {{ $accommodation->contact_email }}</p>@endif
                                @if ($accommodation->whatsapp_number)<p>WhatsApp: {{ $accommodation->whatsapp_number }}</p>@endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-6 pb-24 pt-12 sm:px-8 lg:px-10 lg:pt-16">
            <section class="grid gap-6 lg:grid-cols-[0.8fr_1.2fr]">
                <div class="rounded-[2rem] border border-white/10 bg-white/5 p-8">
                    <p class="text-sm uppercase tracking-[0.35em] text-[var(--site-secondary)]">{{ __('site.storefront.description') }}</p>
                    <h2 class="mt-4 font-serif text-4xl leading-tight text-white">{{ __('site.storefront.luxury_title') }}</h2>
                    <div class="mt-5 text-base leading-8 text-white/72">
                        {!! nl2br(e($siteDescription)) !!}
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    @forelse ($accommodation->getMedia('gallery') as $index => $media)
                        <div class="{{ $index === 0 ? 'md:col-span-2' : '' }} overflow-hidden rounded-[2rem] border border-white/10 bg-white/5">
                            <img src="{{ $media->getUrl() }}" alt="{{ $siteTitle }}" class="{{ $index === 0 ? 'h-[26rem]' : 'h-72' }} w-full object-cover transition duration-500 hover:scale-105">
                        </div>
                    @empty
                        <div class="rounded-[2rem] border border-white/10 bg-white/5 p-8 text-white/60 md:col-span-2">
                            {{ __('site.storefront.gallery_soon') }}
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="mt-14 grid gap-6 lg:grid-cols-[0.72fr_1.28fr]">
                <div class="rounded-[2rem] border border-white/10 bg-white/5 p-8">
                    <p class="text-sm uppercase tracking-[0.35em] text-[var(--site-secondary)]">{{ __('site.storefront.amenities') }}</p>
                    <div class="mt-5 flex flex-wrap gap-3">
                        @forelse ($accommodation->amenities as $amenity)
                            <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-white/82">{{ $amenity->name }}</span>
                        @empty
                            <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-white/55">{{ __('site.storefront.amenities_soon') }}</span>
                        @endforelse
                    </div>

                    <div class="mt-8 rounded-[1.8rem] border border-white/10 bg-black/15 p-6">
                        <p class="text-sm uppercase tracking-[0.35em] text-[var(--site-secondary)]">{{ __('site.storefront.location') }}</p>
                        <h3 class="mt-3 text-3xl font-semibold text-white">{{ $siteCity ?: __('site.storefront.location') }}</h3>
                        <p class="mt-4 leading-8 text-white/70">{{ $siteAddressLine }}</p>
                        @if ($accommodation->google_maps_url)
                            <a href="{{ $accommodation->google_maps_url }}" target="_blank" rel="noreferrer" class="mt-6 inline-flex rounded-full bg-[var(--site-secondary)] px-5 py-3 text-sm font-semibold text-stone-950">
                                {{ __('site.storefront.open_maps') }}
                            </a>
                        @endif
                    </div>
                </div>

                <div id="upit" class="rounded-[2rem] bg-[var(--site-cream)] p-8 text-slate-900 shadow-[0_30px_90px_rgba(0,0,0,0.18)]">
                    <div class="grid gap-8 lg:grid-cols-[0.58fr_0.42fr]">
                        <div>
                            <p class="text-sm uppercase tracking-[0.35em] text-[var(--site-secondary)]">{{ __('site.storefront.contact') }}</p>
                            <h3 class="mt-4 font-serif text-4xl leading-tight text-[var(--site-primary)]">{{ __('site.storefront.luxury_contact_title') }}</h3>
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
                                        <a href="{{ $link['url'] }}" target="_blank" rel="noreferrer" class="rounded-full border border-stone-200 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-slate-700 transition hover:bg-stone-50">
                                            {{ $link['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <form method="POST" action="{{ ($isThemeDemo ?? false) ? '#' : route('storefront.inquiry.store', $accommodation->slug) }}" class="grid gap-4 content-start">
                            @csrf
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">{{ __('site.storefront.name') }}</label>
                                <input name="guest_name" value="{{ old('guest_name') }}" class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 outline-none focus:border-[var(--site-secondary)]">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">{{ __('site.storefront.email') }}</label>
                                <input type="email" name="guest_email" value="{{ old('guest_email') }}" class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 outline-none focus:border-[var(--site-secondary)]">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">{{ __('site.storefront.phone') }}</label>
                                <input name="guest_phone" value="{{ old('guest_phone') }}" class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 outline-none focus:border-[var(--site-secondary)]">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">{{ __('site.storefront.message') }}</label>
                                <textarea name="message" rows="5" class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 outline-none focus:border-[var(--site-secondary)]">{{ old('message') }}</textarea>
                            </div>

                            @if ($isThemeDemo ?? false)
                                <div class="rounded-2xl border border-stone-200 bg-white px-4 py-3 text-sm text-slate-600">
                                    {{ __('site.storefront.demo_form_note') }}
                                </div>
                            @else
                                <button type="submit" class="w-full rounded-full bg-[var(--site-primary)] px-6 py-3 text-sm font-semibold text-white transition hover:opacity-95">
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

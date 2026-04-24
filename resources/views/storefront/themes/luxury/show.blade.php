<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --site-primary: {{ $accommodation->primary_color ?: '#16261f' }};
            --site-secondary: {{ $accommodation->secondary_color ?: '#d8b06a' }};
        }
    </style>
</head>
<body class="bg-[#0d1512] text-stone-100">
    @if ($isThemeDemo ?? false)
        <div class="sticky top-0 z-50 border-b border-amber-300/20 bg-[#111a17]/95 backdrop-blur">
            <div class="mx-auto max-w-7xl px-6 py-3 text-sm font-medium text-amber-100 sm:px-8 lg:px-10">
                Demo pregled teme Luxury: premium izgled za vile i ekskluzivne objekte.
            </div>
        </div>
    @elseif ($isPreview ?? false)
        <div class="sticky top-0 z-50 border-b border-amber-300/20 bg-[#111a17]/95 backdrop-blur">
            <div class="mx-auto max-w-7xl px-6 py-3 text-sm font-medium text-amber-100 sm:px-8 lg:px-10">
                Preview rezim: sajt jos nije javno objavljen.
            </div>
        </div>
    @endif

    <header class="relative isolate overflow-hidden border-b border-white/10">
        @if ($accommodation->hero_image_url)
            <img src="{{ $accommodation->hero_image_url }}" alt="{{ $accommodation->title }}" class="absolute inset-0 h-full w-full object-cover">
        @endif
        <div class="absolute inset-0 bg-[linear-gradient(180deg,_rgba(6,10,9,0.28),_rgba(6,10,9,0.8)_70%,_#0d1512_100%)]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(216,176,106,0.3),_transparent_30%)]"></div>

        <div class="relative mx-auto max-w-7xl px-6 pb-16 pt-8 sm:px-8 lg:px-10 lg:pb-24 lg:pt-12">
            <div class="flex items-center justify-between">
                <a href="{{ route('home') }}" class="text-xs uppercase tracking-[0.38em] text-white/70">StaySite Builder</a>
                <div class="rounded-full border border-white/15 bg-white/5 px-4 py-2 text-xs uppercase tracking-[0.26em] text-[var(--site-secondary)]">
                    Luxury theme
                </div>
            </div>

            <div class="mt-20 grid gap-10 lg:grid-cols-[1.15fr_0.85fr] lg:items-end">
                <div class="max-w-4xl">
                    <p class="text-sm uppercase tracking-[0.35em] text-[var(--site-secondary)]">{{ $accommodation->location_name ?: $accommodation->city }}</p>
                    <h1 class="mt-5 font-serif text-6xl leading-[0.92] text-white sm:text-7xl lg:text-[5.6rem]">{{ $accommodation->title }}</h1>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-white/74">{{ $accommodation->short_description }}</p>
                </div>

                <div class="rounded-[2rem] border border-white/10 bg-white/6 p-8 backdrop-blur-md">
                    <p class="text-xs uppercase tracking-[0.32em] text-[var(--site-secondary)]">Boutique presentation</p>
                    <div class="mt-6 grid grid-cols-2 gap-4 text-white">
                        <div class="rounded-[1.4rem] bg-white/5 p-4"><p class="text-xs uppercase tracking-[0.25em] text-white/50">Gostiju</p><p class="mt-3 text-3xl font-semibold">{{ $accommodation->max_guests ?: '-' }}</p></div>
                        <div class="rounded-[1.4rem] bg-white/5 p-4"><p class="text-xs uppercase tracking-[0.25em] text-white/50">Sobe</p><p class="mt-3 text-3xl font-semibold">{{ $accommodation->bedrooms ?: '-' }}</p></div>
                        <div class="rounded-[1.4rem] bg-white/5 p-4"><p class="text-xs uppercase tracking-[0.25em] text-white/50">Kupatila</p><p class="mt-3 text-3xl font-semibold">{{ $accommodation->bathrooms ?: '-' }}</p></div>
                        <div class="rounded-[1.4rem] bg-white/5 p-4"><p class="text-xs uppercase tracking-[0.25em] text-white/50">Cena od</p><p class="mt-3 text-3xl font-semibold">{{ $accommodation->price_from ? number_format((float) $accommodation->price_from, 0, ',', '.') . ' ' . $accommodation->currency : 'Na upit' }}</p></div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-6 pb-20 pt-12 sm:px-8 lg:px-10 lg:pt-16">
        <section class="grid gap-10 lg:grid-cols-[0.9fr_1.1fr]">
            <div class="space-y-6">
                <p class="text-sm uppercase tracking-[0.35em] text-[var(--site-secondary)]">Atmosfera</p>
                <h2 class="font-serif text-5xl leading-tight text-white">Dizajniran da smestaj izgleda ekskluzivno na prvi pogled.</h2>
                <p class="text-base leading-8 text-white/68">{!! nl2br(e($accommodation->description ?: $accommodation->short_description)) !!}</p>

                <div class="flex flex-wrap gap-3 pt-3">
                    @foreach ($accommodation->amenities as $amenity)
                        <span class="rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-white/80">{{ $amenity->name }}</span>
                    @endforeach
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($accommodation->getMedia('gallery') as $media)
                    <div class="overflow-hidden rounded-[1.8rem] border border-white/10 bg-white/5">
                        <img src="{{ $media->getUrl() }}" alt="{{ $accommodation->title }}" class="h-72 w-full object-cover transition duration-500 hover:scale-105">
                    </div>
                @endforeach
            </div>
        </section>

        <section class="mt-14 grid gap-6 lg:grid-cols-[0.75fr_1.25fr]">
            <div class="rounded-[2rem] border border-white/10 bg-white/5 p-8">
                <p class="text-sm uppercase tracking-[0.35em] text-[var(--site-secondary)]">Lokacija</p>
                <h3 class="mt-4 text-3xl font-semibold text-white">{{ $accommodation->city ?: 'Lokacija' }}</h3>
                <p class="mt-4 leading-8 text-white/68">{{ collect([$accommodation->address, $accommodation->city, $accommodation->region, $accommodation->country])->filter()->join(', ') }}</p>
                @if ($accommodation->google_maps_url)
                    <a href="{{ $accommodation->google_maps_url }}" target="_blank" rel="noreferrer" class="mt-6 inline-flex rounded-full bg-[var(--site-secondary)] px-5 py-3 text-sm font-semibold text-stone-950">
                        Otvori Google Maps
                    </a>
                @endif
            </div>

            <div class="rounded-[2rem] border border-white/10 bg-[#f7f0e4] p-8 text-slate-900">
                <p class="text-sm uppercase tracking-[0.35em] text-[var(--site-secondary)]">Kontakt i rezervacija</p>
                <div class="mt-5 grid gap-3 text-sm text-slate-700 sm:grid-cols-3">
                    @if ($accommodation->contact_phone)<p><span class="font-semibold">Telefon:</span> {{ $accommodation->contact_phone }}</p>@endif
                    @if ($accommodation->contact_email)<p><span class="font-semibold">Email:</span> {{ $accommodation->contact_email }}</p>@endif
                    @if ($accommodation->whatsapp_number)<p><span class="font-semibold">WhatsApp:</span> {{ $accommodation->whatsapp_number }}</p>@endif
                </div>

                <form method="POST" action="{{ ($isThemeDemo ?? false) ? '#' : route('storefront.inquiry.store', $accommodation->slug) }}" class="mt-8 grid gap-4 sm:grid-cols-2">
                    @csrf
                    <div class="sm:col-span-2">
                        <label class="mb-2 block text-sm font-medium text-slate-700">Ime i prezime</label>
                        <input name="guest_name" value="{{ old('guest_name') }}" class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 outline-none focus:border-[var(--site-secondary)]">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                        <input type="email" name="guest_email" value="{{ old('guest_email') }}" class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 outline-none focus:border-[var(--site-secondary)]">
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Telefon</label>
                        <input name="guest_phone" value="{{ old('guest_phone') }}" class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 outline-none focus:border-[var(--site-secondary)]">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-2 block text-sm font-medium text-slate-700">Poruka</label>
                        <textarea name="message" rows="5" class="w-full rounded-2xl border border-stone-200 bg-white px-4 py-3 outline-none focus:border-[var(--site-secondary)]">{{ old('message') }}</textarea>
                    </div>
                    <div class="sm:col-span-2">
                        @if ($isThemeDemo ?? false)
                            <div class="rounded-2xl border border-stone-200 bg-white px-4 py-3 text-sm text-slate-600">
                                Demo tema prikazuje premium raspored kontakt sekcije. Slanje upita radi na objavljenim sajtovima.
                            </div>
                        @else
                            <button type="submit" class="w-full rounded-full bg-[var(--site-primary)] px-6 py-3 text-sm font-semibold text-white">
                                Posalji upit
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </section>
    </main>
</body>
</html>

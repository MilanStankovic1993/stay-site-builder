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
            --site-primary: {{ $accommodation->primary_color ?: '#365446' }};
            --site-secondary: {{ $accommodation->secondary_color ?: '#b98b4d' }};
        }
    </style>
</head>
<body class="bg-[#eef1e7] text-slate-900">
    @if ($isThemeDemo ?? false)
        <div class="sticky top-0 z-50 border-b border-emerald-200 bg-emerald-50/95 backdrop-blur">
            <div class="mx-auto max-w-7xl px-6 py-3 text-sm font-medium text-emerald-900 sm:px-8 lg:px-10">
                Demo pregled teme Nature: topla prezentacija za vikendice, brvnare i odmor u prirodi.
            </div>
        </div>
    @elseif ($isPreview ?? false)
        <div class="sticky top-0 z-50 border-b border-amber-200 bg-amber-50/95 backdrop-blur">
            <div class="mx-auto max-w-7xl px-6 py-3 text-sm font-medium text-amber-900 sm:px-8 lg:px-10">
                Preview rezim: sajt jos nije javno objavljen.
            </div>
        </div>
    @endif

    <header class="relative isolate overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(185,139,77,0.18),_transparent_32%),linear-gradient(180deg,_rgba(238,241,231,0.18),_rgba(238,241,231,0.95))]"></div>
        <div class="mx-auto max-w-7xl px-6 pb-14 pt-8 sm:px-8 lg:px-10 lg:pb-20">
            <div class="flex items-center justify-between">
                <a href="{{ route('home') }}" class="text-xs uppercase tracking-[0.38em] text-[var(--site-primary)]">StaySite Builder</a>
                <div class="rounded-full bg-white/70 px-4 py-2 text-xs uppercase tracking-[0.25em] text-[var(--site-secondary)] shadow-[0_12px_35px_rgba(54,84,70,0.08)]">
                    Nature theme
                </div>
            </div>

            <div class="mt-12 grid gap-8 lg:grid-cols-[1.05fr_0.95fr] lg:items-center">
                <div>
                    <p class="text-sm uppercase tracking-[0.35em] text-[var(--site-secondary)]">{{ $accommodation->location_name ?: $accommodation->city }}</p>
                    <h1 class="mt-5 max-w-4xl font-serif text-5xl leading-[0.96] text-[var(--site-primary)] sm:text-6xl lg:text-[5.1rem]">{{ $accommodation->title }}</h1>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-700">{{ $accommodation->short_description }}</p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <div class="rounded-full bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-[0_12px_35px_rgba(54,84,70,0.08)]">{{ $accommodation->max_guests ?: '-' }} gostiju</div>
                        <div class="rounded-full bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-[0_12px_35px_rgba(54,84,70,0.08)]">{{ $accommodation->bedrooms ?: '-' }} sobe</div>
                        <div class="rounded-full bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-[0_12px_35px_rgba(54,84,70,0.08)]">{{ $accommodation->bathrooms ?: '-' }} kupatila</div>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2 overflow-hidden rounded-[2rem]">
                        <img src="{{ $accommodation->hero_image_url }}" alt="{{ $accommodation->title }}" class="h-80 w-full object-cover">
                    </div>
                    @foreach ($accommodation->getMedia('gallery')->take(2) as $media)
                        <div class="overflow-hidden rounded-[1.6rem]">
                            <img src="{{ $media->getUrl() }}" alt="{{ $accommodation->title }}" class="h-48 w-full object-cover">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-6 pb-20 sm:px-8 lg:px-10">
        <section class="grid gap-6 lg:grid-cols-[1fr_0.8fr]">
            <div class="rounded-[2rem] bg-white p-8 shadow-[0_25px_80px_rgba(54,84,70,0.08)]">
                <p class="text-sm uppercase tracking-[0.35em] text-[var(--site-secondary)]">Opis</p>
                <h2 class="mt-4 font-serif text-4xl text-[var(--site-primary)]">Topao sajt za smestaje koji prodaju mir, prirodu i dozivljaj.</h2>
                <div class="mt-5 text-base leading-8 text-slate-700">
                    {!! nl2br(e($accommodation->description ?: $accommodation->short_description)) !!}
                </div>
            </div>

            <div class="rounded-[2rem] bg-[#dfe7da] p-8">
                <p class="text-sm uppercase tracking-[0.35em] text-[var(--site-secondary)]">Sadrzaji</p>
                <div class="mt-5 flex flex-wrap gap-3">
                    @foreach ($accommodation->amenities as $amenity)
                        <span class="rounded-full bg-white px-4 py-2 text-sm font-medium text-slate-700">{{ $amenity->name }}</span>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="mt-10 grid gap-6 lg:grid-cols-[0.85fr_1.15fr]">
            <div class="rounded-[2rem] bg-[var(--site-primary)] p-8 text-white shadow-[0_25px_80px_rgba(54,84,70,0.16)]">
                <p class="text-sm uppercase tracking-[0.35em] text-[var(--site-secondary)]">Lokacija</p>
                <h3 class="mt-4 text-3xl font-semibold">{{ $accommodation->city ?: 'Lokacija' }}</h3>
                <p class="mt-4 leading-8 text-white/74">{{ collect([$accommodation->address, $accommodation->city, $accommodation->region, $accommodation->country])->filter()->join(', ') }}</p>
                @if ($accommodation->google_maps_url)
                    <a href="{{ $accommodation->google_maps_url }}" target="_blank" rel="noreferrer" class="mt-6 inline-flex rounded-full bg-[var(--site-secondary)] px-5 py-3 text-sm font-semibold text-stone-950">
                        Otvori Google Maps
                    </a>
                @endif
            </div>

            <div class="rounded-[2rem] bg-white p-8 shadow-[0_25px_80px_rgba(54,84,70,0.08)]">
                <p class="text-sm uppercase tracking-[0.35em] text-[var(--site-secondary)]">Kontakt i rezervacija</p>
                <div class="mt-5 grid gap-3 text-sm text-slate-700 sm:grid-cols-3">
                    @if ($accommodation->contact_phone)<p><span class="font-semibold">Telefon:</span> {{ $accommodation->contact_phone }}</p>@endif
                    @if ($accommodation->contact_email)<p><span class="font-semibold">Email:</span> {{ $accommodation->contact_email }}</p>@endif
                    @if ($accommodation->whatsapp_number)<p><span class="font-semibold">WhatsApp:</span> {{ $accommodation->whatsapp_number }}</p>@endif
                </div>

                <form method="POST" action="{{ ($isThemeDemo ?? false) ? '#' : route('storefront.inquiry.store', $accommodation->slug) }}" class="mt-8 space-y-4">
                    @csrf
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-slate-700">Ime i prezime</label>
                            <input name="guest_name" value="{{ old('guest_name') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 outline-none focus:border-[var(--site-secondary)]">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                            <input type="email" name="guest_email" value="{{ old('guest_email') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 outline-none focus:border-[var(--site-secondary)]">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Telefon</label>
                            <input name="guest_phone" value="{{ old('guest_phone') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 outline-none focus:border-[var(--site-secondary)]">
                        </div>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Poruka</label>
                        <textarea name="message" rows="5" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 outline-none focus:border-[var(--site-secondary)]">{{ old('message') }}</textarea>
                    </div>

                    @if ($isThemeDemo ?? false)
                        <div class="rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-slate-600">
                            Demo tema prikazuje izgled forme za upit. Slanje upita je aktivno na objavljenim sajtovima.
                        </div>
                    @else
                        <button type="submit" class="w-full rounded-full bg-[var(--site-primary)] px-6 py-3 text-sm font-semibold text-white">
                            Posalji upit
                        </button>
                    @endif
                </form>
            </div>
        </section>
    </main>
</body>
</html>

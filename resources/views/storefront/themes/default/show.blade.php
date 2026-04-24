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
            --site-primary: {{ $accommodation->primary_color ?: '#234136' }};
            --site-secondary: {{ $accommodation->secondary_color ?: '#c6a66b' }};
        }
    </style>
</head>
<body class="bg-stone-950 text-stone-900">
    <div class="bg-[linear-gradient(180deg,_#10211b_0%,_#173329_42%,_#f5efe3_42%,_#f5efe3_100%)]">
        @if ($isThemeDemo ?? false)
            <div class="sticky top-0 z-50 border-b border-emerald-200 bg-emerald-50/95 backdrop-blur">
                <div class="mx-auto max-w-7xl px-6 py-3 text-sm font-medium text-emerald-900 sm:px-8 lg:px-10">
                    Demo pregled teme: ovo je prikaz izgleda sajta za izabranu temu.
                </div>
            </div>
        @elseif ($isPreview ?? false)
            <div class="sticky top-0 z-50 border-b border-amber-200 bg-amber-50/95 backdrop-blur">
                <div class="mx-auto max-w-7xl px-6 py-3 text-sm font-medium text-amber-900 sm:px-8 lg:px-10">
                    Preview rezim: sajt jos nije javno objavljen. Ovaj link sluzi za proveru izgleda pre objave.
                </div>
            </div>
        @endif

        <header class="relative isolate overflow-hidden">
            <div class="absolute inset-0 bg-black/35"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(198,166,107,0.26),_transparent_35%)]"></div>
            @if ($accommodation->hero_image_url)
                <img src="{{ $accommodation->hero_image_url }}" alt="{{ $accommodation->title }}" class="absolute inset-0 h-full w-full object-cover">
            @endif

            <div class="relative mx-auto flex min-h-[78svh] max-w-7xl flex-col justify-end px-6 pb-12 pt-24 sm:px-8 lg:px-10">
                <div class="max-w-3xl rounded-[2rem] border border-white/15 bg-black/35 p-8 backdrop-blur-md sm:p-10">
                    <p class="text-sm font-semibold uppercase tracking-[0.35em] text-[var(--site-secondary)]">{{ $accommodation->type?->label() }}</p>
                    <h1 class="mt-4 font-serif text-5xl leading-none text-white sm:text-6xl">{{ $accommodation->title }}</h1>
                    <p class="mt-5 text-lg text-white/80">{{ $accommodation->location_name ?: $accommodation->city }}</p>
                    @if ($accommodation->short_description)
                        <p class="mt-6 max-w-2xl text-base leading-8 text-white/85 sm:text-lg">{{ $accommodation->short_description }}</p>
                    @endif
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="#upit" class="rounded-full bg-[var(--site-secondary)] px-6 py-3 text-sm font-semibold text-stone-950 transition hover:opacity-90">Posalji upit</a>
                        @if ($accommodation->whatsapp_number)
                            <a href="https://wa.me/{{ preg_replace('/\D+/', '', $accommodation->whatsapp_number) }}" class="rounded-full border border-white/25 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">WhatsApp</a>
                        @endif
                    </div>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-6 pb-20 sm:px-8 lg:px-10">
            @if (session('status'))
                <div class="-mt-10 mb-8 rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            <section class="grid gap-4 rounded-[2rem] bg-white p-6 shadow-[0_24px_80px_rgba(15,23,42,0.08)] sm:grid-cols-2 lg:grid-cols-5 lg:p-8">
                <div><p class="text-xs uppercase tracking-[0.3em] text-stone-500">Gostiju</p><p class="mt-2 text-2xl font-semibold">{{ $accommodation->max_guests ?: '-' }}</p></div>
                <div><p class="text-xs uppercase tracking-[0.3em] text-stone-500">Sobe</p><p class="mt-2 text-2xl font-semibold">{{ $accommodation->bedrooms ?: '-' }}</p></div>
                <div><p class="text-xs uppercase tracking-[0.3em] text-stone-500">Kupatila</p><p class="mt-2 text-2xl font-semibold">{{ $accommodation->bathrooms ?: '-' }}</p></div>
                <div><p class="text-xs uppercase tracking-[0.3em] text-stone-500">Kvadratura</p><p class="mt-2 text-2xl font-semibold">{{ $accommodation->size_m2 ? $accommodation->size_m2 . ' m2' : '-' }}</p></div>
                <div><p class="text-xs uppercase tracking-[0.3em] text-stone-500">Cena od</p><p class="mt-2 text-2xl font-semibold">{{ $accommodation->price_from ? number_format((float) $accommodation->price_from, 0, ',', '.') . ' ' . $accommodation->currency : 'Na upit' }}</p></div>
            </section>

            <div class="mt-16 grid gap-10 lg:grid-cols-[1.2fr_0.8fr]">
                <section class="space-y-16">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.35em] text-[var(--site-secondary)]">Opis</p>
                        <h2 class="mt-3 font-serif text-4xl text-white sm:text-5xl">Mesto za odmor koje izgleda kao boutique retreat.</h2>
                        <div class="mt-6 rounded-[2rem] bg-white p-8 text-base leading-8 text-slate-700 shadow-[0_24px_80px_rgba(15,23,42,0.08)]">
                            {!! nl2br(e($accommodation->description ?: $accommodation->short_description)) !!}
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.35em] text-[var(--site-secondary)]">Galerija</p>
                        <div class="mt-6 grid gap-4 md:grid-cols-2">
                            @forelse ($accommodation->getMedia('gallery') as $media)
                                <div class="overflow-hidden rounded-[1.75rem] bg-white shadow-[0_24px_80px_rgba(15,23,42,0.08)]">
                                    <img src="{{ $media->getUrl() }}" alt="{{ $accommodation->title }}" class="h-72 w-full object-cover transition duration-500 hover:scale-105">
                                </div>
                            @empty
                                <div class="rounded-[1.75rem] bg-white p-8 text-slate-500 shadow-[0_24px_80px_rgba(15,23,42,0.08)]">Galerija ce uskoro biti dopunjena.</div>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.35em] text-[var(--site-secondary)]">Sadrzaji</p>
                        <div class="mt-6 flex flex-wrap gap-3">
                            @forelse ($accommodation->amenities as $amenity)
                                <span class="rounded-full bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-[0_12px_30px_rgba(15,23,42,0.08)]">{{ $amenity->name }}</span>
                            @empty
                                <span class="rounded-full bg-white px-4 py-2 text-sm font-medium text-slate-500 shadow-[0_12px_30px_rgba(15,23,42,0.08)]">Sadrzaji uskoro.</span>
                            @endforelse
                        </div>
                    </div>
                </section>

                <aside class="space-y-6">
                    <section class="rounded-[2rem] bg-white p-8 shadow-[0_24px_80px_rgba(15,23,42,0.08)]">
                        <p class="text-sm font-semibold uppercase tracking-[0.35em] text-[var(--site-secondary)]">Lokacija</p>
                        <h3 class="mt-3 text-2xl font-semibold text-slate-900">{{ $accommodation->city ?: 'Lokacija' }}</h3>
                        <p class="mt-3 leading-7 text-slate-600">{{ collect([$accommodation->address, $accommodation->city, $accommodation->region, $accommodation->country])->filter()->join(', ') }}</p>
                        @if ($accommodation->google_maps_url)
                            <a href="{{ $accommodation->google_maps_url }}" target="_blank" rel="noreferrer" class="mt-6 inline-flex rounded-full bg-[var(--site-primary)] px-5 py-3 text-sm font-semibold text-white">
                                Otvori Google Maps
                            </a>
                        @endif
                    </section>

                    <section id="upit" class="rounded-[2rem] bg-white p-8 shadow-[0_24px_80px_rgba(15,23,42,0.08)]">
                        <p class="text-sm font-semibold uppercase tracking-[0.35em] text-[var(--site-secondary)]">Kontakt i rezervacija</p>
                        <div class="mt-5 space-y-3 text-sm text-slate-700">
                            @if ($accommodation->contact_phone)<p><span class="font-semibold">Telefon:</span> {{ $accommodation->contact_phone }}</p>@endif
                            @if ($accommodation->contact_email)<p><span class="font-semibold">Email:</span> {{ $accommodation->contact_email }}</p>@endif
                            @if ($accommodation->whatsapp_number)<p><span class="font-semibold">WhatsApp:</span> {{ $accommodation->whatsapp_number }}</p>@endif
                        </div>

                        <form method="POST" action="{{ ($isThemeDemo ?? false) ? '#' : route('storefront.inquiry.store', $accommodation->slug) }}" class="mt-8 space-y-4">
                            @csrf
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">Ime i prezime</label>
                                <input name="guest_name" value="{{ old('guest_name') }}" required class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 outline-none transition focus:border-[var(--site-secondary)]">
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                                    <input type="email" name="guest_email" value="{{ old('guest_email') }}" required class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 outline-none transition focus:border-[var(--site-secondary)]">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-slate-700">Telefon</label>
                                    <input name="guest_phone" value="{{ old('guest_phone') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 outline-none transition focus:border-[var(--site-secondary)]">
                                </div>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-slate-700">Dolazak</label>
                                    <input type="date" name="check_in" value="{{ old('check_in') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 outline-none transition focus:border-[var(--site-secondary)]">
                                </div>
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-slate-700">Odlazak</label>
                                    <input type="date" name="check_out" value="{{ old('check_out') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 outline-none transition focus:border-[var(--site-secondary)]">
                                </div>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">Broj gostiju</label>
                                <input type="number" name="guests_count" min="1" value="{{ old('guests_count') }}" class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 outline-none transition focus:border-[var(--site-secondary)]">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">Poruka</label>
                                <textarea name="message" rows="5" required class="w-full rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 outline-none transition focus:border-[var(--site-secondary)]">{{ old('message') }}</textarea>
                            </div>
                            @if ($errors->any())
                                <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                                    {{ $errors->first() }}
                                </div>
                            @endif
                            @if ($isThemeDemo ?? false)
                                <div class="rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3 text-sm text-slate-600">
                                    Demo tema prikazuje izgled kontakt sekcije. Slanje upita je aktivno na objavljenim sajtovima.
                                </div>
                            @else
                                <button type="submit" class="w-full rounded-full bg-[var(--site-primary)] px-6 py-3 text-sm font-semibold text-white transition hover:opacity-95">
                                    Posalji upit
                                </button>
                            @endif
                        </form>
                    </section>
                </aside>
            </div>
        </main>

        <footer class="border-t border-white/10 bg-[#0c1713]">
            <div class="mx-auto flex max-w-7xl flex-col gap-3 px-6 py-8 text-sm text-white/70 sm:px-8 lg:flex-row lg:items-center lg:justify-between lg:px-10">
                <p>{{ $settings->platform_name }} • Premium mini-sajt za privatni smestaj</p>
                <p>{{ $accommodation->title }} • {{ $accommodation->city }}</p>
            </div>
        </footer>
    </div>
</body>
</html>

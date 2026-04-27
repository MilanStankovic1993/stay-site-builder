<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('site.home.meta_title') }}</title>
    <meta name="description" content="{{ __('site.home.meta_description') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="min-h-screen overflow-x-hidden bg-[radial-gradient(circle_at_top,_rgba(205,170,106,0.22),_transparent_22%),linear-gradient(180deg,_#f8f1e4_0%,_#efe5d2_45%,_#f5efe4_100%)]">
        <section class="relative isolate">
            <div class="absolute inset-x-0 top-0 -z-10 h-[34rem] bg-[radial-gradient(circle_at_20%_20%,_rgba(195,164,106,0.22),_transparent_35%),radial-gradient(circle_at_80%_0%,_rgba(31,58,50,0.16),_transparent_25%)]"></div>

            <div class="mx-auto max-w-7xl px-6 py-8 sm:px-8 lg:px-10">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <a href="/" class="text-sm font-semibold uppercase tracking-[0.38em] text-[var(--color-brand-forest)]">{{ __('site.brand') }}</a>
                    <div class="flex flex-wrap items-center gap-3 text-sm">
                        @include('shared.locale-switcher', ['tone' => 'light'])
                        <a href="/dashboard/register" class="rounded-full bg-[var(--color-brand-forest)] px-5 py-2.5 font-semibold text-white shadow-[0_18px_40px_rgba(31,58,50,0.18)] transition hover:-translate-y-0.5 hover:bg-black">
                            {{ __('site.nav.register') }}
                        </a>
                        <a href="/dashboard/login" class="rounded-full border border-[rgba(31,58,50,0.22)] bg-white/75 px-5 py-2.5 font-semibold text-[var(--color-brand-forest)] backdrop-blur transition hover:bg-white">
                            {{ __('site.nav.login') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="mx-auto grid max-w-7xl items-start gap-14 px-6 pb-20 pt-8 sm:px-8 lg:grid-cols-[1.05fr_0.95fr] lg:px-10 lg:pb-28">
                <div class="pt-6 lg:pt-14">
                    <div class="inline-flex items-center gap-3 rounded-full border border-white/60 bg-white/70 px-4 py-2 text-xs font-semibold uppercase tracking-[0.28em] text-[var(--color-brand-stone)] shadow-[0_18px_50px_rgba(31,58,50,0.08)] backdrop-blur">
                        <span class="h-2 w-2 rounded-full bg-[var(--color-brand-gold)]"></span>
                        {{ __('site.home.eyebrow') }}
                    </div>

                    <p class="mt-8 text-sm font-semibold uppercase tracking-[0.35em] text-[var(--color-brand-stone)]">{{ __('site.home.kicker') }}</p>
                    <h1 class="mt-5 max-w-4xl font-serif text-5xl leading-[0.95] text-[var(--color-brand-forest)] sm:text-6xl lg:text-[5.2rem]">
                        {{ __('site.home.title') }}
                    </h1>
                    <p class="mt-8 max-w-2xl text-lg leading-8 text-slate-700">
                        {{ __('site.home.description') }}
                    </p>

                    <div class="mt-10 grid gap-4 sm:grid-cols-3">
                        <div class="rounded-[1.6rem] border border-white/70 bg-white/70 p-5 shadow-[0_22px_70px_rgba(15,23,42,0.08)] backdrop-blur">
                            <p class="text-3xl font-serif text-[var(--color-brand-forest)]">{{ __('site.home.stats_steps_title') }}</p>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ __('site.home.stats_steps_text') }}</p>
                        </div>
                        <div class="rounded-[1.6rem] border border-white/70 bg-white/70 p-5 shadow-[0_22px_70px_rgba(15,23,42,0.08)] backdrop-blur">
                            <p class="text-3xl font-serif text-[var(--color-brand-forest)]">{{ __('site.home.stats_mobile_title') }}</p>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ __('site.home.stats_mobile_text') }}</p>
                        </div>
                        <div class="rounded-[1.6rem] border border-white/70 bg-white/70 p-5 shadow-[0_22px_70px_rgba(15,23,42,0.08)] backdrop-blur">
                            <p class="text-3xl font-serif text-[var(--color-brand-forest)]">{{ __('site.home.stats_inquiry_title') }}</p>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ __('site.home.stats_inquiry_text') }}</p>
                        </div>
                    </div>
                </div>

                <div class="relative lg:pt-8">
                    <div class="absolute -left-8 top-16 hidden h-32 w-32 rounded-full bg-[rgba(195,164,106,0.18)] blur-3xl lg:block"></div>
                    <div class="rounded-[2.2rem] border border-white/50 bg-[#173329] p-7 text-white shadow-[0_35px_120px_rgba(16,33,27,0.24)]">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs uppercase tracking-[0.32em] text-[var(--color-brand-gold)]">{{ __('site.home.how_it_works') }}</p>
                                <h2 class="mt-4 font-serif text-4xl text-white">{{ __('site.home.how_title') }}</h2>
                            </div>
                            <div class="rounded-full border border-white/10 bg-white/5 px-3 py-1 text-xs uppercase tracking-[0.25em] text-white/70">
                                {{ __('site.home.mvp') }}
                            </div>
                        </div>

                        <div class="mt-8 space-y-5">
                            <div class="rounded-[1.6rem] border border-white/10 bg-white/5 p-5">
                                <p class="text-lg font-semibold">{{ __('site.home.step_1_title') }}</p>
                                <p class="mt-2 text-sm leading-7 text-white/72">{{ __('site.home.step_1_text') }}</p>
                            </div>
                            <div class="rounded-[1.6rem] border border-white/10 bg-white/5 p-5">
                                <p class="text-lg font-semibold">{{ __('site.home.step_2_title') }}</p>
                                <p class="mt-2 text-sm leading-7 text-white/72">{{ __('site.home.step_2_text') }}</p>
                            </div>
                            <div class="rounded-[1.6rem] border border-white/10 bg-white/5 p-5">
                                <p class="text-lg font-semibold">{{ __('site.home.step_3_title') }}</p>
                                <p class="mt-2 text-sm leading-7 text-white/72">{{ __('site.home.step_3_text') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 rounded-[2rem] border border-white/60 bg-white/78 p-5 shadow-[0_25px_80px_rgba(15,23,42,0.09)] backdrop-blur">
                        <div class="grid gap-4 sm:grid-cols-3">
                            <a href="{{ route('storefront.demo-theme', 'default') }}" class="group rounded-[1.5rem] border border-stone-200 bg-white p-4 transition hover:-translate-y-1 hover:shadow-[0_22px_60px_rgba(15,23,42,0.12)]">
                                <div class="h-28 rounded-[1.15rem] bg-[linear-gradient(135deg,_rgba(24,49,41,0.94),_rgba(198,166,107,0.44)),url('/demo/placeholders/hero-villa.svg')] bg-cover bg-center"></div>
                                <p class="mt-4 text-xs uppercase tracking-[0.3em] text-[var(--color-brand-stone)]">Theme 01</p>
                                <p class="mt-2 font-serif text-3xl text-[var(--color-brand-forest)]">Default</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">{{ __('site.home.theme_1_text') }}</p>
                                <span class="mt-4 inline-flex text-sm font-semibold text-[var(--color-brand-forest)]">{{ __('site.nav.theme_preview') }}</span>
                            </a>

                            <a href="{{ route('storefront.demo-theme', 'luxury') }}" class="group rounded-[1.5rem] border border-stone-200 bg-white p-4 transition hover:-translate-y-1 hover:shadow-[0_22px_60px_rgba(15,23,42,0.12)]">
                                <div class="h-28 rounded-[1.15rem] bg-[linear-gradient(135deg,_rgba(18,25,23,0.95),_rgba(216,176,106,0.52)),url('/demo/placeholders/gallery-lounge.svg')] bg-cover bg-center"></div>
                                <p class="mt-4 text-xs uppercase tracking-[0.3em] text-[var(--color-brand-stone)]">Theme 02</p>
                                <p class="mt-2 font-serif text-3xl text-[var(--color-brand-forest)]">Luxury</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">{{ __('site.home.theme_2_text') }}</p>
                                <span class="mt-4 inline-flex text-sm font-semibold text-[var(--color-brand-forest)]">{{ __('site.nav.theme_preview') }}</span>
                            </a>

                            <a href="{{ route('storefront.demo-theme', 'nature') }}" class="group rounded-[1.5rem] border border-stone-200 bg-white p-4 transition hover:-translate-y-1 hover:shadow-[0_22px_60px_rgba(15,23,42,0.12)]">
                                <div class="h-28 rounded-[1.15rem] bg-[linear-gradient(135deg,_rgba(50,84,68,0.92),_rgba(185,139,77,0.45)),url('/demo/placeholders/gallery-bedroom.svg')] bg-cover bg-center"></div>
                                <p class="mt-4 text-xs uppercase tracking-[0.3em] text-[var(--color-brand-stone)]">Theme 03</p>
                                <p class="mt-2 font-serif text-3xl text-[var(--color-brand-forest)]">Nature</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">{{ __('site.home.theme_3_text') }}</p>
                                <span class="mt-4 inline-flex text-sm font-semibold text-[var(--color-brand-forest)]">{{ __('site.nav.theme_preview') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-7xl px-6 pb-24 sm:px-8 lg:px-10">
            <div class="grid gap-6 rounded-[2.2rem] border border-white/70 bg-white/80 p-8 shadow-[0_28px_90px_rgba(15,23,42,0.08)] backdrop-blur lg:grid-cols-[0.9fr_1.1fr] lg:p-10">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.35em] text-[var(--color-brand-gold)]">{{ __('site.home.benefits_kicker') }}</p>
                    <h2 class="mt-4 max-w-lg font-serif text-4xl leading-tight text-[var(--color-brand-forest)] sm:text-5xl">
                        {{ __('site.home.benefits_title') }}
                    </h2>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-[1.6rem] bg-[#f8f3ea] p-5">
                        <p class="text-lg font-semibold text-[var(--color-brand-forest)]">{{ __('site.home.benefit_1_title') }}</p>
                        <p class="mt-2 text-sm leading-7 text-slate-600">{{ __('site.home.benefit_1_text') }}</p>
                    </div>
                    <div class="rounded-[1.6rem] bg-[#f8f3ea] p-5">
                        <p class="text-lg font-semibold text-[var(--color-brand-forest)]">{{ __('site.home.benefit_2_title') }}</p>
                        <p class="mt-2 text-sm leading-7 text-slate-600">{{ __('site.home.benefit_2_text') }}</p>
                    </div>
                    <div class="rounded-[1.6rem] bg-[#f8f3ea] p-5">
                        <p class="text-lg font-semibold text-[var(--color-brand-forest)]">{{ __('site.home.benefit_3_title') }}</p>
                        <p class="mt-2 text-sm leading-7 text-slate-600">{{ __('site.home.benefit_3_text') }}</p>
                    </div>
                    <div class="rounded-[1.6rem] bg-[#f8f3ea] p-5">
                        <p class="text-lg font-semibold text-[var(--color-brand-forest)]">{{ __('site.home.benefit_4_title') }}</p>
                        <p class="mt-2 text-sm leading-7 text-slate-600">{{ __('site.home.benefit_4_text') }}</p>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>

@php
    $tone = $tone ?? 'light';
    $currentLocale = app()->getLocale();
    $redirectUrl = request()->fullUrl();

    $styles = match ($tone) {
        'dark' => [
            'wrapper' => 'inline-flex items-center rounded-full border border-white/15 bg-white/5 p-1 text-xs font-semibold uppercase tracking-[0.22em] text-white/70 backdrop-blur',
            'active' => 'rounded-full bg-white px-3 py-1.5 text-slate-900 shadow-sm',
            'inactive' => 'rounded-full px-3 py-1.5 text-white/65 transition hover:text-white',
        ],
        'panel' => [
            'wrapper' => 'inline-flex items-center rounded-full border border-gray-200 bg-white p-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-gray-500 shadow-sm',
            'active' => 'rounded-full bg-gray-900 px-3 py-1.5 text-white',
            'inactive' => 'rounded-full px-3 py-1.5 text-gray-500 transition hover:text-gray-900',
        ],
        default => [
            'wrapper' => 'inline-flex items-center rounded-full border border-[rgba(31,58,50,0.16)] bg-white/80 p-1 text-xs font-semibold uppercase tracking-[0.22em] text-[var(--color-brand-forest)] shadow-[0_12px_30px_rgba(15,23,42,0.06)] backdrop-blur',
            'active' => 'rounded-full bg-[var(--color-brand-forest)] px-3 py-1.5 text-white',
            'inactive' => 'rounded-full px-3 py-1.5 text-[var(--color-brand-forest)]/70 transition hover:text-[var(--color-brand-forest)]',
        ],
    };
@endphp

<div class="{{ $styles['wrapper'] }}">
    <a
        href="{{ route('locale.switch', ['locale' => 'sr', 'redirect' => $redirectUrl]) }}"
        class="{{ $currentLocale === 'sr' ? $styles['active'] : $styles['inactive'] }}"
    >
        SR
    </a>
    <a
        href="{{ route('locale.switch', ['locale' => 'en', 'redirect' => $redirectUrl]) }}"
        class="{{ $currentLocale === 'en' ? $styles['active'] : $styles['inactive'] }}"
    >
        EN
    </a>
</div>

<x-filament-widgets::widget>
    <section class="overflow-hidden rounded-[1.75rem] border border-stone-200 bg-white shadow-sm">
        <div class="border-b border-stone-100 bg-[linear-gradient(135deg,_#1f3a32,_#2b4a40)] px-6 py-6 text-white">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-200">{{ __('admin.pending.eyebrow') }}</p>
            <h3 class="mt-3 text-2xl font-semibold">{{ __('admin.pending.title') }}</h3>
            <p class="mt-2 max-w-2xl text-sm leading-7 text-white/75">
                {{ __('admin.pending.description') }}
            </p>
        </div>

        <div class="p-6">
            @if ($pendingUsers->isEmpty())
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-800">
                    {{ __('admin.pending.empty') }}
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($pendingUsers as $user)
                        <div class="flex flex-col gap-4 rounded-2xl border border-stone-200 bg-stone-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <p class="text-base font-semibold text-slate-900">{{ $user->name }}</p>
                                    @if ($user->isDemoAccount())
                                        <span class="rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-sky-700">
                                            Demo ACC
                                        </span>
                                    @endif
                                </div>
                                <p class="mt-1 text-sm text-slate-600">{{ $user->email }}</p>
                                <p class="mt-2 text-xs uppercase tracking-[0.2em] text-stone-500">
                                    {{ __('admin.pending.registered') }} {{ $user->created_at?->format('d.m.Y H:i') }}
                                </p>
                            </div>

                            <a
                                href="{{ \App\Filament\Resources\UserResource::getUrl('edit', ['record' => $user], panel: 'admin') }}"
                                class="inline-flex rounded-full bg-[#1f3a32] px-4 py-2 text-sm font-semibold text-white"
                            >
                                {{ __('admin.pending.open_user') }}
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5">
                    <a href="{{ $usersUrl }}" class="text-sm font-semibold text-[#1f3a32]">
                        {{ __('admin.pending.all_users') }}
                    </a>
                </div>
            @endif
        </div>
    </section>
</x-filament-widgets::widget>

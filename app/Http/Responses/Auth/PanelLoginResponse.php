<?php

namespace App\Http\Responses\Auth;

use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class PanelLoginResponse implements LoginResponse
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        $user = auth()->user();

        if ($user?->isSuperAdmin()) {
            return redirect()->to(Filament::getPanel('admin')->getUrl());
        }

        if ($user?->hasAnyRole(['owner', 'staff'])) {
            return redirect()->to(Filament::getPanel('dashboard')->getUrl());
        }

        return redirect()->intended(Filament::getUrl());
    }
}

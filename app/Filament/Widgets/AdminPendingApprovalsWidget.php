<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Widgets\Widget;

class AdminPendingApprovalsWidget extends Widget
{
    protected string $view = 'filament.widgets.admin-pending-approvals';

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        return [
            'pendingUsers' => User::query()
                ->where('is_active', false)
                ->latest()
                ->limit(8)
                ->get(),
            'usersUrl' => UserResource::getUrl(panel: 'admin'),
        ];
    }
}

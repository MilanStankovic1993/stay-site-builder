<?php

namespace Tests\Feature;

use App\Http\Responses\Auth\PanelLoginResponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PanelLoginResponseTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_is_redirected_to_admin_panel_after_login(): void
    {
        Role::findOrCreate('super_admin');

        $admin = User::factory()->create([
            'is_active' => true,
            'can_publish_sites' => true,
        ]);

        $admin->assignRole('super_admin');

        auth()->login($admin);

        $response = app(PanelLoginResponse::class)->toResponse(Request::create('/admin/login', 'POST'));

        $this->assertSame(url('/admin'), $response->getTargetUrl());
    }

    public function test_owner_is_redirected_to_owner_panel_after_login(): void
    {
        Role::findOrCreate('owner');

        $owner = User::factory()->create([
            'is_active' => true,
            'can_publish_sites' => false,
        ]);

        $owner->assignRole('owner');

        auth()->login($owner);

        $response = app(PanelLoginResponse::class)->toResponse(Request::create('/dashboard/login', 'POST'));

        $this->assertSame(url('/dashboard'), $response->getTargetUrl());
    }
}

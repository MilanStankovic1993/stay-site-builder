<?php

namespace Tests\Feature;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_access_admin_panel(): void
    {
        Role::findOrCreate('super_admin');

        $admin = User::factory()->create([
            'is_active' => true,
            'can_publish_sites' => true,
        ]);

        $admin->assignRole('super_admin');

        $this->assertTrue($admin->canAccessPanel(Filament::getPanel('admin')));

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('StaySite Builder Admin');
    }

    public function test_owner_cannot_access_admin_panel(): void
    {
        Role::findOrCreate('owner');

        $owner = User::factory()->create([
            'is_active' => true,
            'can_publish_sites' => false,
        ]);

        $owner->assignRole('owner');

        $this->assertFalse($owner->canAccessPanel(Filament::getPanel('admin')));

        $response = $this->actingAs($owner)->get('/admin');

        $this->assertContains($response->getStatusCode(), [302, 403]);
    }
}

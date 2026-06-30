<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogViewerAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole($role);

        return $user;
    }

    public function test_super_admin_can_access_log_viewer(): void
    {
        $this->actingAs($this->userWithRole('super_admin'))
            ->get('/log-viewer/api/files')
            ->assertOk();
    }

    public function test_admin_can_access_log_viewer(): void
    {
        $this->actingAs($this->userWithRole('admin'))
            ->get('/log-viewer/api/files')
            ->assertOk();
    }

    public function test_regular_user_cannot_access_log_viewer(): void
    {
        $this->actingAs($this->userWithRole('user'))
            ->get('/log-viewer/api/files')
            ->assertForbidden();
    }

    public function test_guest_cannot_access_log_viewer(): void
    {
        $this->get('/log-viewer/api/files')->assertForbidden();
    }
}

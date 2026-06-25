<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function makeUser(string $role): User
    {
        $user = User::factory()->create(['is_active' => true]);
        $user->assignRole($role);

        return $user;
    }

    public function test_admin_can_access_their_profile_inside_admin_panel(): void
    {
        $admin = $this->makeUser('admin');

        $response = $this->actingAs($admin)->get(route('admin.profile.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.profile');
    }

    public function test_super_admin_can_access_their_profile_inside_admin_panel(): void
    {
        $admin = $this->makeUser('super_admin');

        $response = $this->actingAs($admin)->get(route('admin.profile.edit'));

        $response->assertStatus(200);
    }

    public function test_admin_cannot_access_user_profile_page(): void
    {
        $admin = $this->makeUser('admin');

        $response = $this->actingAs($admin)->get(route('user.profile.edit'));

        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_user_dashboard_or_inventory(): void
    {
        $admin = $this->makeUser('admin');

        $this->actingAs($admin)->get(route('user.dashboard'))->assertStatus(403);
        $this->actingAs($admin)->get(route('user.inventory'))->assertStatus(403);
    }

    public function test_super_admin_cannot_access_user_panel(): void
    {
        $admin = $this->makeUser('super_admin');

        $this->actingAs($admin)->get(route('user.profile.edit'))->assertStatus(403);
        $this->actingAs($admin)->get(route('user.dashboard'))->assertStatus(403);
    }

    public function test_user_cannot_access_admin_profile_page(): void
    {
        $user = $this->makeUser('user');

        $response = $this->actingAs($user)->get(route('admin.profile.edit'));

        $response->assertStatus(403);
    }

    public function test_user_cannot_access_admin_dashboard(): void
    {
        $user = $this->makeUser('user');

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_guest_is_redirected_to_login_from_admin_profile(): void
    {
        $this->get(route('admin.profile.edit'))->assertRedirect(route('login'));
    }

    public function test_admin_can_update_their_profile(): void
    {
        $admin = $this->makeUser('admin');

        $response = $this->actingAs($admin)->put(route('admin.profile.update'), [
            'name' => 'Admin Baharu',
            'email' => 'admin-baharu@example.com',
            'phone' => '0123456789',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'Admin Baharu',
            'email' => 'admin-baharu@example.com',
            'phone' => '0123456789',
        ]);
    }

    public function test_admin_can_download_their_profile_pdf(): void
    {
        $admin = $this->makeUser('admin');

        $response = $this->actingAs($admin)->get(route('admin.profile.pdf'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_user_can_still_access_their_own_profile(): void
    {
        $district = District::create([
            'name' => 'Kuala Lumpur',
            'code' => 'KL',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'district_id' => $district->id,
            'is_active' => true,
        ]);
        $user->assignRole('user');

        $response = $this->actingAs($user)->get(route('user.profile.edit'));

        $response->assertStatus(200);
    }
}

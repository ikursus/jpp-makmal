<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\District;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfilePdfTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_authenticated_user_can_download_their_own_profile_pdf(): void
    {
        // 1. Create a District
        $district = District::create([
            'name' => 'Kuala Lumpur',
            'code' => 'KL',
            'is_active' => true
        ]);

        // 2. Create User
        $user = User::factory()->create([
            'district_id' => $district->id,
            'is_active' => true,
        ]);
        $user->assignRole('user');

        // 3. Authenticate user and call PDF route
        $response = $this->actingAs($user)
            ->get(route('user.profile.pdf'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $response->assertHeader('content-disposition', 'attachment; filename=Profil_Saya_' . str_replace(' ', '_', $user->name) . '.pdf');
        
        // Assert relationship is working
        $this->assertNotNull($user->district);
        $this->assertEquals('Kuala Lumpur', $user->district->name);
    }

    public function test_admin_can_download_any_user_profile_pdf(): void
    {
        // 1. Create a District
        $district = District::create([
            'name' => 'Kuala Lumpur',
            'code' => 'KL',
            'is_active' => true
        ]);

        // 2. Create normal user
        $user = User::factory()->create([
            'district_id' => $district->id,
            'is_active' => true,
        ]);

        // 3. Create Admin user
        $admin = User::factory()->create([
            'district_id' => $district->id,
            'is_active' => true,
        ]);
        $admin->assignRole('super_admin');

        // 4. Authenticate admin and call PDF route for the user
        $response = $this->actingAs($admin)
            ->get(route('admin.users.pdf', $user));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $response->assertHeader('content-disposition', 'attachment; filename=Profil_' . str_replace(' ', '_', $user->name) . '.pdf');
    }

    public function test_guest_cannot_download_profile_pdf(): void
    {
        $user = User::factory()->create();

        $response1 = $this->get(route('user.profile.pdf'));
        $response1->assertRedirect(route('login'));

        $response2 = $this->get(route('admin.users.pdf', $user));
        $response2->assertRedirect(route('login'));
    }
}

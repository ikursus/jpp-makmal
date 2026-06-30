<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_protected_route_requires_token(): void
    {
        $this->getJson('/api/v1/user')->assertStatus(401);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'ali@example.com',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'ali@example.com',
            'password' => 'password',
            'device_name' => 'Pixel 8',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email']]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'Pixel 8',
        ]);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'ali@example.com',
            'password' => 'password',
        ]);

        $this->postJson('/api/v1/login', [
            'email' => 'ali@example.com',
            'password' => 'wrong-password',
        ])->assertStatus(422)->assertJsonValidationErrors('email');
    }

    public function test_login_is_throttled_after_too_many_attempts(): void
    {
        User::factory()->create(['email' => 'ali@example.com', 'password' => 'password']);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/login', ['email' => 'ali@example.com', 'password' => 'wrong']);
        }

        $this->postJson('/api/v1/login', ['email' => 'ali@example.com', 'password' => 'password'])
            ->assertStatus(429);
    }

    public function test_me_returns_authenticated_user(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/user')
            ->assertOk()
            ->assertJsonPath('user.id', $user->id);
    }

    public function test_logout_revokes_current_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)->postJson('/api/v1/logout')->assertOk();
        $this->withToken($token)->getJson('/api/v1/user')->assertStatus(401);
    }
}

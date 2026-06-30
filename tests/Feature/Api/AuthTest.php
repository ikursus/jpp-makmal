<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_protected_route_requires_token(): void
    {
        $this->getJson('/api/v1/user')->assertStatus(401);
    }
}

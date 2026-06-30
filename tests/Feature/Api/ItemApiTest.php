<?php

namespace Tests\Feature\Api;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ItemApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_only_available_items(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Item::factory()->create(['name' => 'Mikroskop', 'status' => 'tersedia', 'available_quantity' => 5, 'is_active' => true]);
        Item::factory()->create(['status' => 'dipinjam', 'available_quantity' => 0]);
        Item::factory()->create(['status' => 'tersedia', 'available_quantity' => 0]);
        Item::factory()->create(['status' => 'tersedia', 'available_quantity' => 3, 'is_active' => false]);

        $this->getJson('/api/v1/items')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Mikroskop');
    }

    public function test_can_filter_items_by_search(): void
    {
        Sanctum::actingAs(User::factory()->create());
        Item::factory()->create(['name' => 'Mikroskop Digital']);
        Item::factory()->create(['name' => 'Beaker Kaca']);

        $this->getJson('/api/v1/items?search=Mikro')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Mikroskop Digital');
    }

    public function test_can_show_single_item(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $item = Item::factory()->create(['name' => 'Mikroskop']);

        $this->getJson("/api/v1/items/{$item->id}")
            ->assertOk()
            ->assertJsonPath('data.name', 'Mikroskop');
    }

    public function test_items_require_authentication(): void
    {
        $this->getJson('/api/v1/items')->assertStatus(401);
    }
}

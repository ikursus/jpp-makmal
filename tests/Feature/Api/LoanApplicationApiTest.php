<?php

namespace Tests\Feature\Api;

use App\Models\District;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LoanApplicationApiTest extends TestCase
{
    use RefreshDatabase;

    private function userWithDistrict(): User
    {
        $district = District::factory()->create();

        return User::factory()->create(['district_id' => $district->id]);
    }

    public function test_user_can_submit_loan_application(): void
    {
        $user = $this->userWithDistrict();
        Sanctum::actingAs($user);
        $item = Item::factory()->create(['available_quantity' => 5]);

        $this->postJson('/api/v1/loan-applications', [
            'items' => [['item_id' => $item->id, 'quantity' => 2]],
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'purpose' => 'Untuk program makmal sekolah',
        ])
            ->assertStatus(201)
            ->assertJsonPath('data.status', 'menunggu')
            ->assertJsonPath('data.items_count', 1);

        $this->assertDatabaseHas('loan_applications', [
            'user_id' => $user->id,
            'district_id' => $user->district_id,
            'status' => 'menunggu',
        ]);
        $this->assertDatabaseHas('loan_application_items', [
            'item_id' => $item->id,
            'quantity_requested' => 2,
        ]);
    }

    public function test_submission_fails_validation_with_short_purpose(): void
    {
        $user = $this->userWithDistrict();
        Sanctum::actingAs($user);
        $item = Item::factory()->create(['available_quantity' => 5]);

        $this->postJson('/api/v1/loan-applications', [
            'items' => [['item_id' => $item->id, 'quantity' => 1]],
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'purpose' => 'pendek',
        ])->assertStatus(422)->assertJsonValidationErrors('purpose');
    }

    public function test_submission_fails_when_stock_insufficient(): void
    {
        $user = $this->userWithDistrict();
        Sanctum::actingAs($user);
        $item = Item::factory()->create(['available_quantity' => 1]);

        $this->postJson('/api/v1/loan-applications', [
            'items' => [['item_id' => $item->id, 'quantity' => 5]],
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'purpose' => 'Untuk program makmal sekolah',
        ])->assertStatus(422);

        $this->assertDatabaseCount('loan_applications', 0);
    }

    public function test_submission_requires_authentication(): void
    {
        $this->postJson('/api/v1/loan-applications', [])->assertStatus(401);
    }
}

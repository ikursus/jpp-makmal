<?php

namespace Tests\Feature\Api;

use App\Models\District;
use App\Models\Item;
use App\Models\LoanApplication;
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
        ])
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors' => ['items']]);

        $this->assertDatabaseCount('loan_applications', 0);
    }

    public function test_submission_fails_when_end_date_before_start_date(): void
    {
        $user = $this->userWithDistrict();
        Sanctum::actingAs($user);
        $item = Item::factory()->create(['available_quantity' => 5]);

        $this->postJson('/api/v1/loan-applications', [
            'items' => [['item_id' => $item->id, 'quantity' => 1]],
            'start_date' => now()->addDays(3)->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'purpose' => 'Untuk program makmal sekolah',
        ])->assertStatus(422)->assertJsonValidationErrors('end_date');
    }

    public function test_submission_fails_when_user_has_no_district(): void
    {
        $user = User::factory()->create(['district_id' => null]);
        Sanctum::actingAs($user);
        $item = Item::factory()->create(['available_quantity' => 5]);

        $this->postJson('/api/v1/loan-applications', [
            'items' => [['item_id' => $item->id, 'quantity' => 1]],
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'purpose' => 'Untuk program makmal sekolah',
        ])->assertStatus(422)->assertJsonValidationErrors('district');

        $this->assertDatabaseCount('loan_applications', 0);
    }

    public function test_submission_requires_authentication(): void
    {
        $this->postJson('/api/v1/loan-applications', [])->assertStatus(401);
    }

    public function test_user_sees_only_their_own_applications(): void
    {
        $user = $this->userWithDistrict();
        $other = $this->userWithDistrict();
        LoanApplication::factory()->create(['user_id' => $user->id, 'district_id' => $user->district_id]);
        LoanApplication::factory()->create(['user_id' => $other->id, 'district_id' => $other->district_id]);

        Sanctum::actingAs($user);

        $this->getJson('/api/v1/loan-applications')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_user_can_view_their_own_application(): void
    {
        $user = $this->userWithDistrict();
        $app = LoanApplication::factory()->create(['user_id' => $user->id, 'district_id' => $user->district_id]);

        Sanctum::actingAs($user);

        $this->getJson("/api/v1/loan-applications/{$app->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $app->id);
    }

    public function test_user_cannot_view_another_users_application(): void
    {
        $user = $this->userWithDistrict();
        $other = $this->userWithDistrict();
        $app = LoanApplication::factory()->create(['user_id' => $other->id, 'district_id' => $other->district_id]);

        Sanctum::actingAs($user);

        $this->getJson("/api/v1/loan-applications/{$app->id}")->assertStatus(403);
    }

    public function test_listing_and_viewing_applications_requires_authentication(): void
    {
        $this->getJson('/api/v1/loan-applications')->assertStatus(401);
        $this->getJson('/api/v1/loan-applications/1')->assertStatus(401);
    }

    public function test_submission_rejects_duplicate_items(): void
    {
        $user = $this->userWithDistrict();
        Sanctum::actingAs($user);
        $item = Item::factory()->create(['available_quantity' => 10]);

        $this->postJson('/api/v1/loan-applications', [
            'items' => [
                ['item_id' => $item->id, 'quantity' => 3],
                ['item_id' => $item->id, 'quantity' => 3],
            ],
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'purpose' => 'Untuk program makmal sekolah',
        ])->assertStatus(422)->assertJsonValidationErrors('items.0.item_id');

        $this->assertDatabaseCount('loan_applications', 0);
    }
}

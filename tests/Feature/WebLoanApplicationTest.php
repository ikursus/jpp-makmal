<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Item;
use App\Models\LoanApplication;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebLoanApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_user_can_submit_loan_application_via_web(): void
    {
        $district = District::factory()->create();
        $user = User::factory()->create(['district_id' => $district->id, 'is_active' => true]);
        $user->assignRole('user');
        $item = Item::factory()->create(['available_quantity' => 5]);

        $response = $this->actingAs($user)->post('/user/loan-applications', [
            'items' => [$item->id => 2],
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'purpose' => 'Untuk program makmal sekolah',
        ]);

        $application = LoanApplication::where('user_id', $user->id)->firstOrFail();
        $response->assertRedirect(route('user.loan-applications.show', $application->id));
        $response->assertSessionHas('success', 'Permohonan pinjaman berjaya dihantar.');
        $this->assertDatabaseHas('loan_applications', [
            'user_id' => $user->id,
            'status' => 'menunggu',
        ]);
        $this->assertDatabaseHas('loan_application_items', [
            'item_id' => $item->id,
            'quantity_requested' => 2,
        ]);
    }

    public function test_web_submission_with_all_zero_quantities_is_rejected(): void
    {
        $district = District::factory()->create();
        $user = User::factory()->create(['district_id' => $district->id, 'is_active' => true]);
        $user->assignRole('user');
        $item = Item::factory()->create(['available_quantity' => 5]);

        $response = $this->actingAs($user)->from('/user/loan-applications/create')->post('/user/loan-applications', [
            'items' => [$item->id => 0],
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'purpose' => 'Untuk program makmal sekolah',
        ]);

        $response->assertSessionHasErrors('items');
        $this->assertDatabaseCount('loan_applications', 0);
    }

    public function test_web_submission_with_unknown_item_is_rejected(): void
    {
        $district = District::factory()->create();
        $user = User::factory()->create(['district_id' => $district->id, 'is_active' => true]);
        $user->assignRole('user');

        $response = $this->actingAs($user)->from('/user/loan-applications/create')->post('/user/loan-applications', [
            'items' => [999999 => 2],
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'purpose' => 'Untuk program makmal sekolah',
        ]);

        $response->assertSessionHasErrors('items');
        $this->assertDatabaseCount('loan_applications', 0);
    }

    public function test_web_submission_with_insufficient_stock_shows_error(): void
    {
        $district = District::factory()->create();
        $user = User::factory()->create(['district_id' => $district->id, 'is_active' => true]);
        $user->assignRole('user');
        $item = Item::factory()->create(['available_quantity' => 1]);

        $response = $this->actingAs($user)->from('/user/loan-applications/create')->post('/user/loan-applications', [
            'items' => [$item->id => 5],
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'purpose' => 'Untuk program makmal sekolah',
        ]);

        $response->assertRedirect('/user/loan-applications/create');
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('loan_applications', 0);
    }

    public function test_web_submission_without_district_shows_error(): void
    {
        $user = User::factory()->create(['district_id' => null, 'is_active' => true]);
        $user->assignRole('user');
        $item = Item::factory()->create(['available_quantity' => 5]);

        $response = $this->actingAs($user)->from('/user/loan-applications/create')->post('/user/loan-applications', [
            'items' => [$item->id => 2],
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'purpose' => 'Untuk program makmal sekolah',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('loan_applications', 0);
    }
}

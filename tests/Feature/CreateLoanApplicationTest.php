<?php

namespace Tests\Feature;

use App\Actions\CreateLoanApplication;
use App\Exceptions\InsufficientStockException;
use App\Models\District;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateLoanApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_application_with_items(): void
    {
        $district = District::factory()->create();
        $user = User::factory()->create(['district_id' => $district->id]);
        $item = Item::factory()->create(['available_quantity' => 5]);

        $application = app(CreateLoanApplication::class)->handle(
            $user,
            [['id' => $item->id, 'quantity' => 2]],
            now()->addDay()->toDateString(),
            now()->addDays(3)->toDateString(),
            'Untuk program makmal sekolah',
        );

        $this->assertSame('menunggu', $application->status);
        $this->assertSame($user->district_id, $application->district_id);
        $this->assertDatabaseHas('loan_application_items', [
            'loan_application_id' => $application->id,
            'item_id' => $item->id,
            'quantity_requested' => 2,
        ]);
    }

    public function test_throws_when_stock_insufficient(): void
    {
        $district = District::factory()->create();
        $user = User::factory()->create(['district_id' => $district->id]);
        $item = Item::factory()->create(['available_quantity' => 1]);

        $this->expectException(InsufficientStockException::class);

        app(CreateLoanApplication::class)->handle(
            $user,
            [['id' => $item->id, 'quantity' => 3]],
            now()->addDay()->toDateString(),
            now()->addDays(3)->toDateString(),
            'Untuk program makmal sekolah',
        );
    }

    public function test_creates_application_with_multiple_items(): void
    {
        $district = District::factory()->create();
        $user = User::factory()->create(['district_id' => $district->id]);
        $itemA = Item::factory()->create(['available_quantity' => 5]);
        $itemB = Item::factory()->create(['available_quantity' => 5]);

        $application = app(CreateLoanApplication::class)->handle(
            $user,
            [
                ['id' => $itemA->id, 'quantity' => 2],
                ['id' => $itemB->id, 'quantity' => 3],
            ],
            now()->addDay()->toDateString(),
            now()->addDays(3)->toDateString(),
            'Untuk program makmal sekolah',
        );

        $this->assertSame(2, $application->items()->count());
        $this->assertDatabaseHas('loan_application_items', [
            'loan_application_id' => $application->id,
            'item_id' => $itemA->id,
            'quantity_requested' => 2,
        ]);
        $this->assertDatabaseHas('loan_application_items', [
            'loan_application_id' => $application->id,
            'item_id' => $itemB->id,
            'quantity_requested' => 3,
        ]);
    }

    public function test_rolls_back_when_a_later_item_has_insufficient_stock(): void
    {
        $district = District::factory()->create();
        $user = User::factory()->create(['district_id' => $district->id]);
        $ok = Item::factory()->create(['available_quantity' => 5]);
        $low = Item::factory()->create(['available_quantity' => 1]);

        try {
            app(CreateLoanApplication::class)->handle(
                $user,
                [
                    ['id' => $ok->id, 'quantity' => 1],
                    ['id' => $low->id, 'quantity' => 3],
                ],
                now()->addDay()->toDateString(),
                now()->addDays(3)->toDateString(),
                'Untuk program makmal sekolah',
            );
            $this->fail('Expected InsufficientStockException was not thrown.');
        } catch (InsufficientStockException $e) {
            // expected
        }

        $this->assertDatabaseCount('loan_applications', 0);
        $this->assertDatabaseCount('loan_application_items', 0);
    }

    public function test_allows_quantity_equal_to_available_stock(): void
    {
        $district = District::factory()->create();
        $user = User::factory()->create(['district_id' => $district->id]);
        $item = Item::factory()->create(['available_quantity' => 2]);

        $application = app(CreateLoanApplication::class)->handle(
            $user,
            [['id' => $item->id, 'quantity' => 2]],
            now()->addDay()->toDateString(),
            now()->addDays(3)->toDateString(),
            'Untuk program makmal sekolah',
        );

        $this->assertSame('menunggu', $application->status);
        $this->assertDatabaseHas('loan_application_items', [
            'item_id' => $item->id,
            'quantity_requested' => 2,
        ]);
    }
}

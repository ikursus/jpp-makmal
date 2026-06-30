<?php

namespace Database\Factories;

use App\Models\District;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<LoanApplication> */
class LoanApplicationFactory extends Factory
{
    protected $model = LoanApplication::class;

    public function definition(): array
    {
        return [
            'application_no' => 'LA-'.now()->format('Ymd').'-'.fake()->unique()->numerify('###'),
            'user_id' => User::factory(),
            'district_id' => District::factory(),
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'purpose' => fake()->sentence(),
            'status' => 'menunggu',
        ];
    }
}

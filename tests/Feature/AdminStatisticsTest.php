<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\District;
use App\Models\Loan;
use App\Models\LoanApplication;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminStatisticsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_guest_user_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.statistics'));
        $response->assertRedirect(route('login'));
    }

    public function test_unauthorized_user_receives_403(): void
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
        $user->assignRole('user'); // Has 'view-dashboard', but not 'view-reports'

        $response = $this->actingAs($user)->get(route('admin.statistics'));
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_statistics_page(): void
    {
        $district = District::create([
            'name' => 'Kuala Lumpur',
            'code' => 'KL',
            'is_active' => true,
        ]);

        $admin = User::factory()->create([
            'district_id' => $district->id,
            'is_active' => true,
        ]);
        $admin->assignRole('admin'); // Has 'view-reports'

        $response = $this->actingAs($admin)->get(route('admin.statistics'));
        $response->assertStatus(200);
        $response->assertSeeLivewire(\App\Livewire\AdminStatistics::class);
    }

    public function test_statistics_component_calculates_correct_kpis_and_chart_data(): void
    {
        $district = District::create([
            'name' => 'Kuala Lumpur',
            'code' => 'KL',
            'is_active' => true,
        ]);

        $admin = User::factory()->create([
            'district_id' => $district->id,
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        // Create some sample data for selected period (current year)
        $currentYear = (int) now()->year;
        
        $user = User::factory()->create([
            'district_id' => $district->id,
            'is_active' => true,
        ]);
        $user->assignRole('user');

        // Create loan applications
        $app1 = LoanApplication::create([
            'application_no' => 'APP-01',
            'user_id' => $user->id,
            'district_id' => $district->id,
            'start_date' => now(),
            'end_date' => now()->addDays(7),
            'purpose' => 'Test',
            'status' => 'diluluskan',
            'created_at' => now(),
        ]);

        $app2 = LoanApplication::create([
            'application_no' => 'APP-02',
            'user_id' => $user->id,
            'district_id' => $district->id,
            'start_date' => now(),
            'end_date' => now()->addDays(7),
            'purpose' => 'Test 2',
            'status' => 'menunggu',
            'created_at' => now(),
        ]);

        // Create a loan
        Loan::create([
            'loan_no' => 'LOAN-01',
            'loan_application_id' => $app1->id,
            'user_id' => $user->id,
            'district_id' => $district->id,
            'start_date' => now(),
            'end_date' => now()->addDays(7),
            'status' => 'aktif',
            'created_by' => $admin->id,
        ]);

        Livewire::actingAs($admin)
            ->test(\App\Livewire\AdminStatistics::class)
            ->assertSet('selectedYear', $currentYear)
            ->assertSet('selectedMonth', '')
            ->assertSet('activeTab', 'loans')
            // Check KPI values
            ->assertViewHas('kpi', function ($kpi) {
                return $kpi['total_applications'] === 2 &&
                       $kpi['total_approved_loans'] === 1 &&
                       $kpi['active_loans'] === 1;
            })
            // Check user stats
            ->assertViewHas('userLoanStats', function ($stats) use ($user) {
                return $stats->first()->id === $user->id && $stats->first()->loans_count === 1;
            })
            ->assertViewHas('userAppStats', function ($stats) use ($user) {
                return $stats->first()->id === $user->id && $stats->first()->loan_applications_count === 2;
            })
            // Check district stats
            ->assertViewHas('districtStats', function ($stats) use ($district) {
                $kl = $stats->firstWhere('id', $district->id);
                return $kl->users_count === 2 && // admin and user
                       $kl->loan_applications_count === 2 &&
                       $kl->loans_count === 1;
            })
            // Change tab
            ->call('$set', 'activeTab', 'applications')
            ->assertSet('activeTab', 'applications')
            // Reset filters
            ->call('resetFilters')
            ->assertSet('selectedYear', $currentYear)
            ->assertSet('selectedMonth', '');
    }
}

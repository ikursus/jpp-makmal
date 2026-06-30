<?php

namespace Tests\Feature;

use App\Livewire\AdminLoanApplicationTable;
use App\Models\Category;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminLoanApplicationTableTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_can_render_loan_application_table_with_category_filter(): void
    {
        Category::factory()->create(['name' => 'Peralatan Ujian']);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Livewire::actingAs($admin)
            ->test(AdminLoanApplicationTable::class)
            ->assertSee('Peralatan Ujian');
    }
}

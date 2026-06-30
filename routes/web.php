<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminLoanController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DistrictController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\StorageLocationController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\UserInventoryController;
use App\Http\Controllers\User\UserLoanApplicationController;
use App\Http\Controllers\User\UserLoanController;
use App\Http\Controllers\User\UserProfileController;
use Illuminate\Support\Facades\Route;

// ========== PUBLIC ROUTES ==========
Route::get('/', PublicController::class)->name('home');
Route::get('/api-docs', function () {
    $html = str_replace(
        '__APP_URL__',
        rtrim(config('app.url'), '/'),
        file_get_contents(resource_path('api-docs.html')),
    );

    return response($html);
})->name('api.docs');
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ========== USER ROUTES (Pegawai Daerah) ==========
// Restricted to the `user` role so admins/super admins never land in the user
// panel — they stay in the admin panel even when viewing their own account.
Route::middleware(['auth', 'role:user'])
    ->prefix('user')->name('user.')->group(function () {

        Route::get('/dashboard', [UserDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/inventory', [UserInventoryController::class, 'index'])
            ->middleware('permission:view-inventory')->name('inventory');

        Route::get('/loan-applications', [UserLoanApplicationController::class, 'index'])
            ->middleware('permission:view-own-applications')->name('loan-applications.index');
        Route::get('/loan-applications/create', [UserLoanApplicationController::class, 'create'])
            ->middleware('permission:create-loan-application')->name('loan-applications.create');
        Route::post('/loan-applications', [UserLoanApplicationController::class, 'store'])
            ->middleware('permission:create-loan-application')->name('loan-applications.store');
        Route::get('/loan-applications/{id}', [UserLoanApplicationController::class, 'show'])
            ->middleware('permission:view-own-applications')->name('loan-applications.show');

        // Pinjaman & rekod pemulangan (read-only, milik user sahaja)
        Route::get('/loans', [UserLoanController::class, 'index'])
            ->middleware('permission:view-own-applications')->name('loans.index');
        Route::get('/loans/{id}', [UserLoanController::class, 'show'])
            ->middleware('permission:view-own-applications')->name('loans.show');

        Route::get('/profile', [UserProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [UserProfileController::class, 'update'])->name('profile.update');
        Route::get('/profile/pdf', [UserProfileController::class, 'downloadPdf'])->name('profile.pdf');
    });

// ========== ADMIN ROUTES (HQ) ==========
// Restricted to admin roles so the `user` role can never reach the admin panel,
// even though both roles share the `view-dashboard` permission.
Route::middleware(['auth', 'role:admin|super_admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->middleware('permission:view-dashboard')->name('dashboard');
    // Route::get('/dashboard', function () {

    //     $totalItems = Item::count();
    //     $availableItems = Item::where('status', 'tersedia')->count();
    //     $loanedItems = Item::where('status', 'dipinjam')->count();
    //     $pendingApplications = LoanApplication::where('status', 'menunggu')->count();
    //     $approvedApplications = LoanApplication::where('status', 'diluluskan')->count();
    //     $activeLoans = Loan::where('status', 'aktif')->count();
    //     $recentApplications = LoanApplication::with(['user', 'district'])->latest()->take(5)->get();
    //     $expiringItems = Item::whereNotNull('expiry_date')
    //         ->where('expiry_date', '<=', now()->addDays(30))
    //         ->where('expiry_date', '>=', now())
    //         ->get();

    //     return view('admin.dashboard', compact(
    //         'totalItems', 'availableItems', 'loanedItems',
    //         'pendingApplications', 'approvedApplications', 'activeLoans',
    //         'recentApplications', 'expiringItems'
    //     ));

    // })->middleware('permission:view-dashboard')->name('dashboard');

    // `show` is excluded for master-data resources: these have no show() method
    // / view and the UI links to edit, not show. Avoids latent 500s.
    Route::resource('districts', DistrictController::class)->except(['show'])
        ->middleware('permission:manage-districts');
    Route::resource('categories', CategoryController::class)->except(['show'])
        ->middleware('permission:manage-categories');
    Route::resource('items', ItemController::class)
        ->middleware('permission:manage-items');
    Route::resource('storage-locations', StorageLocationController::class)->except(['show'])
        ->middleware('permission:manage-storage-locations');
    Route::resource('users', UserManagementController::class)->except(['show'])
        ->middleware('permission:manage-users');
    Route::get('users/{user}/pdf', [UserManagementController::class, 'downloadProfilePdf'])
        ->middleware('permission:manage-users')
        ->name('users.pdf');

    // User export routes (all formats & options)
    Route::prefix('users/export')->name('users.export.')->middleware('permission:export-data')->group(function () {
        Route::get('/xlsx', [UserManagementController::class, 'exportXlsx'])->name('xlsx');
        Route::get('/csv', [UserManagementController::class, 'exportCsv'])->name('csv');
        Route::get('/ods', [UserManagementController::class, 'exportOds'])->name('ods');
        Route::get('/html', [UserManagementController::class, 'exportHtml'])->name('html');
        Route::get('/pdf', [UserManagementController::class, 'exportPdf'])->name('pdf');
        Route::get('/tcpdf', [UserManagementController::class, 'exportTcpdf'])->name('tcpdf');
        Route::get('/mpdf', [UserManagementController::class, 'exportMpdf'])->name('mpdf');
        Route::get('/view', [UserManagementController::class, 'exportView'])->name('view');
        Route::get('/multiple-sheets', [UserManagementController::class, 'exportMultipleSheets'])->name('multiple-sheets');
        Route::post('/store', [UserManagementController::class, 'exportStore'])->name('store');
        Route::post('/queue', [UserManagementController::class, 'exportQueue'])->name('queue');
        Route::get('/stream', [UserManagementController::class, 'exportStream'])->name('stream');
    });

    Route::get('/senarai-permohonan', [AdminLoanController::class, 'index'])
        ->middleware('permission:manage-loan-applications')->name('loan-applications.index');
    Route::get('/senarai-permohonan/{loanApplication}', [AdminLoanController::class, 'show'])
        ->middleware('permission:manage-loan-applications')->name('loan-applications.show');
    Route::put('/loan-applications/{loanApplication}/approve', [AdminLoanController::class, 'approve'])
        ->middleware('permission:approve-loan-applications')->name('loan-applications.approve');
    Route::put('/loan-applications/{loanApplication}/reject', [AdminLoanController::class, 'reject'])
        ->middleware('permission:approve-loan-applications')->name('loan-applications.reject');

    // Loan export routes (all formats & options)
    Route::prefix('loans/export')->name('loans.export.')->middleware('permission:export-data')->group(function () {
        Route::get('/xlsx', [AdminLoanController::class, 'exportXlsx'])->name('xlsx');
        Route::get('/csv', [AdminLoanController::class, 'exportCsv'])->name('csv');
        Route::get('/ods', [AdminLoanController::class, 'exportOds'])->name('ods');
        Route::get('/html', [AdminLoanController::class, 'exportHtml'])->name('html');
        Route::get('/pdf', [AdminLoanController::class, 'exportPdf'])->name('pdf');
        Route::get('/tcpdf', [AdminLoanController::class, 'exportTcpdf'])->name('tcpdf');
        Route::get('/mpdf', [AdminLoanController::class, 'exportMpdf'])->name('mpdf');
        Route::get('/view', [AdminLoanController::class, 'exportView'])->name('view');
        Route::get('/multiple-sheets', [AdminLoanController::class, 'exportMultipleSheets'])->name('multiple-sheets');
        Route::post('/store', [AdminLoanController::class, 'exportStore'])->name('store');
        Route::post('/queue', [AdminLoanController::class, 'exportQueue'])->name('queue');
        Route::get('/stream', [AdminLoanController::class, 'exportStream'])->name('stream');
    });

    Route::get('/loans', [AdminLoanController::class, 'loans'])
        ->middleware('permission:manage-loan-applications')->name('loans.index');
    Route::get('/loans/{loan}/return', [AdminLoanController::class, 'returnForm'])
        ->middleware('permission:manage-loan-applications')->name('loans.return.form');
    Route::put('/loans/{loan}/return', [AdminLoanController::class, 'processReturn'])
        ->middleware('permission:manage-loan-applications')->name('loans.return');

    Route::get('/reports', [AdminDashboardController::class, 'reports'])
        ->middleware('permission:view-reports')->name('reports');
    Route::get('/reports/export/{type}', [AdminDashboardController::class, 'export'])
        ->middleware('permission:export-data')->name('reports.export');
    Route::get('/statistics', function () {
        return view('admin.statistics');
    })->middleware('permission:view-reports')->name('statistics');

    // Profil sendiri (kekal dalam panel admin — tiada permission tambahan diperlukan
    // selain keahlian panel admin yang dikuatkuasakan oleh group middleware di atas).
    Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/pdf', [AdminProfileController::class, 'downloadPdf'])->name('profile.pdf');
});

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\LoanApplication;
use App\Models\Loan;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalItems = Item::count();
        $availableItems = Item::where('status', 'tersedia')->count();
        $loanedItems = Item::where('status', 'dipinjam')->count();
        $pendingApplications = LoanApplication::where('status', 'menunggu')->count();
        $approvedApplications = LoanApplication::where('status', 'diluluskan')->count();
        $activeLoans = Loan::where('status', 'aktif')->count();
        $recentApplications = LoanApplication::with(['user', 'district'])->latest()->take(5)->get();
        $expiringItems = Item::whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays(30))
            ->where('expiry_date', '>=', now())
            ->get();

        return view('admin.dashboard', compact(
            'totalItems', 'availableItems', 'loanedItems',
            'pendingApplications', 'approvedApplications', 'activeLoans',
            'recentApplications', 'expiringItems'
        ));
    }

    public function reports()
    {
        $totalItems = Item::count();
        $availableItems = Item::where('status', 'tersedia')->count();
        $loanedItems = Item::where('status', 'dipinjam')->count();
        $pendingApplications = LoanApplication::where('status', 'menunggu')->count();
        $approvedApplications = LoanApplication::where('status', 'diluluskan')->count();
        $activeLoans = Loan::where('status', 'aktif')->count();

        return view('admin.reports', compact(
            'totalItems', 'availableItems', 'loanedItems',
            'pendingApplications', 'approvedApplications', 'activeLoans'
        ));
    }

    public function export($type)
    {
        // TODO: Implement PDF/Excel export
        return back()->with('info', 'Ciri eksport akan datang');
    }
}

<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanApplication;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $activeLoans = Loan::where('user_id', $user->id)
            ->where('status', 'aktif')
            ->with('items.item')
            ->get();

        $recentApplications = LoanApplication::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $pendingCount = LoanApplication::where('user_id', $user->id)
            ->where('status', 'menunggu')
            ->count();

        $approvedCount = LoanApplication::where('user_id', $user->id)
            ->whereIn('status', ['diluluskan', 'dipinjam'])
            ->count();

        return view('user.dashboard', compact(
            'activeLoans', 'recentApplications', 'pendingCount', 'approvedCount'
        ));
    }
}

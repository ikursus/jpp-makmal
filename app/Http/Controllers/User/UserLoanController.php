<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Support\Facades\Auth;

class UserLoanController extends Controller
{
    public function index()
    {
        $loans = Loan::withCount('items')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('user.loans.index', compact('loans'));
    }

    public function show($id)
    {
        // Scoped to the authenticated user so a borrower can only ever see
        // their own loan / return records.
        $loan = Loan::with(['items.item', 'district'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('user.loans.show', compact('loan'));
    }
}

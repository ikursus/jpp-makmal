<?php

namespace App\Http\Controllers\User;

use App\Actions\CreateLoanApplication;
use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoanApplicationRequest;
use App\Models\Item;
use App\Models\LoanApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserLoanApplicationController extends Controller
{
    public function index()
    {
        $applications = LoanApplication::withCount('items')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('user.loan-application.index', compact('applications'));
    }

    public function create(Request $request)
    {
        $items = Item::with('category')
            ->where('is_active', true)
            ->where('status', 'tersedia')
            ->where('available_quantity', '>', 0)
            ->get();

        // Item pre-selected from the inventory list ("Mohon" button).
        $preselectId = (int) $request->query('item');

        return view('user.loan-application.create', compact('items', 'preselectId'));
    }

    public function store(StoreLoanApplicationRequest $request, CreateLoanApplication $action): RedirectResponse
    {
        if ($request->user()->district_id === null) {
            return back()
                ->with('error', 'Akaun anda tiada daerah berdaftar. Sila hubungi pentadbir.')
                ->withInput();
        }

        try {
            $application = $action->handle(
                Auth::user(),
                $request->getSelectedItems(),
                $request->validated('start_date'),
                $request->validated('end_date'),
                $request->validated('purpose'),
            );
        } catch (InsufficientStockException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('user.loan-applications.show', $application->id)
            ->with('success', 'Permohonan pinjaman berjaya dihantar.');
    }

    public function show($id)
    {
        $application = LoanApplication::with(['items.item', 'district'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('user.loan-application.show', compact('application'));
    }
}

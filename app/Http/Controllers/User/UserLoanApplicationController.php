<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoanApplicationRequest;
use App\Models\Item;
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem;
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

    public function store(StoreLoanApplicationRequest $request)
    {
        $validated = $request->validated();
        $validated['items'] = $request->getSelectedItems();

        $user = Auth::user();

        // Re-check stock at submit time.
        foreach ($validated['items'] as $itemData) {
            $item = Item::findOrFail($itemData['id']);
            if ($item->available_quantity < $itemData['quantity']) {
                return back()
                    ->with('error', "Stok {$item->name} tidak mencukupi. Tersedia: {$item->available_quantity}")
                    ->withInput();
            }
        }

        $application = LoanApplication::create([
            'application_no' => 'LA-' . now()->format('Ymd') . '-' . str_pad(LoanApplication::max('id') + 1, 3, '0', STR_PAD_LEFT),
            'user_id' => $user->id,
            'district_id' => $user->district_id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'purpose' => $validated['purpose'],
            'status' => 'menunggu',
        ]);

        foreach ($validated['items'] as $itemData) {
            LoanApplicationItem::create([
                'loan_application_id' => $application->id,
                'item_id' => $itemData['id'],
                'quantity_requested' => $itemData['quantity'],
            ]);
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

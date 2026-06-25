<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoanApplication;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\Item;
use App\Models\ItemCondition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminLoanController extends Controller
{
    public function index()
    {
        $applications = LoanApplication::with(['user', 'district', 'items.item'])
            ->latest()
            ->paginate(10);
        return view('admin.loan-applications.index', compact('applications'));
    }

    public function show(LoanApplication $loanApplication)
    {
        $loanApplication->load(['user', 'district', 'items.item', 'approvedBy']);
        return view('admin.loan-applications.show', compact('loanApplication'));
    }

    public function approve(Request $request, LoanApplication $loanApplication)
    {
        if ($loanApplication->status !== 'menunggu') {
            return back()->with('error', 'Permohonan ini sudah diproses.');
        }

        try {
            DB::transaction(function () use ($loanApplication) {
                $loanApplication->load('items');

                // Lock each item row and verify availability before mutating.
                // Locking prevents two concurrent approvals from over-booking.
                $lockedItems = [];
                foreach ($loanApplication->items as $appItem) {
                    $item = Item::whereKey($appItem->item_id)->lockForUpdate()->first();
                    if ($item->available_quantity < $appItem->quantity_requested) {
                        throw new \RuntimeException("Stok {$item->name} tidak mencukupi. Tersedia: {$item->available_quantity}");
                    }
                    $lockedItems[$appItem->item_id] = $item;
                }

                $loanApplication->update([
                    'status' => 'diluluskan',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);

                $loan = Loan::create([
                    'loan_no' => 'LN-' . now()->format('Ymd') . '-' . str_pad($loanApplication->id, 3, '0', STR_PAD_LEFT),
                    'loan_application_id' => $loanApplication->id,
                    'user_id' => $loanApplication->user_id,
                    'district_id' => $loanApplication->district_id,
                    'start_date' => $loanApplication->start_date,
                    'end_date' => $loanApplication->end_date,
                    'status' => 'aktif',
                    'created_by' => auth()->id(),
                ]);

                foreach ($loanApplication->items as $appItem) {
                    $item = $lockedItems[$appItem->item_id];
                    $item->decrement('available_quantity', $appItem->quantity_requested);
                    if ($item->available_quantity <= 0) {
                        $item->update(['status' => 'dipinjam']);
                    }

                    LoanItem::create([
                        'loan_id' => $loan->id,
                        'item_id' => $appItem->item_id,
                        'quantity_loaned' => $appItem->quantity_requested,
                        'condition_before' => $item->condition,
                    ]);

                    $appItem->update(['quantity_approved' => $appItem->quantity_requested]);
                }
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.loans.index')
            ->with('success', 'Permohonan #' . $loanApplication->application_no . ' telah diluluskan.');
    }

    public function reject(Request $request, LoanApplication $loanApplication)
    {
        if ($loanApplication->status !== 'menunggu') {
            return back()->with('error', 'Permohonan ini sudah diproses.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|min:5',
        ]);

        $loanApplication->update([
            'status' => 'ditolak',
            'rejection_reason' => $request->rejection_reason,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('admin.loan-applications.index')
            ->with('success', 'Permohonan #' . $loanApplication->application_no . ' telah ditolak.');
    }

    public function loans()
    {
        $loans = Loan::with(['user', 'district', 'items.item'])
            ->latest()
            ->paginate(10);
        return view('admin.loans.index', compact('loans'));
    }

    public function returnForm(Loan $loan)
    {
        if ($loan->status === 'dipulangkan') {
            return redirect()->route('admin.loans.index')
                ->with('info', 'Pinjaman ini telah dipulangkan sepenuhnya.');
        }

        $loan->load(['user', 'district', 'items.item']);
        return view('admin.loans.return', compact('loan'));
    }

    public function processReturn(Request $request, Loan $loan)
    {
        if ($loan->status === 'dipulangkan') {
            return back()->with('error', 'Pinjaman ini telah dipulangkan sepenuhnya.');
        }

        $validated = $request->validate([
            'returns' => 'required|array',
            'returns.*.quantity' => 'required|integer|min:0',
            'returns.*.condition' => 'required|in:baik,rosak,service',
        ], [
            'returns.required' => 'Tiada data pemulangan diterima.',
        ]);

        try {
            DB::transaction(function () use ($loan, $validated) {
                $loan->load('items.item');
                $totalReturnedNow = 0;

                foreach ($loan->items as $loanItem) {
                    $input = $validated['returns'][$loanItem->id] ?? null;
                    if (!$input) {
                        continue;
                    }

                    $qty = (int) $input['quantity'];
                    if ($qty <= 0) {
                        continue;
                    }

                    $outstanding = $loanItem->quantity_loaned - $loanItem->quantity_returned;
                    if ($qty > $outstanding) {
                        throw new \RuntimeException("Kuantiti pulang bagi {$loanItem->item->name} melebihi baki ({$outstanding}).");
                    }

                    $condition = $input['condition'];
                    $totalReturnedNow += $qty;

                    // Update the loan item line
                    $loanItem->quantity_returned += $qty;
                    $loanItem->condition_after = $condition;
                    if ($loanItem->quantity_returned >= $loanItem->quantity_loaned) {
                        $loanItem->returned_at = now();
                    }
                    $loanItem->save();

                    // Restore stock (lock the item row to avoid races)
                    $item = Item::whereKey($loanItem->item_id)->lockForUpdate()->first();
                    $item->increment('available_quantity', $qty);
                    if ($item->status === 'dipinjam' && $item->available_quantity > 0) {
                        $item->status = 'tersedia';
                    }

                    // Log a condition change if the returned condition differs
                    if ($condition !== $item->condition) {
                        ItemCondition::create([
                            'item_id' => $item->id,
                            'previous_condition' => $item->condition,
                            'new_condition' => $condition,
                            'notes' => "Perubahan keadaan semasa pemulangan pinjaman {$loan->loan_no}.",
                            'changed_by' => auth()->id(),
                        ]);
                        $item->condition = $condition;
                    }

                    $item->save();
                }

                if ($totalReturnedNow === 0) {
                    throw new \RuntimeException('Sila masukkan kuantiti untuk sekurang-kurangnya satu barang.');
                }

                // Mark the loan fully returned only when every line is settled
                $fullyReturned = $loan->items->every(
                    fn ($li) => $li->quantity_returned >= $li->quantity_loaned
                );
                if ($fullyReturned) {
                    $loan->status = 'dipulangkan';
                    $loan->actual_return_date = now();
                    $loan->save();
                }
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('admin.loans.index')
            ->with('success', "Pemulangan bagi pinjaman {$loan->loan_no} berjaya direkod.");
    }
}

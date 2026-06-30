<?php

namespace App\Actions;

use App\Exceptions\InsufficientStockException;
use App\Models\Item;
use App\Models\LoanApplication;
use App\Models\LoanApplicationItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateLoanApplication
{
    /**
     * @param  array<int, array{id: int, quantity: int}>  $items
     */
    public function handle(User $user, array $items, string $startDate, string $endDate, string $purpose): LoanApplication
    {
        return DB::transaction(function () use ($user, $items, $startDate, $endDate, $purpose) {
            foreach ($items as $row) {
                $item = Item::findOrFail($row['id']);

                if ($item->available_quantity < $row['quantity']) {
                    throw new InsufficientStockException(
                        "Stok {$item->name} tidak mencukupi. Tersedia: {$item->available_quantity}"
                    );
                }
            }

            $application = LoanApplication::create([
                'application_no' => 'LA-'.now()->format('Ymd').'-'.str_pad((string) (LoanApplication::max('id') + 1), 3, '0', STR_PAD_LEFT),
                'user_id' => $user->id,
                'district_id' => $user->district_id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'purpose' => $purpose,
                'status' => 'menunggu',
            ]);

            foreach ($items as $row) {
                LoanApplicationItem::create([
                    'loan_application_id' => $application->id,
                    'item_id' => $row['id'],
                    'quantity_requested' => $row['quantity'],
                ]);
            }

            return $application->load('items.item', 'district');
        });
    }
}

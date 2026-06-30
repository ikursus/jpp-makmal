<?php

namespace App\Http\Requests;

use App\Models\Item;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreLoanApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'items.*' => 'integer|min:0',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'purpose' => 'required|string|min:10',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $selected = $this->getSelectedItems();

            if (empty($selected)) {
                $validator->errors()->add('items', 'Sila masukkan kuantiti sekurang-kurangnya satu barang.');

                return;
            }

            $selectedIds = collect($selected)->pluck('id');
            $existingIds = Item::whereIn('id', $selectedIds)->pluck('id');
            $missingIds = $selectedIds->diff($existingIds);

            if ($missingIds->isNotEmpty()) {
                $validator->errors()->add('items', 'Sebahagian barang yang dipilih tidak sah.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Sila pilih sekurang-kurangnya satu barang dengan memasukkan kuantiti.',
            'items.min' => 'Sila pilih sekurang-kurangnya satu barang dengan memasukkan kuantiti.',
            'purpose.min' => 'Tujuan pinjaman mesti sekurang-kurangnya 10 aksara.',
        ];
    }

    public function getSelectedItems(): array
    {
        return collect($this->input('items', []))
            ->map(fn ($quantity, $id) => ['id' => (int) $id, 'quantity' => (int) $quantity])
            ->filter(fn ($row) => $row['quantity'] > 0)
            ->values()
            ->all();
    }
}

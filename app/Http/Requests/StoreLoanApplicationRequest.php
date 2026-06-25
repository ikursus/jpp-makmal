<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'items.*.id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'purpose' => 'required|string|min:10',
        ];
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

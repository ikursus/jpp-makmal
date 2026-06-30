<?php

namespace App\Http\Requests\Api;

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
            'items.*.item_id' => 'required|integer|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'purpose' => 'required|string|min:10',
        ];
    }

    /**
     * @return array<int, array{id: int, quantity: int}>
     */
    public function normalizedItems(): array
    {
        return collect($this->input('items'))
            ->map(fn ($row) => ['id' => (int) $row['item_id'], 'quantity' => (int) $row['quantity']])
            ->all();
    }
}

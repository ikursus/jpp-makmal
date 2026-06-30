<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanApplicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'application_no' => $this->application_no,
            'status' => $this->status,
            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'purpose' => $this->purpose,
            'district' => $this->whenLoaded('district', fn () => $this->district ? [
                'id' => $this->district->id,
                'name' => $this->district->name,
            ] : null),
            'items' => LoanApplicationItemResource::collection($this->whenLoaded('items')),
            'items_count' => $this->whenLoaded('items',
                fn () => $this->items->count(),
                $this->items_count ?? null
            ),
            'rejection_reason' => $this->rejection_reason,
            'created_at' => $this->created_at,
        ];
    }
}

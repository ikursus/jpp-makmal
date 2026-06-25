<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanApplicationItem extends Model
{
    protected $fillable = ['loan_application_id', 'item_id', 'quantity_requested', 'quantity_approved'];

    public function loanApplication()
    {
        return $this->belongsTo(LoanApplication::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}

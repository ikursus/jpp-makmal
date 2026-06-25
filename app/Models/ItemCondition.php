<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemCondition extends Model
{
    protected $fillable = ['item_id', 'previous_condition', 'new_condition', 'notes', 'changed_by'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}

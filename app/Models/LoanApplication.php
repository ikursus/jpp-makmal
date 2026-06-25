<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanApplication extends Model
{
    protected $fillable = [
        'application_no', 'user_id', 'district_id', 'start_date', 'end_date',
        'purpose', 'status', 'rejection_reason', 'approved_by', 'approved_at', 'notes'
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(LoanApplicationItem::class);
    }

    public function loan()
    {
        return $this->hasOne(Loan::class);
    }
}

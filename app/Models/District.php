<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'address', 'phone', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function loanApplications()
    {
        return $this->hasMany(LoanApplication::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}

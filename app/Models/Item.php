<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'quantity', 'available_quantity',
        'condition', 'status', 'category_id', 'storage_location_id',
        'expiry_date', 'image', 'qr_code', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'expiry_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function storageLocation()
    {
        return $this->belongsTo(StorageLocation::class);
    }

    public function itemConditions()
    {
        return $this->hasMany(ItemCondition::class);
    }

    public function loanApplicationItems()
    {
        return $this->hasMany(LoanApplicationItem::class);
    }

    public function loanItems()
    {
        return $this->hasMany(LoanItem::class);
    }
}

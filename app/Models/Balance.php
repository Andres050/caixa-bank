<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    use HasFactory;

    public $timestamps = false;

    private $balanceTypes = [
        'closingBooked',
        'forwardAvailable',
    ];

    protected $fillable = [
        'amount',
        'currency',
        'balance_type',
        'reference_date',
        'account_id',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'code');
    }

    public function scopeBalanceTypeForward($query)
    {
        return $query->where('balance_type', $this->balanceTypes[1]);
    }

    public function scopeBalanceTypeClosing($query)
    {
        return $query->where('balance_type', $this->balanceTypes[0]);
    }
}

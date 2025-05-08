<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $casts = [
        'reference_date' => 'date',
    ];

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

    public function scopeCurrentMonth($query)
    {
        $date = session('month') ?? now()->format('m-Y');
        $date = \DateTime::createFromFormat('m-Y', $date);

        if ($date === false) {
            return null;
        }

        $startDate = $date->modify('first day of this month')->format('Y-m-d');
        $endDate = $date->modify('last day of this month')->format('Y-m-d');
        return $query->whereBetween('reference_date', [$startDate, $endDate]);
    }

    public function scopeLastInstance($query)
    {
        return $query->orderBy('reference_date', 'desc')->first();
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

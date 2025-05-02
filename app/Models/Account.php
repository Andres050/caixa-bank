<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $casts = [
        'created' => 'datetime',
        'last_accessed' => 'datetime',
        'transactions_disabled_date' => 'datetime',
        'balance_disabled_date' => 'datetime',
    ];

    protected $fillable = [
        'id',
        'name',
        'iban',
        'bban',
        'status',
        'owner_name',
        'created',
        'last_accessed',
        'institution_id',
        'user_id'
    ];

    public function getCodeAttribute()
    {
        return $this->attributes['id'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'account_id', 'code');
    }

    public function balances()
    {
        return $this->hasMany(Balance::class, 'account_id', 'code');
    }

    public function getTransactionsCurrentMonthAttribute()
    {
        return $this->transactions()
            ->whereBetween('bookingDate', [now()->format('Y-m-01 00:00:00'), now()->format('Y-m-t 23:59:59')])
            ->orderBy('bookingDate', 'desc')
            ->get();
    }

    public function getExpensesAttribute()
    {
        return $this->transactions()
            ->whereBetween('bookingDate', [now()->format('Y-m-01 00:00:00'), now()->format('Y-m-t 23:59:59')])
            // Where transactionAmount_amount is less than 0
            ->where('transactionAmount_amount', '<', 0)
            ->sum('transactionAmount_amount');
    }

    public function getIncomeAttribute()
    {
        return $this->transactions()
            ->whereBetween('bookingDate', [now()->format('Y-m-01 00:00:00'), now()->format('Y-m-t 23:59:59')])
            // Where transactionAmount_amount is greater than 0
            ->where('transactionAmount_amount', '>', 0)
            ->sum('transactionAmount_amount');
    }
}

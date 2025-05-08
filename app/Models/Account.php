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

    public function bankDataSync()
    {
        return $this->hasMany(BankDataSync::class, 'account_id', 'code');
    }

    public function scopeSortOrder($query)
    {
        return $query->orderBy('order', 'asc');
    }

    public function getUsedColors(&$usedColors)
    {
        do {
            $r = mt_rand(0, 180);
            $g = mt_rand(0, 255);
            $b = mt_rand(180, 255);
            $color = sprintf("#%02X%02X%02X", $r, $g, $b);
        } while (in_array($color, $usedColors));
        $usedColors[] = $color;
        return $color;
    }

    public function getTransactionsDisabledAttribute()
    {
        return $this->transactions_disabled_date !== null && $this->transactions_disabled_date->isFuture();
    }

    public function getBalanceDisabledAttribute()
    {
        return $this->balance_disabled_date !== null && $this->balance_disabled_date->isFuture();
    }

    public function getTransactionsCurrentMonthAttribute()
    {
        $date = session('month') ?? now()->format('m-Y');
        $parsedDate = \Carbon\Carbon::createFromFormat('m-Y', $date);

        $startOfMonth = $parsedDate->copy()->startOfMonth()->startOfDay();
        $endOfMonth = $parsedDate->copy()->endOfMonth()->endOfDay();

        return $this->transactions()
            ->whereBetween('bookingDate', [$startOfMonth, $endOfMonth])
            ->orderDate()
            ->get();
    }

    public function getTransactionsExpensesCurrentMonthAttribute()
    {
        $date = session('month') ?? now()->format('m-Y');
        $parsedDate = \Carbon\Carbon::createFromFormat('m-Y', $date);

        $startOfMonth = $parsedDate->copy()->startOfMonth()->startOfDay();
        $endOfMonth = $parsedDate->copy()->endOfMonth()->endOfDay();

        return $this->transactions()
            ->whereBetween('bookingDate', [$startOfMonth, $endOfMonth])
            ->where('transactionAmount_amount', '<', 0)
            ->orderDate()
            ->get();
    }

    public function getTransactionsIncomeCurrentMonthAttribute()
    {
        $date = session('month') ?? now()->format('m-Y');
        $parsedDate = \Carbon\Carbon::createFromFormat('m-Y', $date);

        $startOfMonth = $parsedDate->copy()->startOfMonth()->startOfDay();
        $endOfMonth = $parsedDate->copy()->endOfMonth()->endOfDay();

        return $this->transactions()
            ->whereBetween('bookingDate', [$startOfMonth, $endOfMonth])
            ->where('transactionAmount_amount', '>', 0)
            ->orderDate()
            ->get();
    }

    public function getExpensesAttribute()
    {
        $date = session('month') ?? now()->format('m-Y');
        $parsedDate = \Carbon\Carbon::createFromFormat('m-Y', $date);

        $startOfMonth = $parsedDate->copy()->startOfMonth()->startOfDay();
        $endOfMonth = $parsedDate->copy()->endOfMonth()->endOfDay();

        return $this->transactions()
            ->whereBetween('bookingDate', [$startOfMonth, $endOfMonth])
            ->where('transactionAmount_amount', '<', 0)
            ->sum('transactionAmount_amount');
    }

    public function getIncomeAttribute()
    {
        $date = session('month') ?? now()->format('m-Y');
        $parsedDate = \Carbon\Carbon::createFromFormat('m-Y', $date);

        $startOfMonth = $parsedDate->copy()->startOfMonth()->startOfDay();
        $endOfMonth = $parsedDate->copy()->endOfMonth()->endOfDay();

        return $this->transactions()
            ->whereBetween('bookingDate', [$startOfMonth, $endOfMonth])
            ->where('transactionAmount_amount', '>', 0)
            ->sum('transactionAmount_amount');
    }
}

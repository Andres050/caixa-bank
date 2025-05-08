<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankDataSync extends Model
{
    use HasFactory;

    public $timestamps = true;

    public static $TRANSACTIONS_TYPE = 'transaction';
    public static $BALANCE_TYPE = 'balance';

    public static $ACCOUNT_TYPE = 'account';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'last_fetched_at' => 'datetime',
    ];

    protected $fillable = [
        'data_type',
        'status',
        'account_id',
        'last_fetched_at',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function scopeDataTypeTransaction($query)
    {
        return $query->where('data_type', self::$TRANSACTIONS_TYPE);
    }

    public function scopeDataTypeBalance($query)
    {
        return $query->where('data_type', self::$BALANCE_TYPE);
    }

    public function scopeDataTypeAccount($query)
    {
        return $query->where('data_type', self::$ACCOUNT_TYPE);
    }
}

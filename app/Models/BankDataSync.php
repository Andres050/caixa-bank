<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string|null $data_type
 * @property string|null $status
 * @property string $account_id
 * @property Carbon $last_fetched_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Account $account
 * @method static Builder|BankDataSync dataTypeAccount()
 * @method static Builder|BankDataSync dataTypeBalance()
 * @method static Builder|BankDataSync dataTypeTransaction()
 * @method static Builder|BankDataSync newModelQuery()
 * @method static Builder|BankDataSync newQuery()
 * @method static Builder|BankDataSync query()
 * @method static Builder|BankDataSync whereAccountId($value)
 * @method static Builder|BankDataSync whereCreatedAt($value)
 * @method static Builder|BankDataSync whereDataType($value)
 * @method static Builder|BankDataSync whereId($value)
 * @method static Builder|BankDataSync whereLastFetchedAt($value)
 * @method static Builder|BankDataSync whereStatus($value)
 * @method static Builder|BankDataSync whereUpdatedAt($value)
 * @mixin Eloquent
 */
class BankDataSync extends Model
{
    use HasFactory;

    public $timestamps = true;

    public static string $TRANSACTIONS_TYPE = 'transaction';
    public static string $BALANCE_TYPE = 'balance';

    public static string $ACCOUNT_TYPE = 'account';

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

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Scope a query to only include transactions.
     *
     * @noinspection PhpUnused
     * @param $query
     * @return mixed
     */
    public function scopeDataTypeTransaction($query): mixed
    {
        return $query->where('data_type', self::$TRANSACTIONS_TYPE);
    }

    /**
     * Scope a query to only include balance.
     *
     * @noinspection PhpUnused
     * @param $query
     * @return mixed
     */
    public function scopeDataTypeBalance($query): mixed
    {
        return $query->where('data_type', self::$BALANCE_TYPE);
    }

    /**
     * Scope a query to only include account.
     *
     * @noinspection PhpUnused
     * @param $query
     * @return mixed
     */
    public function scopeDataTypeAccount($query): mixed
    {
        return $query->where('data_type', self::$ACCOUNT_TYPE);
    }
}

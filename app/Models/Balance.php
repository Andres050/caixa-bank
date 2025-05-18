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
 * @property string|null $amount
 * @property string|null $currency
 * @property string|null $balance_type
 * @property Carbon|null $reference_date
 * @property string $account_id
 * @property-read Account $account
 * @property-read mixed $balance_types
 * @property-read mixed $code
 * @method static Builder|Balance balanceTypeClosing()
 * @method static Builder|Balance balanceTypeForward()
 * @method static Builder|Balance currentMonth()
 * @method static Builder|Balance lastInstance()
 * @method static Builder|Balance newModelQuery()
 * @method static Builder|Balance newQuery()
 * @method static Builder|Balance query()
 * @method static Builder|Balance whereAccountId($value)
 * @method static Builder|Balance whereAmount($value)
 * @method static Builder|Balance whereBalanceType($value)
 * @method static Builder|Balance whereCurrency($value)
 * @method static Builder|Balance whereId($value)
 * @method static Builder|Balance whereReferenceDate($value)
 * @mixin Eloquent
 */
class Balance extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $casts = [
        'reference_date' => 'date',
    ];

    public static array $balanceTypes = [
        'closingBooked' => 'closingBooked',
        'forwardAvailable' => 'forwardAvailable',
    ];

    protected $fillable = [
        'amount',
        'currency',
        'balance_type',
        'reference_date',
        'account_id',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    /**
     * Get the balance for the current month.
     *
     * @noinspection PhpUnused
     * @param $query
     * @return mixed
     */
    public function scopeCurrentMonth($query): mixed
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

    /**
     * Order by reference date.
     *
     * @noinspection PhpUnused
     * @param $query
     * @return mixed
     */
    public function scopeLastInstance($query): mixed
    {
        return $query->orderBy('reference_date', 'desc');
    }

    /**
     * Order by reference date.
     *
     * @noinspection PhpUnused
     * @param $query
     * @return mixed
     */
    public function scopeOrderDate($query): mixed
    {
        return $query->orderBy('reference_date', 'desc');
    }

    /**
     * Filter by balance type forwardAvailable.
     *
     * @noinspection PhpUnused
     * @param $query
     * @return mixed
     */
    public function scopeBalanceTypeForward($query): mixed
    {
        return $query->where('balance_type', self::$balanceTypes['forwardAvailable']);
    }

    /**
     * Filter by balance type closingBooked.
     *
     * @noinspection PhpUnused
     * @param $query
     * @return mixed
     */
    public function scopeBalanceTypeClosing($query): mixed
    {
        return $query->where('balance_type', self::$balanceTypes['closingBooked']);
    }

    /**
     * Get the balance types.
     *
     * @noinspection PhpUnused
     * @return array|string[]
     */
    public function getBalanceTypesAttribute(): array
    {
        return self::$balanceTypes;
    }

    /**
     * Get the balance code.
     *
     * @noinspection PhpUnused
     * @return mixed
     */
    public function getCodeAttribute(): mixed
    {
        return $this->attributes['id'];
    }

    public static function getExampleModel(): Balance
    {
        return new self([
            'amount' => 12.3,
            'currency' => 'EUR',
            'balance_type' => 'forwardAvailable',
            'reference_date' => now(),
        ]);
    }
}

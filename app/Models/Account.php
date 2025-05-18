<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Helpers\ColorHelper;

/**
 * @property int $id
 * @property string|null $name
 * @property string|null $iban
 * @property string|null $bban
 * @property string|null $status
 * @property string|null $owner_name
 * @property Carbon $created
 * @property Carbon $last_accessed
 * @property Carbon|null $transactions_disabled_date
 * @property Carbon|null $balance_disabled_date
 * @property int $institution_id
 * @property int $user_id
 * @property int $order
 * @property string $type
 * @property-read Collection<int, Balance> $balances
 * @property-read int|null $balances_count
 * @property-read Collection<int, BankDataSync> $bankDataSync
 * @property-read int $bank_data_sync_count
 * @property-read bool $balance_disabled
 * @property-read int $bank_data_sync_balances_count
 * @property-read int $bank_data_sync_transactions_count
 * @property-read mixed $chart_balances_labels
 * @property-read mixed $chart_balances_values
 * @property-read mixed $chart_transactions_colors
 * @property-read mixed $chart_transactions_labels
 * @property-read mixed $chart_transactions_values
 * @property-read mixed|null $code
 * @property-read int|mixed $expenses
 * @property-read int|mixed $income
 * @property-read mixed $is_api
 * @property-read mixed $is_manual
 * @property-read mixed $transactions_current_month
 * @property-read bool $transactions_disabled
 * @property-read mixed $transactions_expenses_current_month
 * @property-read mixed $transactions_income_current_month
 * @property-read Institution $institution
 * @property-read Collection<int, Transaction> $transactions
 * @property-read int|null $transactions_count
 * @property-read User $user
 * @method static Builder|Account newModelQuery()
 * @method static Builder|Account newQuery()
 * @method static Builder|Account onlyApi()
 * @method static Builder|Account onlyManual()
 * @method static Builder|Account onlyType($type)
 * @method static Builder|Account query()
 * @method static Builder|Account sortOrder()
 * @method static Builder|Account whereBalanceDisabledDate($value)
 * @method static Builder|Account whereBban($value)
 * @method static Builder|Account whereCreated($value)
 * @method static Builder|Account whereIban($value)
 * @method static Builder|Account whereId($value)
 * @method static Builder|Account whereInstitutionId($value)
 * @method static Builder|Account whereLastAccessed($value)
 * @method static Builder|Account whereName($value)
 * @method static Builder|Account whereOrder($value)
 * @method static Builder|Account whereOwnerName($value)
 * @method static Builder|Account whereStatus($value)
 * @method static Builder|Account whereTransactionsDisabledDate($value)
 * @method static Builder|Account whereType($value)
 * @method static Builder|Account whereUserId($value)
 * @mixin Eloquent
 */
class Account extends Model
{
    use HasFactory;

    public $timestamps = false;

    public static array $accountTypes = [
        'api' => 'api',
        'manual' => 'manual',
    ];

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
        'type',
        'user_id'
    ];

    /**
     * Get the account code.
     *
     * @noinspection PhpUnused
     * @return mixed
     */
    public function getCodeAttribute(): mixed
    {
        return $this->attributes['id'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'account_id', 'code');
    }

    public function balances(): HasMany
    {
        return $this->hasMany(Balance::class, 'account_id', 'code');
    }

    public function bankDataSync(): HasMany
    {
        return $this->hasMany(BankDataSync::class, 'account_id', 'code');
    }

    /**
     * Order the accounts by their order attribute.
     *
     * @noinspection PhpUnused
     */
    public function scopeSortOrder($query)
    {
        return $query->orderBy('order', 'asc');
    }

    /**
     * Generate a random color for the chart but with a color from tailwind-colors.json.
     * @param $usedColors
     * @return string
     */
    public function getUsedColors(&$usedColors): string
    {
        $colorsJson = file_get_contents(base_path('tailwind-colors.json'));
        $colorsArray = json_decode($colorsJson, true);

        $rgb = ColorHelper::hexToRgb($colorsArray['main3']);

        do {
            list($r, $g, $b) = $rgb;

            // Switch the RGB values to see wich one is the most dominant
            // and set the other two to random values between 0 and 255

            switch (true) {
                case $r > $g && $r > $b:
                    // Red
                    $g = rand(0, 255);
                    $b = rand(0, 50);
                    break;
                case $g > $r && $g > $b:
                    // Green
                    $r = rand(0, 50);
                    $b = rand(0, 255);
                    break;
                case $b > $r && $b > $g:
                    // Blue
                    $r = mt_rand(0, 50);
                    $g = mt_rand(0, 255);
                    break;
            }

            $color = sprintf("#%02X%02X%02X", $r, $g, $b);
        } while (in_array($color, $usedColors));
        $usedColors[] = $color;
        return $color;
    }

    /**
     * Scope only accounts created by the user.
     *
     * @noinspection PhpUnused
     */
    public function scopeOnlyManual($query)
    {
        return $query->where('type', Account::$accountTypes['manual']);
    }

    /**
     * Scope only accounts created by the API.
     *
     * @noinspection PhpUnused
     */
    public function scopeOnlyApi($query)
    {
        return $query->where('type', Account::$accountTypes['api']);
    }

    /**
     * Scope only accounts created by the type.
     *
     * @noinspection PhpUnused
     */
    public function scopeOnlyType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Bool is the account is created by the API.
     *
     * @noinspection PhpUnused
     */
    public function getIsApiAttribute(): bool
    {
        return $this->type == Account::$accountTypes['api'];
    }

    /**
     * Bool is the account is created by the user.
     *
     * @noinspection PhpUnused
     */
    public function getIsManualAttribute(): bool
    {
        return $this->type == Account::$accountTypes['manual'];
    }

    /**
     * Get the number of bank data syncs for today.
     *
     * @noinspection PhpUnused
     * @return int
     */
    public function getBankDataSyncCountAttribute(): int
    {
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        return $this->bankDataSync()
            ->where('created_at', '>=', $todayStart)
            ->where('created_at', '<=', $todayEnd)
            ->count();
    }

    /**
     * Get the number of bank data syncs for today for type transactions.
     *
     * @noinspection PhpUnused
     * @return int
     */
    public function getBankDataSyncTransactionsCountAttribute(): int
    {
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        return $this->bankDataSync()->dataTypeTransaction()
            ->where('created_at', '>=', $todayStart)
            ->where('created_at', '<=', $todayEnd)
            ->count();
    }

    /**
     * Get the number of bank data syncs for today for type balances.
     *
     * @noinspection PhpUnused
     * @return int
     */
    public function getBankDataSyncBalancesCountAttribute(): int
    {
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        return $this->bankDataSync()->dataTypeBalance()
            ->where('created_at', '>=', $todayStart)
            ->where('created_at', '<=', $todayEnd)
            ->count();
    }

    /**
     * Check if the transactions is disabled.
     *
     * @noinspection PhpUnused
     * @return bool
     */
    public function getTransactionsDisabledAttribute(): bool
    {
        return $this->transactions_disabled_date !== null && $this->transactions_disabled_date->isFuture();
    }

    /**
     * Check if the balance is disabled.
     *
     * @noinspection PhpUnused
     * @return bool
     */
    public function getBalanceDisabledAttribute(): bool
    {
        return $this->balance_disabled_date !== null && $this->balance_disabled_date->isFuture();
    }

    /**
     * Get the transactions for the current month.
     *
     * @noinspection PhpUnused
     * @return mixed
     */
    public function getTransactionsCurrentMonthAttribute(): mixed
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

    /**
     * Get the transactions for the current month that are expenses.
     *
     * @noinspection PhpUnused
     * @return mixed
     */
    public function getTransactionsExpensesCurrentMonthAttribute(): mixed
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

    /**
     * Get the transactions for the current month that are income.
     *
     * @noinspection PhpUnused
     * @return mixed
     */
    public function getTransactionsIncomeCurrentMonthAttribute(): mixed
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

    /**
     * Get the total expenses for the current month.
     *
     * @noinspection PhpUnused
     * @return int|mixed
     */
    public function getExpensesAttribute(): mixed
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

    /**
     * Get the total income for the current month.
     *
     * @noinspection PhpUnused
     * @return int|mixed
     */
    public function getIncomeAttribute(): mixed
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

    /**
     * Get the chart transactions values for the current month.
     *
     * @noinspection PhpUnused
     * @return mixed
     */
    public function getChartTransactionsValuesAttribute(): mixed
    {
        return $this->transactionsExpensesCurrentMonth
            ->groupBy('remittanceInformationUnstructured')
            ->map(fn($group) => $group->sum('transactionAmount_amount'))
            ->implode(',');
    }

    /**
     * Get the chart transactions labels for the current month.
     *
     * @noinspection PhpUnused
     * @return mixed
     */
    public function getChartTransactionsLabelsAttribute(): mixed
    {
        return $this->transactionsExpensesCurrentMonth
            ->groupBy('remittanceInformationUnstructured')
            ->keys()
            ->map(fn($key) => trim((string) $key, '[]"'))
            ->implode(',');
    }

    /**
     * Get the chart transactions colors for the current month.
     *
     * @noinspection PhpUnused
     * @return mixed
     */
    public function getChartTransactionsColorsAttribute(): mixed
    {
        $usedColors = [];
        return $this->transactionsExpensesCurrentMonth
            ->groupBy('remittanceInformationUnstructured')
            ->map(function ($group, $key) use (&$usedColors) {
                return $this->getUsedColors($usedColors);
            })->implode(',');
    }

    /**
     * Get the chart balances values for the current month.
     *
     * @noinspection PhpUnused
     * @return mixed
     */
    public function getChartBalancesValuesAttribute(): mixed
    {
        return $this->balances()
            ->balanceTypeForward()
            ->currentMonth()
            ->pluck('amount')
            ->implode(',');
    }

    /**
     * Get the chart balances labels for the current month.
     *
     * @noinspection PhpUnused
     * @return mixed
     */
    public function getChartBalancesLabelsAttribute(): mixed
    {
        return $this->balances()
            ->balanceTypeForward()
            ->currentMonth()
            ->pluck('reference_date')
            ->map(fn($date) => trim($date->format('d-m-Y'), '[]"'))
            ->implode(',');
    }

    /**
     * Check if the account should show the update all button.
     *
     * @noinspection PhpUnused
     * @return bool
     */
    public function getShowUpdateAllAttribute(): bool
    {
        return $this->bankDataSync()
            ->where('created_at', '>=', now()->subDays(7))
            ->where('created_at', '<=', now())
            ->count() >= ScheduledTasks::$MAX_TIMES;
    }

    /**
     * Get the example model for testing purposes.
     *
     * @noinspection PhpUnused
     * @return self
     */
    public static function getExampleModel(): Account
    {
        return new self([
            'id' => Str::uuid()->toString(),
            'name' => 'Example Account',
            'iban' => 'DE89370400440532013000',
            'bban' => '12345678901234567890',
            'status' => 'active',
            'owner_name' => 'John Doe',
            'created' => now(),
            'last_accessed' => now(),
            'institution_id' => 1
        ]);
    }
}

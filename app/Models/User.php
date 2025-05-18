<?php

namespace App\Models;

use App\Http\Controllers\NordigenController;
use Database\Factories\UserFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property mixed $password
 * @property int $schedule_times
 * @property string|null $remember_token
 * @property int $execute_login
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Account> $accounts
 * @property-read int|null $accounts_count
 * @property-read Bank|null $bank
 * @property-read mixed $bank_data_sync_count
 * @property-read Logger $logger
 * @property-read mixed $total_account_sum
 * @property-read mixed $transactions
 * @property-read mixed $balances
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, ScheduledTasks> $schedule
 * @property-read int|null $schedule_count
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static UserFactory factory($count = null, $state = [])
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereExecuteLogin($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereScheduleTimes($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @mixin Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'schedule_times',
        'execute_login'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function bank(): HasOne
    {
        return $this->hasOne(Bank::class);
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class, 'user_id');
    }

    public function schedule(): HasMany
    {
        return $this->hasMany(ScheduledTasks::class, 'user_id');
    }

    /**
     * Get the count of bank data syncs for all accounts
     *
     * @noinspection PhpUnused
     * @return int
     */
    public function getBankDataSyncCountAttribute(): int
    {
        $count = 0;
        $accounts = $this->accounts()->get();

        foreach ($accounts as $account) {
            $count += $account->bankDataSyncCount;
        }

        return $count;
    }

    /**
     * Fetch all transactions for the user ordered by booking date
     *
     * @noinspection PhpUnused
     * @return Collection|array
     */
    public function getTransactionsAttribute(): Collection|array
    {
        return Transaction::whereHas('account', function ($query) {
            $query->where('user_id', $this->id);
        })->orderDate()->get();
    }

    /**
     * Fetch all balances for the user ordered by reference date
     *
     * @noinspection PhpUnused
     * @return Collection|array
     */
    public function getBalancesAttribute(): Collection|array
    {
        return Balance::whereHas('account', function ($query) {
            $query->where('user_id', $this->id);
        })->orderDate()->get();
    }

    /**
     * Get user's logger instance.
     *
     * @noinspection PhpUnused
     * @return Logger
     */
    public function getLoggerAttribute(): Logger
    {
        $logger = new Logger("user_{$this->id}");
        $logPath = storage_path("logs/user_{$this->id}.log");
        $logger->pushHandler(new StreamHandler($logPath, Logger::INFO));

        return $logger;
    }

    /**
     * Calculate the total sum of all accounts for the user on the relationship with balances on each account
     *
     * @noinspection PhpUnused
     * @return mixed
     */
    public function getTotalAccountSumAttribute(): mixed
    {
        return $this->accounts->sum(function ($account) {
            $latestBalance = $account->balances()
                ->balanceTypeForward()
                ->orderByDesc('reference_date')
                ->first();

            return $latestBalance?->amount ?? 0;
        });
    }

    /**
     * Execute account tasks for the user
     * @return void
     */
    public function executeAccountTasks(): void
    {
        $nordigen = new NordigenController();

        foreach ($this->accounts as $account) {
            $nordigen->update(new Request(), $account->code);
        }
    }
}

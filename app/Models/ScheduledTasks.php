<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $hour
 * @property int $user_id
 * @property-read string $hour_simple
 * @property-read User $user
 * @method static Builder|ScheduledTasks newModelQuery()
 * @method static Builder|ScheduledTasks newQuery()
 * @method static Builder|ScheduledTasks query()
 * @method static Builder|ScheduledTasks whereHour($value)
 * @method static Builder|ScheduledTasks whereId($value)
 * @method static Builder|ScheduledTasks whereUserId($value)
 * @mixin Eloquent
 */
class ScheduledTasks extends Model
{
    use HasFactory;

    public $timestamps = false;

    public static int $MAX_TIMES = 3;

    public static int $WARNING_TIMES = 2;

    protected $fillable = [
        'hour',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the hour in a simple format (HH:MM).
     *
     * @noinspection PhpUnused
     * @return string
     */
    public function getHourSimpleAttribute(): string
    {
        return date('H:i', strtotime($this->hour));
    }
}

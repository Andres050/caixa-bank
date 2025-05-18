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
 * @property int $user_id
 * @property int $institution_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Institution $institution
 * @property-read User $user
 * @method static Builder|Bank newModelQuery()
 * @method static Builder|Bank newQuery()
 * @method static Builder|Bank query()
 * @method static Builder|Bank whereCreatedAt($value)
 * @method static Builder|Bank whereId($value)
 * @method static Builder|Bank whereInstitutionId($value)
 * @method static Builder|Bank whereUpdatedAt($value)
 * @method static Builder|Bank whereUserId($value)
 * @mixin Eloquent
 */
class Bank extends Model
{
    use HasFactory;

    protected $table = 'banks';

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

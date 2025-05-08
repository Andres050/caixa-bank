<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledTasks extends Model
{
    use HasFactory;

    public $timestamps = false;

    public static $MAX_TIMES = 3;

    protected $fillable = [
        'hour',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getHourSimpleAttribute()
    {
        return date('H:i', strtotime($this->hour));
    }
}

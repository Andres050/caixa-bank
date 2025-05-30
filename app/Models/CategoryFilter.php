<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryFilter extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'category_id',
        'value',
        'field',
        'type',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function scopeIsEnabled($query)
    {
        return $query->where('enabled', true);
    }
}

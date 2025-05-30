<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'categories';

    protected $fillable = [
        'name',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'category_id');
    }

    public function filters(): HasMany
    {
        return $this->hasMany(CategoryFilter::class, 'category_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

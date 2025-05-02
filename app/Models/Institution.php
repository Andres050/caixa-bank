<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    use HasFactory;

    protected $table = 'institutions';

    public $timestamps = false;

    protected $fillable = [
        'code',
        'name',
        'bic',
        'transaction_total_days',
        'country',
        'logo',
        'max_access_valid_for_days',
    ];

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function bank()
    {
        return $this->hasMany(Bank::class);
    }
}

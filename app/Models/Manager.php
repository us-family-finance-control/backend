<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Manager extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone'
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function dependents(): HasMany
    {
        return $this->hasMany(Dependent::class);
    }
}

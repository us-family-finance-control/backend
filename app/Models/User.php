<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const TYPE_ADMIN = 'admin';
    const TYPE_MANAGER = 'manager';
    const TYPE_DEPENDENT = 'dependent';

    protected $fillable = [
        'admin_id', 'manager_id', 'dependent_id', 'name', 'username', 'email', 'email_verified_at', 'password',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Manager::class);
    }

    public function dependent(): BelongsTo
    {
        return $this->belongsTo(Dependent::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function getType()
    {
        if (!is_null($this->admin)) {
            return self::TYPE_ADMIN;
        }

        if (!is_null($this->manager)) {
            return self::TYPE_MANAGER;
        }

        if (!is_null($this->dependent)) {
            return self::TYPE_DEPENDENT;
        }
    }

    public static function getTypes()
    {
        return [
            self::TYPE_ADMIN,
            self::TYPE_MANAGER,
            self::TYPE_DEPENDENT,
        ];
    }
}

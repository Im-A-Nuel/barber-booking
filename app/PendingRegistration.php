<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PendingRegistration extends Model
{
    protected $fillable = [
        'name',
        'username',
        'email',
        'token',
        'is_verified',
        'expires_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * Check if token is expired
     */
    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    /**
     * Generate a random 6-digit token
     */
    public static function generateToken()
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}

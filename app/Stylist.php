<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stylist extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'specialty',
        'bio',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the stylist profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the schedules for the stylist.
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}

<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'email', 'image', 'password', 'role',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * cek jika user adalah admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * cek jika user adalah stylist
     *
     * @return bool
     */
    public function isStylist()
    {
        return $this->role === 'stylist';
    }

    /**
     * cek jika user adalah customer
     *
     * @return bool
     */
    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    /**
     * Get the stylist profile associated with the user.
     */
    public function stylist()
    {
        return $this->hasOne(Stylist::class);
    }

    /**
     * Get the bookings for the user (as customer).
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }
}

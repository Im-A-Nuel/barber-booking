<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'booking_id',
        'amount',
        'method',
        'status',
        'paid_at',
    ];

    protected $dates = [
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    /**
     * Get the booking that owns the payment.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}

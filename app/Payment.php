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
        'payment_type',
        'gateway_name',
        'transaction_id',
        'payment_url',
        'gateway_response',
        'crypto_currency',
        'crypto_amount',
        'crypto_address',
        'crypto_tx_hash',
        'expires_at',
    ];

    protected $dates = [
        'paid_at',
        'expires_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'crypto_amount' => 'decimal:8',
    ];

    /**
     * Get the booking that owns the payment.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}

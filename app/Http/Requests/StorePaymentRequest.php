<?php

namespace App\Http\Requests;

use App\Booking;
use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'amount' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    $booking = Booking::find($this->route('booking'));
                    if ($booking && $value != $booking->service->price) {
                        $fail('Jumlah pembayaran harus sesuai dengan harga layanan (Rp ' . number_format($booking->service->price, 0, ',', '.') . ')');
                    }
                }
            ],
            'method' => 'required|string|in:cash,transfer,e-wallet,debit_card,credit_card',
            'status' => 'required|string|in:pending,paid,failed',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'amount' => 'jumlah pembayaran',
            'method' => 'metode pembayaran',
            'status' => 'status pembayaran',
        ];
    }
}

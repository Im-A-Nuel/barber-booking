<?php

namespace App\Http\Requests\Booking;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Only authenticated customers can create bookings
        return auth()->check() && auth()->user()->isCustomer();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'service_id' => [
                'required',
                'exists:services,id',
                function ($attribute, $value, $fail) {
                    $service = \App\Service::find($value);
                    if ($service && !$service->is_active) {
                        $fail('Service yang dipilih tidak tersedia.');
                    }
                },
            ],
            'stylist_id' => [
                'required',
                'exists:stylists,id',
                function ($attribute, $value, $fail) {
                    $stylist = \App\Stylist::find($value);
                    if ($stylist && !$stylist->is_active) {
                        $fail('Stylist yang dipilih tidak tersedia.');
                    }
                },
            ],
            'booking_date' => [
                'required',
                'date',
                'after_or_equal:today',
                function ($attribute, $value, $fail) {
                    if (!$this->stylist_id) {
                        return;
                    }

                    $date = Carbon::parse($value);
                    $dayOfWeek = $date->dayOfWeek;

                    // Check if stylist has schedule for this day
                    $schedule = \App\Schedule::where('stylist_id', $this->stylist_id)
                        ->where('day_of_week', $dayOfWeek)
                        ->where('is_active', true)
                        ->first();

                    if (!$schedule) {
                        $fail('Stylist tidak tersedia pada tanggal yang dipilih.');
                    }
                },
            ],
            'start_time' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    if (!$this->booking_date || !$this->stylist_id) {
                        return;
                    }

                    $date = Carbon::parse($this->booking_date);
                    $dayOfWeek = $date->dayOfWeek;
                    $startTime = $value . ':00';

                    // Get stylist schedule
                    $schedule = \App\Schedule::where('stylist_id', $this->stylist_id)
                        ->where('day_of_week', $dayOfWeek)
                        ->where('is_active', true)
                        ->first();

                    if ($schedule) {
                        // Check if start_time is within schedule
                        if ($startTime < $schedule->start_time || $startTime >= $schedule->end_time) {
                            $fail('Waktu mulai harus dalam jam kerja stylist.');
                        }
                    }

                    // If booking for today, check if time has passed
                    if ($date->isToday()) {
                        $now = Carbon::now();
                        $bookingTime = Carbon::parse($this->booking_date . ' ' . $value);

                        if ($bookingTime->lessThanOrEqualTo($now->addHour())) {
                            $fail('Booking harus minimal 1 jam dari sekarang.');
                        }
                    }
                },
            ],
            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
                function ($attribute, $value, $fail) {
                    if (!$this->booking_date || !$this->stylist_id || !$this->start_time) {
                        return;
                    }

                    $startTime = $this->start_time . ':00';
                    $endTime = $value . ':00';

                    // Check for overlapping bookings
                    $hasConflict = \App\Booking::where('stylist_id', $this->stylist_id)
                        ->whereDate('booking_date', $this->booking_date)
                        ->whereIn('status', [\App\Booking::STATUS_PENDING, \App\Booking::STATUS_CONFIRMED])
                        ->where(function ($query) use ($startTime, $endTime) {
                            $query->where(function ($q) use ($startTime, $endTime) {
                                // New booking overlaps with existing
                                $q->where('start_time', '<', $endTime)
                                  ->where('end_time', '>', $startTime);
                            });
                        })
                        ->exists();

                    if ($hasConflict) {
                        $fail('Waktu yang dipilih sudah dibooking. Silakan pilih waktu lain.');
                    }
                },
            ],
            'notes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom error messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'booking_date.after_or_equal' => 'Tanggal booking tidak boleh di masa lalu.',
            'end_time.after' => 'Waktu selesai harus lebih besar dari waktu mulai.',
        ];
    }
}

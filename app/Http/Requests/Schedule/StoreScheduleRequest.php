<?php

namespace App\Http\Requests\Schedule;

use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $scheduleId = $this->route('schedule') ? $this->route('schedule')->id : null;

        return [
            'stylist_id' => [
                'required',
                'exists:stylists,id',
            ],
            'day_of_week' => [
                'required',
                'integer',
                'between:0,6',
                // Unique combination of stylist_id and day_of_week
                function ($attribute, $value, $fail) use ($scheduleId) {
                    $query = \App\Schedule::where('stylist_id', $this->stylist_id)
                        ->where('day_of_week', $value);

                    if ($scheduleId) {
                        $query->where('id', '!=', $scheduleId);
                    }

                    if ($query->exists()) {
                        $fail('Jadwal untuk hari ini sudah ada untuk stylist yang dipilih.');
                    }
                },
            ],
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_active' => 'required|boolean',
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
            'end_time.after' => 'Waktu selesai harus lebih besar dari waktu mulai.',
        ];
    }
}

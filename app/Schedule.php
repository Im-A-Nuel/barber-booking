<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    // Day constants
    const SUNDAY = 0;
    const MONDAY = 1;
    const TUESDAY = 2;
    const WEDNESDAY = 3;
    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'stylist_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'day_of_week' => 'integer',
    ];

    /**
     * Get the stylist that owns the schedule.
     */
    public function stylist()
    {
        return $this->belongsTo(Stylist::class);
    }

    /**
     * Get day name in Indonesian.
     *
     * @return string
     */
    public function getDayNameAttribute()
    {
        $days = [
            self::SUNDAY => 'Minggu',
            self::MONDAY => 'Senin',
            self::TUESDAY => 'Selasa',
            self::WEDNESDAY => 'Rabu',
            self::THURSDAY => 'Kamis',
            self::FRIDAY => 'Jumat',
            self::SATURDAY => 'Sabtu',
        ];

        return $days[$this->day_of_week] ?? '-';
    }

    /**
     * Get all day options.
     *
     * @return array
     */
    public static function getDayOptions()
    {
        return [
            self::MONDAY => 'Senin',
            self::TUESDAY => 'Selasa',
            self::WEDNESDAY => 'Rabu',
            self::THURSDAY => 'Kamis',
            self::FRIDAY => 'Jumat',
            self::SATURDAY => 'Sabtu',
            self::SUNDAY => 'Minggu',
        ];
    }
}

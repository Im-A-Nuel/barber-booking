<?php

use App\Stylist;
use App\Schedule;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get the first stylist (should exist from StylistSeeder)
        $stylist = Stylist::first();

        if ($stylist) {
            // Create schedules for Monday to Friday (9:00 - 17:00)
            $weekdaySchedules = [
                Schedule::MONDAY,
                Schedule::TUESDAY,
                Schedule::WEDNESDAY,
                Schedule::THURSDAY,
                Schedule::FRIDAY,
            ];

            foreach ($weekdaySchedules as $day) {
                Schedule::create([
                    'stylist_id' => $stylist->id,
                    'day_of_week' => $day,
                    'start_time' => '09:00',
                    'end_time' => '17:00',
                    'is_active' => true,
                ]);
            }

            // Saturday with shorter hours (9:00 - 14:00)
            Schedule::create([
                'stylist_id' => $stylist->id,
                'day_of_week' => Schedule::SATURDAY,
                'start_time' => '09:00',
                'end_time' => '14:00',
                'is_active' => true,
            ]);
        }
    }
}

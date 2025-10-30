<?php

use Illuminate\Database\Seeder;
use Fake\Factory as Faker;

class BarbersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('barbers')->insert([
            'name' => 'Andi Barbers',
            'specialty' => 'Classic Cut & Beard Trim',
            'experience_years' => 5,
            'phone' => '081234567890',
            'rating_avg' => 4.50,
            'is_active' => true,
        ]);
    }
}

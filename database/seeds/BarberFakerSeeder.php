<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class BarberFakerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $specialties = [
            'Classic Cut', 'Fade', 'Pompadour', 'Undercut',
            'Buzz Cut', 'Scissor Cut', 'Hot Towel Shave', 'Beard Trim', 'Kids Cut'
        ];

        for ($i = 1; $i <= 50; $i++) {
            DB::table('barbers')->insert([
                'name'             => $faker->name,
                'specialty'        => $faker->randomElement($specialties),
                'experience_years' => $faker->numberBetween(0, 25),
                'phone'            => '08' . $faker->numberBetween(1000000000, 9999999999),
                'rating_avg'       => $faker->randomFloat(2, 3.50, 5.00),
                'is_active'        => $faker->boolean(90)
            ]);
        }
    }
}

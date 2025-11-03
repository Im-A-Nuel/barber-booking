<?php

use App\User;
use App\Stylist;
use Illuminate\Database\Seeder;

class StylistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create stylist profile for the existing stylist user
        $stylistUser = User::where('email', 'stylist@barber.test')->first();

        if ($stylistUser) {
            Stylist::create([
                'user_id' => $stylistUser->id,
                'specialty' => 'Haircut & Beard Trim',
                'bio' => 'Professional barber with 5+ years of experience in modern and classic hairstyles.',
                'is_active' => true,
            ]);
        }
    }
}

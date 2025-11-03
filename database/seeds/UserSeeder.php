<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Admin user
        User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@barber.test',
            'role' => 'admin',
            'password' => Hash::make('admin'),
        ]);

        // Stylist user
        User::create([
            'name' => 'John Stylist',
            'username' => 'johnstylist',
            'email' => 'stylist@barber.test',
            'role' => 'stylist',
            'password' => Hash::make('password'),
        ]);

        // Customer user
        User::create([
            'name' => 'Jane Customer',
            'username' => 'janecustomer',
            'email' => 'customer@barber.test',
            'role' => 'customer',
            'password' => Hash::make('password'),
        ]);
    }
}

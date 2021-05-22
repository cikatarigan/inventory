<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::firstOrCreate([
            'name' => 'admin',
            'username' => 'admin',
            'email' => 'admin@pkt.com',
            'password' => Hash::make('password'),
            'status' => 'active',
            'phone' => '082120006888'
        ]);
        
        $user->assignRole('admin');
    }
}

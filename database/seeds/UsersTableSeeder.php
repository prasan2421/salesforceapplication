<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Karan Bajracharya',
            'username' => 'karan',
            'email' => 'karan@biztechnepal.com',
        	'password' => bcrypt('admin'),
            'role' => 'admin'
        ]);
    }
}

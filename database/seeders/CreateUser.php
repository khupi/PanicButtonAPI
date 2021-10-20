<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class CreateUser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Commissioner Gordon',
            'email' => 'gordon@gothampd.com',
            //'email_verified_at' => now(),
            'password' => bcrypt('123456'),
            //'remember_token' => Str::random(10),
       ]);

       User::create([
        'name' => 'Khupi',
        'email' => 'makhup7070@gmail',
        'password' => bcrypt('123456'),
    ]);
       
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password'=> Hash::make('123456')
        ]);

        User::create([
            'name' => 'Ahmed',
            'email' => 'ahmed@admin.com',
            'password'=> Hash::make('123456')
        ]);


        User::create([
            'name' => 'Ali',
            'email' => 'ali@admin.com',
            'password'=> Hash::make('123456')
        ]);
    }
}

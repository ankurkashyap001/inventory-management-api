<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['phone' => '6396956896'],
            [
                'name'     => 'AnkurKart Admin',
                'email'    => 'admin@ankurkart.com',
                'password' => Hash::make('password123'),
                'role'     => 'admin',
                'is_admin' => true,
            ]
        );
    }
}
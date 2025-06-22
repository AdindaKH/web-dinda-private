<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Untuk Pimpinan User
        User::firstOrCreate(
            ['email' => 'pimpinan@example.com'], // Kondisi untuk mencari data
            [
                'name' => 'Pimpinan User',
                'password' => Hash::make('PassAdmin123'),
                'role' => 'pimpinan',
            ]
        );

        // Untuk Operator User
        User::firstOrCreate(
            ['email' => 'operator@example.com'], // Kondisi untuk mencari data
            [
                'name' => 'Operator User',
                'password' => Hash::make('PassOperator123'),
                'role' => 'operator',
            ]
        );
    }
}

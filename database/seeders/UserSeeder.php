<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@spk.test'], [
            'name' => 'Administrator',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        User::updateOrCreate(['email' => 'hrd@spk.test'], [
            'name' => 'Staff HRD',
            'password' => Hash::make('password123'),
            'role' => 'hrd',
        ]);

        User::updateOrCreate(['email' => 'manajer@spk.test'], [
            'name' => 'Manajer SDM',
            'password' => Hash::make('password123'),
            'role' => 'manajer_sdm',
        ]);
    }
}
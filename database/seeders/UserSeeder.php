<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'               => 'Admin FishMarket',
            'email'              => 'admin@fishmarket.id',
            'password'           => Hash::make('password123'),
            'role'               => 'admin',
            'no_hp'              => '081234567890',
            'alamat'             => 'Jl. Ikan Segar No. 1, Jakarta',
            'email_verified_at'  => now(),
        ]);

        User::create([
            'name'               => 'Budi Pembeli',
            'email'              => 'budi@gmail.com',
            'password'           => Hash::make('password123'),
            'role'               => 'customer',
            'no_hp'              => '089876543210',
            'alamat'             => 'Jl. Pelanggan Setia No. 10, Bandung',
            'email_verified_at'  => now(),
        ]);

        User::create([
            'name'               => 'Siti Customer',
            'email'              => 'siti@gmail.com',
            'password'           => Hash::make('password123'),
            'role'               => 'customer',
            'no_hp'              => '081122334455',
            'alamat'             => 'Jl. Raya Surabaya No. 5',
            'email_verified_at'  => now(),
        ]);
    }
}
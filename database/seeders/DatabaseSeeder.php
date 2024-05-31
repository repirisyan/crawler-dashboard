<?php

namespace Database\Seeders;

use App\Models\Marketplace;
use App\Models\Province;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Province::create([
            'name' => 'Jawa Barat'
        ]);
        Province::create([
            'name' => 'Jawa Tengah'
        ]);
        Marketplace::create([
            'name' => 'Bukalapak'
        ]);
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);
    }
}

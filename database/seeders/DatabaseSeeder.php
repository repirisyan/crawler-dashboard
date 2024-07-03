<?php

namespace Database\Seeders;

use App\Models\Marketplace;
use App\Models\Notification;
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
        Marketplace::create([
            'name' => 'Bukalapak',
            'logo' => 'bukalapak.svg',
            'maintenance' => false,
        ]);
        Marketplace::create([
            'name' => 'Shopee',
            'logo' => 'shopee.webp',
            'maintenance' => true,
        ]);
        Marketplace::create([
            'name' => 'Lazada',
            'logo' => 'lazada.png',
            'maintenance' => false,
        ]);
        Marketplace::create([
            'name' => 'Tokopedia',
            'logo' => 'tokopedia.svg',
            'maintenance' => false,
        ]);
        Marketplace::create([
            'name' => 'Blibli',
            'logo' => 'blibli.svg',
            'maintenance' => false,
        ]);
        Marketplace::create([
            'name' => 'OLX',
            'logo' => 'olx.svg',
            'maintenance' => false,
        ]);
        Marketplace::create([
            'name' => 'Amazon',
            'logo' => 'amazon.svg',
            'maintenance' => false,
        ]);
        $user_id = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ])->id;

        Notification::create([
            'message' => 'Started Crawler Using Bukalapak Engine',
            'status' => false,
            'user_id' => $user_id,
        ]);
    }
}

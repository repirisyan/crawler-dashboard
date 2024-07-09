<?php

namespace Database\Seeders;

use App\Models\Marketplace;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MarketplaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
    }
}

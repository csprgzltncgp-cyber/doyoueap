<?php

namespace Database\Seeders;

use App\Models\AssetType;
use Illuminate\Database\Seeder;

class InventoryTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AssetType::query()->create([
            'id' => 1,
            'name' => 'Laptop',
        ]);

        AssetType::query()->create([
            'id' => 2,
            'name' => 'Billentyűzet',
        ]);

        AssetType::query()->create([
            'id' => 3,
            'name' => 'Telefon',
        ]);

        AssetType::query()->create([
            'id' => 4,
            'name' => 'Monitor',
        ]);
    }
}

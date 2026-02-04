<?php

namespace Database\Seeders;

use App\Models\AssetOwner;
use Illuminate\Database\Seeder;

class InventoryOwnerStorageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AssetOwner::query()->create([
            'id' => 900,
            'name' => 'Raktár',
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Imports\CompaniesImport;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class CompaniesImporterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/WPO - Hungary.xlsx');
        Excel::import(new CompaniesImport, $path);
    }
}

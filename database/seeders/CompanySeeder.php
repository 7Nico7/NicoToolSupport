<?php
// database/seeders/CompanySeeder.php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'name'      => 'Acme Corp',
                'slug'      => 'acme-corp',
                'is_active' => true,
            ],
            [
                'name'      => 'Beta Solutions',
                'slug'      => 'beta-solutions',
                'is_active' => true,
            ],
        ];

        foreach ($companies as $company) {
            Company::firstOrCreate(['slug' => $company['slug']], $company);
        }
    }
}

<?php
// database/seeders/TicketCategorySeeder.php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\TicketCategory;
use Illuminate\Database\Seeder;

class TicketCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categoriesByCompany = [
            'acme-corp' => [
                'Hardware',
                'Software',
                'Red y Conectividad',
                'Accesos y Permisos',
                'Correo Electrónico',
                'Impresoras',
                'Servidores',
                'Facturación',
            ],
            'beta-solutions' => [
                'Desarrollo',
                'Infraestructura',
                'Base de Datos',
                'Seguridad',
                'Soporte al Cliente',
            ],
        ];

        foreach ($categoriesByCompany as $slug => $categories) {
            $company = Company::where('slug', $slug)->first();
            if (!$company) continue;

            foreach ($categories as $name) {
                TicketCategory::firstOrCreate([
                    'company_id' => $company->id,
                    'name'       => $name,
                ]);
            }
        }
    }
}

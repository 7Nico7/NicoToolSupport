<?php
// database/seeders/ProjectSeeder.php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projectsByCompany = [
            'acme-corp' => [
                ['name' => 'Portal Corporativo',    'description' => 'Sitio web y portal de empleados', 'is_active' => true],
                ['name' => 'ERP Interno',            'description' => 'Sistema de gestión empresarial', 'is_active' => true],
                ['name' => 'App Móvil',              'description' => 'Aplicación para clientes móviles', 'is_active' => true],
                ['name' => 'Migración Cloud',        'description' => 'Migración de servidores on-premise a AWS', 'is_active' => false],
            ],
            'beta-solutions' => [
                ['name' => 'Plataforma SaaS',        'description' => 'Producto principal de la empresa', 'is_active' => true],
                ['name' => 'API Gateway',             'description' => 'Gateway de integración con clientes', 'is_active' => true],
            ],
        ];

        foreach ($projectsByCompany as $slug => $projects) {
            $company = Company::where('slug', $slug)->first();
            if (!$company) continue;

            foreach ($projects as $project) {
                Project::firstOrCreate(
                    ['company_id' => $company->id, 'name' => $project['name']],
                    [...$project, 'company_id' => $company->id]
                );
            }
        }
    }
}

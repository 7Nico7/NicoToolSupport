<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1. Catálogos globales (sin dependencias)
            TicketStatusSeeder::class,
            TicketPrioritySeeder::class,
            TicketTypeSeeder::class,

            // 2. Compañías y estructura multi-tenant
            CompanySeeder::class,

            // 3. Usuarios (dependen de company)
            UserSeeder::class,

            // 4. Catálogos por compañía (dependen de company)
            TicketCategorySeeder::class,
            ProjectSeeder::class,
            HelpdeskSeeder::class,

            // 5. Tickets y actividad (dependen de todo lo anterior)
            TicketSeeder::class,
        ]);
    }
}

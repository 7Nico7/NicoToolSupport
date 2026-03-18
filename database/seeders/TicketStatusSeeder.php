<?php
// database/seeders/TicketStatusSeeder.php

namespace Database\Seeders;

use App\Models\TicketStatus;
use Illuminate\Database\Seeder;

class TicketStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'Nuevo',       'color' => '#6366f1', 'order' => 1],
            ['name' => 'Abierto',     'color' => '#3b82f6', 'order' => 2],
            ['name' => 'En Progreso', 'color' => '#f59e0b', 'order' => 3],
            ['name' => 'En Espera',   'color' => '#8b5cf6', 'order' => 4],
            ['name' => 'Resuelto',    'color' => '#10b981', 'order' => 5],
            ['name' => 'Cerrado',     'color' => '#64748b', 'order' => 6],
        ];

        foreach ($statuses as $status) {
            TicketStatus::firstOrCreate(['name' => $status['name']], $status);
        }
    }
}

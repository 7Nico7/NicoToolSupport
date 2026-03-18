<?php
// database/seeders/TicketPrioritySeeder.php

namespace Database\Seeders;

use App\Models\TicketPriority;
use Illuminate\Database\Seeder;

class TicketPrioritySeeder extends Seeder
{
    public function run(): void
    {
        // level: 1 = más baja, 4 = crítica
        $priorities = [
            ['name' => 'Baja',    'level' => 1, 'color' => '#10b981'],
            ['name' => 'Media',   'level' => 2, 'color' => '#3b82f6'],
            ['name' => 'Alta',    'level' => 3, 'color' => '#f59e0b'],
            ['name' => 'Crítica', 'level' => 4, 'color' => '#ef4444'],
        ];

        foreach ($priorities as $priority) {
            TicketPriority::firstOrCreate(['name' => $priority['name']], $priority);
        }
    }
}

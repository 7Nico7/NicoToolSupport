<?php
// database/seeders/TicketTypeSeeder.php

namespace Database\Seeders;

use App\Models\TicketType;
use Illuminate\Database\Seeder;

class TicketTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Incidente',
            'Solicitud de Servicio',
            'Problema',
            'Cambio',
            'Consulta',
        ];

        foreach ($types as $name) {
            TicketType::firstOrCreate(['name' => $name]);
        }
    }
}

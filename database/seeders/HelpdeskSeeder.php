<?php
// database/seeders/HelpdeskSeeder.php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Helpdesk;
use App\Models\User;
use Illuminate\Database\Seeder;

class HelpdeskSeeder extends Seeder
{
    public function run(): void
    {
        // ── Acme Corp ────────────────────────────────────────────────────────
        $acme = Company::where('slug', 'acme-corp')->firstOrFail();

        $acmeAgents = User::where('company_id', $acme->id)
            ->whereIn('role', ['agent', 'admin'])
            ->pluck('id');

        $helpdesksTI = Helpdesk::firstOrCreate(
            ['company_id' => $acme->id, 'name' => 'Soporte TI'],
        );
        $helpdesksTI->users()->syncWithoutDetaching($acmeAgents);

        $helpdeskFacturacion = Helpdesk::firstOrCreate(
            ['company_id' => $acme->id, 'name' => 'Soporte Facturación'],
        );
        // Solo los primeros dos agentes atienden facturación
        $helpdeskFacturacion->users()->syncWithoutDetaching($acmeAgents->take(2));

        // ── Beta Solutions ───────────────────────────────────────────────────
        $beta = Company::where('slug', 'beta-solutions')->firstOrFail();

        $betaAgents = User::where('company_id', $beta->id)
            ->whereIn('role', ['agent', 'admin'])
            ->pluck('id');

        $helpdeskBeta = Helpdesk::firstOrCreate(
            ['company_id' => $beta->id, 'name' => 'Helpdesk General'],
        );
        $helpdeskBeta->users()->syncWithoutDetaching($betaAgents);
    }
}

<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Credenciales de acceso generadas:
     *
     *  ROL     | EMAIL                          | PASSWORD
     * ---------|--------------------------------|----------
     *  admin   | admin@acme.com                 | password
     *  agent   | agent1@acme.com                | password
     *  agent   | agent2@acme.com                | password
     *  agent   | agent3@acme.com                | password
     *  client  | client1@acme.com               | password
     *  client  | client2@acme.com               | password
     *  client  | client3@acme.com               | password
     *  admin   | admin@beta.com                 | password
     *  agent   | agent1@beta.com                | password
     *  client  | client1@beta.com               | password
     */
    public function run(): void
    {
        $acme = Company::where('slug', 'acme-corp')->firstOrFail();
        $beta = Company::where('slug', 'beta-solutions')->firstOrFail();

        // ── Acme Corp ────────────────────────────────────────────────────────
        $this->createUser($acme, 'admin', 'Admin Acme',     'admin@gmail.com');

        $this->createUser($acme, 'agent', 'Carlos Mendoza', 'agent1@acme.com');
        $this->createUser($acme, 'agent', 'Laura Pérez',    'agent2@acme.com');
        $this->createUser($acme, 'agent', 'Marcos Torres',  'agent3@acme.com');

        $this->createUser($acme, 'client', 'Sandra López',  'client1@acme.com');
        $this->createUser($acme, 'client', 'Raúl Jiménez',  'client2@acme.com');
        $this->createUser($acme, 'client', 'Diana Reyes',   'client3@acme.com');

        // ── Beta Solutions ────────────────────────────────────────────────────
        $this->createUser($beta, 'admin', 'Admin Beta',     'admin@beta.com');
        $this->createUser($beta, 'agent', 'Sofía Ramírez',  'agent1@beta.com');
        $this->createUser($beta, 'client', 'Luis Herrera',  'client1@beta.com');
    }

    private function createUser(Company $company, string $role, string $name, string $email): User
    {
        return User::firstOrCreate(
            ['email' => $email],
            [
                'company_id' => $company->id,
                'name'       => $name,
                'password'   => Hash::make('password'),
                'role'       => $role,
                'is_active'  => true,
            ]
        );
    }
}

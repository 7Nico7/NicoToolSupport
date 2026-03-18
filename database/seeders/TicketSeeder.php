<?php
// database/seeders/TicketSeeder.php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Helpdesk;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\TicketCategory;
use App\Models\TicketMessage;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TicketSeeder extends Seeder
{
    // Contadores de ticket_number por company
    private array $ticketCounters = [];

    public function run(): void
    {
        $this->seedCompany('acme-corp');
        $this->seedCompany('beta-solutions');
    }

    // ─── Orquestador por compañía ─────────────────────────────────────────────

    private function seedCompany(string $slug): void
    {
        $company  = Company::where('slug', $slug)->firstOrFail();
        $context  = $this->buildContext($company);

        $this->ticketCounters[$company->id] = 0;

        // Tickets distribuidos en todos los estados para poblar el Kanban
        $this->createTicketsForStatus($context, 'Nuevo',       5);
        $this->createTicketsForStatus($context, 'Abierto',     6);
        $this->createTicketsForStatus($context, 'En Progreso', 5);
        $this->createTicketsForStatus($context, 'En Espera',   3);
        $this->createTicketsForStatus($context, 'Resuelto',    4);
        $this->createTicketsForStatus($context, 'Cerrado',     3);
    }

    // ─── Construcción del contexto por compañía ───────────────────────────────

    private function buildContext(Company $company): array
    {
        return [
            'company'    => $company,
            'statuses'   => TicketStatus::orderBy('order')->get()->keyBy('name'),
            'priorities' => TicketPriority::all()->keyBy('name'),
            'types'      => TicketType::all()->keyBy('name'),
            'categories' => TicketCategory::where('company_id', $company->id)->get(),
            'projects'   => Project::where('company_id', $company->id)->get(),
            'helpdesks'  => Helpdesk::where('company_id', $company->id)->get(),
            'admin'      => User::where('company_id', $company->id)->where('role', 'admin')->first(),
            'agents'     => User::where('company_id', $company->id)->where('role', 'agent')->get(),
            'clients'    => User::where('company_id', $company->id)->where('role', 'client')->get(),
        ];
    }

    // ─── Creación de tickets por estado ──────────────────────────────────────

    private function createTicketsForStatus(array $ctx, string $statusName, int $count): void
    {
        $status = $ctx['statuses'][$statusName] ?? null;
        if (!$status) return;

        $templates = $this->ticketTemplates();

        for ($i = 0; $i < $count; $i++) {
            $template = $templates[array_rand($templates)];
            $client   = $ctx['clients']->random();
            $agent    = $ctx['agents']->isNotEmpty() ? $ctx['agents']->random() : $ctx['admin'];
            $priority = $ctx['priorities']->random();
            $type     = $ctx['types']->random();
            $category = $ctx['categories']->isNotEmpty() ? $ctx['categories']->random() : null;
          $project = $ctx['projects']->isNotEmpty()
    ? $ctx['projects']->random()
    : null;
            $helpdesk = $ctx['helpdesks']->isNotEmpty() ? $ctx['helpdesks']->random() : null;

            $isClosedState = in_array($statusName, ['Resuelto', 'Cerrado']);
            $createdAt     = Carbon::now()->subDays(rand(1, 60));
            $dueDate       = !$isClosedState && rand(0, 1)
                ? Carbon::now()->addDays(rand(-3, 14)) // Algunos vencidos, algunos futuros
                : null;

            $ticket = Ticket::create([
                'company_id'    => $ctx['company']->id,
                'project_id'    => $project?->id,
                'helpdesk_id'   => $helpdesk?->id,
                'ticket_number' => $this->nextTicketNumber($ctx['company']->id),
                'title'         => $template['title'],
                'description'   => $template['description'],
                'status_id'     => $status->id,
                'priority_id'   => $priority->id,
                'type_id'       => $type->id,
                'category_id'   => $category?->id,
                'created_by'    => $client->id,
                'assigned_to'   => in_array($statusName, ['Nuevo']) ? null : $agent->id,
                'due_date'      => $dueDate,
                'closed_at'     => $isClosedState ? $createdAt->copy()->addDays(rand(1, 7)) : null,
                'created_at'    => $createdAt,
                'updated_at'    => $createdAt,
            ]);

            $this->seedActivity($ticket, $client, $agent, $statusName, $createdAt);
            $this->seedMessages($ticket, $client, $agent, $statusName, $createdAt);
        }
    }

    // ─── Actividad del ticket ─────────────────────────────────────────────────

    private function seedActivity(Ticket $ticket, User $client, User $agent, string $statusName, Carbon $base): void
    {
        // Creación
        TicketActivity::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => $client->id,
            'action'      => 'created',
            'description' => 'Ticket creado',
            'created_at'  => $base,
            'updated_at'  => $base,
        ]);

        // Asignación (si ya está asignado)
        if ($ticket->assigned_to) {
            $assignedAt = $base->copy()->addMinutes(rand(5, 60));
            TicketActivity::create([
                'ticket_id'   => $ticket->id,
                'user_id'     => $agent->id,
                'action'      => 'assigned',
                'description' => "Asignado a: {$agent->name}",
                'created_at'  => $assignedAt,
                'updated_at'  => $assignedAt,
            ]);
        }

        // Movimientos entre estados según el estado final
        $transitions = $this->statusTransitions($statusName);
        $cursor      = $base->copy()->addHours(rand(1, 3));

        foreach ($transitions as $transition) {
            TicketActivity::create([
                'ticket_id'   => $ticket->id,
                'user_id'     => $agent->id,
                'action'      => 'moved',
                'description' => $transition,
                'created_at'  => $cursor,
                'updated_at'  => $cursor,
            ]);
            $cursor->addHours(rand(1, 24));
        }

        // Cierre
        if (in_array($statusName, ['Resuelto', 'Cerrado'])) {
            TicketActivity::create([
                'ticket_id'   => $ticket->id,
                'user_id'     => $agent->id,
                'action'      => 'updated',
                'description' => 'Ticket marcado como ' . strtolower($statusName),
                'created_at'  => $cursor,
                'updated_at'  => $cursor,
            ]);
        }
    }

    // ─── Mensajes del ticket ──────────────────────────────────────────────────

    private function seedMessages(Ticket $ticket, User $client, User $agent, string $statusName, Carbon $base): void
    {
        // Mensajes proporcionales al estado de avance
        $messageCount = match($statusName) {
            'Nuevo'       => rand(0, 1),
            'Abierto'     => rand(1, 2),
            'En Progreso' => rand(2, 4),
            'En Espera'   => rand(2, 3),
            'Resuelto'    => rand(3, 5),
            'Cerrado'     => rand(2, 4),
            default       => 1,
        };

        $cursor = $base->copy()->addMinutes(rand(10, 120));

        for ($i = 0; $i < $messageCount; $i++) {
            $isAgentTurn = $i % 2 !== 0; // Alternamos: cliente, agente, cliente...
            $isInternal  = $isAgentTurn && rand(0, 4) === 0; // 20% de los mensajes de agentes son notas internas

            TicketMessage::create([
                'ticket_id'   => $ticket->id,
                'user_id'     => $isAgentTurn ? $agent->id : $client->id,
                'message'     => $isInternal
                    ? $this->randomInternalNote()
                    : ($isAgentTurn ? $this->randomAgentReply() : $this->randomClientMessage()),
                'is_internal' => $isInternal,
                'created_at'  => $cursor,
                'updated_at'  => $cursor,
            ]);

            $cursor->addMinutes(rand(15, 180));
        }
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

private int $globalCounter = 0;

private function nextTicketNumber(): string
{
    $this->globalCounter++;

    return 'TKT-' . str_pad(
        $this->globalCounter,
        5,
        '0',
        STR_PAD_LEFT
    );
}

    private function statusTransitions(string $statusName): array
    {
        return match($statusName) {
            'Abierto'     => ['Movido de "Nuevo" a "Abierto"'],
            'En Progreso' => ['Movido de "Nuevo" a "Abierto"', 'Movido de "Abierto" a "En Progreso"'],
            'En Espera'   => ['Movido de "Nuevo" a "Abierto"', 'Movido de "Abierto" a "En Espera"'],
            'Resuelto'    => ['Movido de "Abierto" a "En Progreso"', 'Movido de "En Progreso" a "Resuelto"'],
            'Cerrado'     => ['Movido de "En Progreso" a "Resuelto"', 'Movido de "Resuelto" a "Cerrado"'],
            default       => [],
        };
    }

    // ─── Contenido de ejemplo ─────────────────────────────────────────────────

    private function ticketTemplates(): array
    {
        return [
            [
                'title'       => 'Error crítico en pasarela de pago',
                'description' => 'Los usuarios no pueden completar pagos en el checkout. El error aparece después de ingresar los datos de la tarjeta. Afecta a todos los clientes desde las 10:30 AM.',
            ],
            [
                'title'       => 'Pantalla en blanco al iniciar sesión',
                'description' => 'Después de ingresar las credenciales correctas, la aplicación muestra una pantalla en blanco. El problema se reproduce en Chrome y Firefox. Versión afectada: 2.4.1.',
            ],
            [
                'title'       => 'Solicitud de acceso a módulo de reportes',
                'description' => 'Necesito acceso al módulo de reportes avanzados para poder exportar los datos mensuales de ventas. Mi supervisor ya aprobó la solicitud.',
            ],
            [
                'title'       => 'Impresora de red no responde',
                'description' => 'La impresora HP LaserJet del piso 3 dejó de ser accesible desde ayer. Otros equipos en la misma red tampoco pueden conectarse. IP: 192.168.1.45.',
            ],
            [
                'title'       => 'Correos llegando a carpeta de spam',
                'description' => 'Los correos enviados desde nuestro dominio están siendo marcados como spam por los clientes. El problema afecta principalmente a cuentas de Gmail y Outlook.',
            ],
            [
                'title'       => 'Timeout recurrente en endpoint /api/auth',
                'description' => 'El endpoint de autenticación presenta timeouts intermitentes bajo carga alta. Ocurre especialmente en horas pico (9-11 AM). Logs adjuntos en la descripción extendida.',
            ],
            [
                'title'       => 'Actualización de software requerida en equipos de contabilidad',
                'description' => 'El departamento de contabilidad necesita actualizar el sistema SAP a la versión 4.5. Son 8 equipos en total. Se requiere ventana de mantenimiento nocturna.',
            ],
            [
                'title'       => 'Pérdida de datos en formulario de registro',
                'description' => 'Al enviar el formulario de registro de nuevos empleados, los datos se pierden sin mostrar error. El problema comenzó después del despliegue del viernes.',
            ],
            [
                'title'       => 'VPN no conecta desde home office',
                'description' => 'Varios empleados reportan que la VPN corporativa no conecta desde redes domésticas. El error es "Authentication failed". Afecta a 12 usuarios identificados.',
            ],
            [
                'title'       => 'Dashboard no carga en Safari',
                'description' => 'El dashboard principal muestra solo el spinner de carga indefinidamente cuando se accede desde Safari 16+. En Chrome y Edge funciona correctamente.',
            ],
            [
                'title'       => 'Configurar backup automático de base de datos',
                'description' => 'Necesitamos implementar backups automáticos diarios de la base de datos de producción. Actualmente los backups son manuales y han habido olvidos.',
            ],
            [
                'title'       => 'Facturas duplicadas en el sistema',
                'description' => 'Se detectaron 23 facturas duplicadas en el sistema durante el cierre de mes. Parece ser un problema con el proceso de sincronización con el banco.',
            ],
            [
                'title'       => 'Error 500 al exportar reportes en PDF',
                'description' => 'La funcionalidad de exportar reportes a PDF devuelve un error 500 para reportes con más de 500 registros. Reportes pequeños funcionan sin problema.',
            ],
            [
                'title'       => 'Integración con API de proveedor caída',
                'description' => 'La integración con la API de nuestro proveedor de logística dejó de funcionar. El proveedor confirmó que no hay cambios de su lado. Revisión urgente requerida.',
            ],
            [
                'title'       => 'Solicitud de nueva cuenta de correo corporativo',
                'description' => 'El nuevo empleado Juan Pérez (Dpto. Marketing) necesita su cuenta de correo corporativo. Fecha de inicio: próximo lunes. Requiere acceso a los grupos de distribución del departamento.',
            ],
        ];
    }

    private function randomClientMessage(): string
    {
        $messages = [
            'Buenos días, ¿hay alguna actualización sobre mi ticket? El problema sigue afectando mi trabajo.',
            'Gracias por la respuesta. Seguí los pasos indicados pero el problema persiste.',
            'El error sigue ocurriendo. Adjunto captura de pantalla para mayor referencia.',
            '¿Cuánto tiempo estimado tomaría la resolución? Necesito planificar mis actividades.',
            'Confirmo que el problema sigue presente. ¿Necesitan acceso remoto para revisarlo?',
            'Hola, solo quería hacer seguimiento. ¿Ya tienen un diagnóstico?',
        ];
        return $messages[array_rand($messages)];
    }

    private function randomAgentReply(): string
    {
        $messages = [
            'Hola, recibí tu ticket. Estamos investigando el problema y te contactaremos en breve.',
            'Gracias por el reporte detallado. He escalado el caso al equipo de segundo nivel.',
            'Hemos identificado la causa del problema. Estamos preparando la solución, tiempo estimado: 2 horas.',
            'Por favor, intenta limpiar el caché del navegador y vuelve a intentarlo. Si el problema persiste, avísame.',
            'El problema fue resuelto. ¿Puedes confirmar que todo funciona correctamente desde tu lado?',
            'Necesito información adicional para continuar. ¿Puedes compartir el mensaje de error exacto?',
            'Hemos aplicado un parche temporal mientras trabajamos en la solución definitiva.',
        ];
        return $messages[array_rand($messages)];
    }

    private function randomInternalNote(): string
    {
        $notes = [
            'NOTA INTERNA: Revisado con el equipo de infraestructura. El problema está relacionado con la última actualización de dependencias.',
            'NOTA INTERNA: Cliente VIP — priorizar resolución. Contactado por el gerente de cuenta.',
            'NOTA INTERNA: Posible bug conocido #4521. Verificar si el hotfix del sprint anterior lo cubre.',
            'NOTA INTERNA: El cliente tiene contrato SLA Platinum. Tiempo de resolución máximo: 4 horas.',
            'NOTA INTERNA: Escalado a Luis (DBA) para revisión de queries lentos.',
        ];
        return $notes[array_rand($notes)];
    }
}

<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\TicketStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TicketService
{
    /**
     * Campos opcionales que se omiten del INSERT si vienen como null.
     * Evita violar constraints NOT NULL de columnas opcionales en la migración.
     */
    private const NULLABLE_FIELDS = [
        'project_id', 'helpdesk_id', 'category_id',
        'type_id', 'priority_id', 'assigned_to',
        'due_date', 'description',
    ];

    // ─────────────────────────────────────────────────────────────────────────

    public function create(array $data, User $author): Ticket
    {
        return DB::transaction(function () use ($data, $author) {
            $ticket = Ticket::create([
                ...$this->stripNulls($data),
                'company_id'    => $author->company_id,
                'created_by'    => $author->id,
                'ticket_number' => $this->generateTicketNumber($author->company_id),
            ]);

            $this->logActivity($ticket, $author, 'created', 'Ticket creado');

            return $ticket->load($this->defaultRelations());
        });
    }

    public function update(Ticket $ticket, array $data, User $author): Ticket
    {
        return DB::transaction(function () use ($ticket, $data, $author) {
            $changes = $this->describeChanges($ticket, $data);

            // Filtrar nulls también en update para evitar sobreescribir con null
            $ticket->update($this->stripNulls($data));

            if ($changes) {
                $this->logActivity($ticket, $author, 'updated', $changes);
            }

            return $ticket->fresh($this->defaultRelations());
        });
    }

    public function move(Ticket $ticket, TicketStatus $newStatus, User $author): Ticket
    {
        return DB::transaction(function () use ($ticket, $newStatus, $author) {
            $fromName = $ticket->status?->name ?? 'Sin estado';

            $ticket->update(['status_id' => $newStatus->id]);

            // Cerrar automáticamente si el estado lo indica
            if (str_contains(strtolower($newStatus->name), 'cerrado') ||
                str_contains(strtolower($newStatus->name), 'closed')) {
                $ticket->update(['closed_at' => now()]);
            }

            $this->logActivity(
                $ticket, $author, 'moved',
                "Movido de \"{$fromName}\" a \"{$newStatus->name}\""
            );

            return $ticket->fresh($this->defaultRelations());
        });
    }

    public function addMessage(Ticket $ticket, string $message, bool $isInternal, User $author): void
    {
        DB::transaction(function () use ($ticket, $message, $isInternal, $author) {
            $ticket->messages()->create([
                'user_id'     => $author->id,
                'message'     => $message,
                'is_internal' => $isInternal,
            ]);

            $label = $isInternal ? 'nota interna' : 'respuesta';
            $this->logActivity($ticket, $author, 'message_added', "Agregó una {$label}");
        });
    }

    public function assign(Ticket $ticket, ?int $agentId, User $author): Ticket
    {
        return DB::transaction(function () use ($ticket, $agentId, $author) {
            $ticket->update(['assigned_to' => $agentId]);

            $agentName = $agentId
                ? optional(User::find($agentId))->name ?? 'Desconocido'
                : 'Nadie';

            $this->logActivity($ticket, $author, 'assigned', "Asignado a: {$agentName}");

            return $ticket->fresh($this->defaultRelations());
        });
    }

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function defaultRelations(): array
    {
        return [
            'status',
            'priority',
            'type',
            'category',
            'project',
            'helpdesk',
            'createdBy',
            'assignedTo',
            'messages',
            'messages.user',
            'activities',
            'activities.user',
        ];
    }

    // ── Helpers privados ──────────────────────────────────────────────────────

    /**
     * Registra actividad directamente en la BD, sin pasar por el sistema de eventos.
     * Evita 500 por listeners no registrados en EventServiceProvider.
     */
    private function logActivity(Ticket $ticket, User $user, string $action, string $description): void
    {
        TicketActivity::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => $user->id,
            'action'      => $action,
            'description' => $description,
        ]);
    }

    /**
     * Elimina del array los campos opcionales cuyo valor sea null.
     * Necesario cuando la columna en BD es NOT NULL pero el campo es opcional en la app.
     */
    private function stripNulls(array $data): array
    {
        return array_filter(
            $data,
            fn($value, $key) => ! (in_array($key, self::NULLABLE_FIELDS) && is_null($value)),
            ARRAY_FILTER_USE_BOTH
        );
    }

    /**
     * Genera el número de ticket globalmente único. Formato: TKT-00001
     *
     * IMPORTANTE: El índice unique en tickets.ticket_number es global (no por compañía),
     * por lo tanto el MAX debe consultarse sobre TODA la tabla, no filtrado por company_id.
     * Usar COUNT causaba duplicados porque no consideraba tickets de otras compañías
     * ni huecos dejados por intentos fallidos anteriores.
     */
    private function generateTicketNumber(int $companyId): string
    {
        // MAX global sobre todos los tickets para respetar el unique index global
        $max = Ticket::lockForUpdate()
            ->whereRaw("ticket_number REGEXP '^TKT-[0-9]+$'")
            ->max(DB::raw("CAST(SUBSTRING_INDEX(ticket_number, '-', -1) AS UNSIGNED)")) ?? 0;

        return 'TKT-' . str_pad((int)$max + 1, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Describe los cambios de una actualización para el log de actividad.
     */
    private function describeChanges(Ticket $ticket, array $data): string
    {
        $labels = [
            'status_id'   => 'Estado',
            'priority_id' => 'Prioridad',
            'assigned_to' => 'Asignado a',
            'title'       => 'Título',
        ];

        $parts = [];
        foreach ($labels as $field => $label) {
            if (array_key_exists($field, $data) && $data[$field] != $ticket->{$field}) {
                $parts[] = "{$label} actualizado";
            }
        }

        return implode(', ', $parts);
    }
}

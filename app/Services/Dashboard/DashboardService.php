<?php
// app/Services/DashboardService.php
//
// Nivel: INTERMEDIO — lógica de negocio moderada (queries por rol, agregaciones).
// No usa Repository: las queries son lecturas agregadas, no entidades CRUD.
// Recibe el User autenticado y devuelve arrays listos para Inertia.

namespace App\Services\Dashboard;

use App\Models\Company;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    // ── Punto de entrada principal ────────────────────────────────────────────

    public function getData(User $user): array
    {
        return match ($user->role) {
            'super_admin' => $this->superAdminData($user),
            'admin'       => $this->adminData($user),
            'agent'       => $this->agentData($user),
            'client'      => $this->clientData($user),
            default       => $this->agentData($user),
        };
    }

    // ── super_admin ───────────────────────────────────────────────────────────

    private function superAdminData(User $user): array
    {
        $base = Ticket::query();

        return [
            'role'                 => 'super_admin',
            'stats'                => $this->globalStats($base),
            'by_status'            => $this->byStatus(Ticket::query()),
            'by_priority'          => $this->byPriority(Ticket::query()),
            'by_company'           => $this->byCompany(),
            'by_agent'             => $this->byAgent(Ticket::query()),
            'trend'                => $this->trend(Ticket::query()),
            'avg_resolution_hours' => $this->avgResolution(Ticket::query()),
            'recent_tickets'       => $this->recentTickets(Ticket::query()),
        ];
    }

    // ── admin ─────────────────────────────────────────────────────────────────

    private function adminData(User $user): array
    {
        $companyId = $user->company_id;
        $base      = fn() => Ticket::where('tickets.company_id', $companyId);

        return [
            'role'                 => 'admin',
            'stats'                => $this->globalStats($base()),
            'by_status'            => $this->byStatus($base()),
            'by_priority'          => $this->byPriority($base()),
            'by_company'           => [],
            'by_agent'             => $this->byAgent($base()),
            'trend'                => $this->trend($base()),
            'avg_resolution_hours' => $this->avgResolution($base()),
            'recent_tickets'       => $this->recentTickets($base()),
        ];
    }

    // ── agent ─────────────────────────────────────────────────────────────────

    private function agentData(User $user): array
    {
        $base = fn() => Ticket::where('tickets.company_id', $user->company_id)
            ->where(fn($q) =>
                $q->where('tickets.assigned_to', $user->id)
                  ->orWhere('tickets.created_by', $user->id)
            );

        return [
            'role'                 => 'agent',
            'stats'                => $this->globalStats($base()),
            'by_status'            => $this->byStatus($base()),
            'by_priority'          => $this->byPriority($base()),
            'by_company'           => [],
            'by_agent'             => [],
            'trend'                => $this->trend($base()),
            'avg_resolution_hours' => $this->avgResolution($base()),
            'recent_tickets'       => $this->recentTickets($base()),
        ];
    }

    // ── client ────────────────────────────────────────────────────────────────

    private function clientData(User $user): array
    {
        $base = fn() => Ticket::where('tickets.company_id', $user->company_id)
            ->where('tickets.created_by', $user->id);

        return [
            'role'                 => 'client',
            'stats'                => $this->globalStats($base()),
            'by_status'            => $this->byStatus($base()),
            'by_priority'          => $this->byPriority($base()),
            'by_company'           => [],
            'by_agent'             => [],
            'trend'                => $this->trend($base()),
            'avg_resolution_hours' => $this->avgResolution($base()),
            'recent_tickets'       => $this->recentTickets($base()),
        ];
    }

    // ── Métricas individuales ─────────────────────────────────────────────────

    private function globalStats($query): array
    {
        $now = Carbon::now();

        $total   = (clone $query)->count();
        $closed  = (clone $query)->whereNotNull('closed_at')->count();
        $open    = $total - $closed;
        $overdue = (clone $query)
            ->whereNull('closed_at')
            ->whereNotNull('due_date')
            ->where('due_date', '<', $now)
            ->count();
        $dueSoon = (clone $query)
            ->whereNull('closed_at')
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$now, $now->copy()->addDays(3)])
            ->count();

        return compact('total', 'open', 'closed', 'overdue', 'dueSoon');
    }

    private function byStatus($query): array
    {
        return (clone $query)
            ->join('ticket_statuses', 'tickets.status_id', '=', 'ticket_statuses.id')
            ->groupBy('ticket_statuses.id', 'ticket_statuses.name', 'ticket_statuses.color', 'ticket_statuses.order')
            ->orderBy('ticket_statuses.order')
            ->select(
                'ticket_statuses.name',
                'ticket_statuses.color',
                DB::raw('COUNT(tickets.id) as count')
            )
            ->get()
            ->map(fn($r) => ['name' => $r->name, 'color' => $r->color, 'count' => $r->count])
            ->toArray();
    }

    private function byPriority($query): array
    {
        return (clone $query)
            ->leftJoin('ticket_priorities', 'tickets.priority_id', '=', 'ticket_priorities.id')
            ->groupBy('ticket_priorities.id', 'ticket_priorities.name', 'ticket_priorities.color', 'ticket_priorities.level')
            ->orderBy('ticket_priorities.level')
            ->select(
                DB::raw("COALESCE(ticket_priorities.name, 'Sin prioridad') as name"),
                DB::raw("COALESCE(ticket_priorities.color, '#94a3b8') as color"),
                DB::raw('COUNT(tickets.id) as count')
            )
            ->get()
            ->map(fn($r) => ['name' => $r->name, 'color' => $r->color, 'count' => $r->count])
            ->toArray();
    }

    private function byCompany(): array
    {
        return Ticket::query()
            ->join('companies', 'tickets.company_id', '=', 'companies.id')
            ->groupBy('companies.id', 'companies.name')
            ->orderByDesc('count')
            ->select('companies.name', DB::raw('COUNT(tickets.id) as count'))
            ->limit(10)
            ->get()
            ->map(fn($r) => ['name' => $r->name, 'count' => $r->count])
            ->toArray();
    }

    private function byAgent($query): array
    {
        return (clone $query)
            ->whereNotNull('tickets.assigned_to')
            ->join('users', 'tickets.assigned_to', '=', 'users.id')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total')
            ->select(
                'users.name',
                DB::raw('COUNT(tickets.id) as total'),
                DB::raw('SUM(CASE WHEN tickets.closed_at IS NOT NULL THEN 1 ELSE 0 END) as closed'),
                DB::raw('SUM(CASE WHEN tickets.closed_at IS NULL THEN 1 ELSE 0 END) as open')
            )
            ->limit(8)
            ->get()
            ->map(fn($r) => ['name' => $r->name, 'total' => $r->total, 'open' => $r->open, 'closed' => $r->closed])
            ->toArray();
    }

    private function trend($query): array
    {
        $days = 14;
        $from = Carbon::now()->subDays($days - 1)->startOfDay();

        $counts = (clone $query)
            ->where('tickets.created_at', '>=', $from)
            ->groupBy('day')
            ->select(DB::raw('DATE(tickets.created_at) as day'), DB::raw('COUNT(*) as count'))
            ->orderBy('day')
            ->pluck('count', 'day');

        // Rellenar días sin datos con 0
        $result = [];
        for ($i = 0; $i < $days; $i++) {
            $date = Carbon::now()->subDays($days - 1 - $i)->toDateString();
            $result[] = [
                'date'  => $date,
                'label' => Carbon::parse($date)->locale('es')->isoFormat('D MMM'),
                'count' => (int) ($counts[$date] ?? 0),
            ];
        }

        return $result;
    }

    private function avgResolution($query): ?float
    {
        $avg = (clone $query)
            ->whereNotNull('closed_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, tickets.created_at, tickets.closed_at)) as avg_hours'))
            ->value('avg_hours');

        return $avg ? round((float) $avg, 1) : null;
    }

    private function recentTickets($query): array
    {
        return (clone $query)
            ->with(['status:id,name,color', 'priority:id,name,color', 'assignedTo:id,name'])
            ->orderByDesc('tickets.created_at')
            ->limit(5)
            ->select('tickets.id', 'tickets.ticket_number', 'tickets.title', 'tickets.status_id',
                     'tickets.priority_id', 'tickets.assigned_to', 'tickets.created_at', 'tickets.due_date')
            ->get()
            ->map(fn($t) => [
                'id'            => $t->id,
                'ticket_number' => $t->ticket_number,
                'title'         => $t->title,
                'status'        => $t->status ? ['name' => $t->status->name, 'color' => $t->status->color] : null,
                'priority'      => $t->priority ? ['name' => $t->priority->name, 'color' => $t->priority->color] : null,
                'assigned_user' => $t->assignedTo?->name,
                'created_at'    => $t->created_at->diffForHumans(),
                'due_date'      => $t->due_date?->toDateString(),
            ])
            ->toArray();
    }
}

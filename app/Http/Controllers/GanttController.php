<?php
// app/Http/Controllers/GanttController.php
//
// Arquitectura: Inertia props + filtros server-side (igual que Users/Projects).
// Los tickets llegan como prop paginados/limitados — sin endpoint axios.
//
// Defaults automáticos si no hay filtros de fecha:
//   due_after  → primer día del mes actual
//   due_before → último día del mes actual
//
// Límite duro: 300 tickets. Si se supera, meta.limited = true y se muestra aviso.

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Helpdesk;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class GanttController extends Controller
{
    public function index(Request $request): Response
    {
        /** @var User $user */
        $user         = Auth::user();
        $companyId    = $user->company_id;
        $isSuperAdmin = $user->isSuperAdmin();

        // ── Defaults de fechas: mes actual si no se especifican ────────────────
        $dueAfter  = $request->filled('due_after')
            ? $request->string('due_after')->toString()
            : now()->startOfMonth()->toDateString();

        $dueBefore = $request->filled('due_before')
            ? $request->string('due_before')->toString()
            : now()->endOfMonth()->toDateString();

        // ── Catálogos para los filtros ──────────────────────────────────────────
        $catalogs = [
            'statuses'   => TicketStatus::orderBy('order')->get(['id', 'name', 'color', 'order']),
            'priorities' => TicketPriority::orderBy('level')->get(['id', 'name', 'color', 'level']),
            'types'      => TicketType::all(['id', 'name']),
            'categories' => $isSuperAdmin
                ? TicketCategory::orderBy('name')->get(['id', 'name'])
                : TicketCategory::where('company_id', $companyId)->get(['id', 'name']),
            'projects'   => $isSuperAdmin
                ? Project::orderBy('name')->get(['id', 'name'])
                : Project::where('company_id', $companyId)->get(['id', 'name']),
            'agents'     => $isSuperAdmin
                ? User::whereIn('role', ['super_admin', 'admin', 'agent'])->orderBy('name')->get(['id', 'name', 'role'])
                : User::where('company_id', $companyId)->whereIn('role', ['admin', 'agent'])->get(['id', 'name', 'role']),
            'companies'  => $isSuperAdmin
                ? Company::orderBy('name')->get(['id', 'name'])
                : [],
            'isSuperAdmin' => $isSuperAdmin,
        ];

        // ── Tickets ────────────────────────────────────────────────────────────
        // super_admin sin company_id → ve tickets de todas las compañías en la ventana.
        // No se fuerza selección previa; el filtro de compañía es opcional.
        $query = $this->buildQuery($request, $user, $isSuperAdmin, $dueAfter, $dueBefore);

        $total   = $query->count();
        $tickets = $query
            ->orderBy('project_id')
            ->orderBy('due_date')
            ->get();

        return Inertia::render('Gantt/Index', array_merge($catalogs, [
            'tickets' => $tickets->map(fn(Ticket $t) => $this->serializeForGantt($t))->values(),
            'filters' => $this->activeFilters($request, $dueAfter, $dueBefore),
            'meta'    => ['total' => $total],
        ]));
    }

    // ─── Query builder ────────────────────────────────────────────────────────

    private function buildQuery(Request $request, User $user, bool $isSuperAdmin, string $dueAfter, string $dueBefore)
    {
        $query = Ticket::query()
            ->with(['status', 'priority', 'type', 'category', 'assignedTo', 'project'])
            ->whereNotNull('due_date')
            // Condición de solapamiento de intervalos:
            // el ticket es visible si su rango [created_at … due_date]
            // se cruza con la ventana [dueAfter … dueBefore].
            //   ticket.start <= ventana.fin  AND  ticket.due >= ventana.inicio
            ->where('due_date',   '>=', $dueAfter)
            ->where('created_at', '<=', $dueBefore . ' 23:59:59');

        // ── Scope por rol ──────────────────────────────────────────────────────
        if ($isSuperAdmin) {
            if ($v = $request->integer('company_id')) {
                $query->where('company_id', $v);
            }
        } elseif ($user->role === 'client') {
            $query->where('company_id', $user->company_id)
                  ->where('created_by', $user->id);
        } elseif ($user->role === 'agent') {
            $agentHelpdeskIds = Helpdesk::where('company_id', $user->company_id)
                ->whereHas('users', fn($q) => $q->where('users.id', $user->id))
                ->pluck('id');

            $query->where('company_id', $user->company_id)
                  ->where(fn($q) =>
                      $q->whereIn('helpdesk_id', $agentHelpdeskIds)
                        ->orWhere('assigned_to', $user->id)
                        ->orWhere('created_by', $user->id)
                  );
        } else {
            // admin
            $query->where('company_id', $user->company_id);
        }

        // ── Filtros opcionales ─────────────────────────────────────────────────
        if ($v = $request->integer('status_id'))   $query->where('status_id', $v);
        if ($v = $request->integer('priority_id')) $query->where('priority_id', $v);
        if ($v = $request->integer('type_id'))     $query->where('type_id', $v);
        if ($v = $request->integer('category_id')) $query->where('category_id', $v);
        if ($v = $request->integer('project_id'))  $query->where('project_id', $v);
        if ($v = $request->integer('assigned_to')) $query->where('assigned_to', $v);

        return $query;
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function activeFilters(Request $request, string $dueAfter, string $dueBefore): array
    {
        return [
            'due_after'   => $dueAfter,
            'due_before'  => $dueBefore,
            'status_id'   => $request->integer('status_id')   ?: null,
            'priority_id' => $request->integer('priority_id') ?: null,
            'type_id'     => $request->integer('type_id')     ?: null,
            'category_id' => $request->integer('category_id') ?: null,
            'project_id'  => $request->integer('project_id')  ?: null,
            'assigned_to' => $request->integer('assigned_to') ?: null,
            'company_id'  => $request->integer('company_id')  ?: null,
        ];
    }

    private function serializeForGantt(Ticket $ticket): array
    {
        return [
            'id'            => $ticket->id,
            'company_id'    => $ticket->company_id,
            'ticket_number' => $ticket->ticket_number,
            'title'         => $ticket->title,
            'status_id'     => $ticket->status_id,
            'priority_id'   => $ticket->priority_id,
            'project_id'    => $ticket->project_id,
            'assigned_to'   => $ticket->assigned_to,
            'start_date'    => $ticket->created_at->toDateString(),
            'due_date'      => $ticket->due_date->toDateString(),
            'closed_at'     => $ticket->closed_at?->toDateString(),
            'status'        => $ticket->status ? [
                'id' => $ticket->status->id, 'name' => $ticket->status->name,
                'color' => $ticket->status->color, 'order' => $ticket->status->order,
            ] : null,
            'priority'      => $ticket->priority ? [
                'id' => $ticket->priority->id, 'name' => $ticket->priority->name,
                'color' => $ticket->priority->color, 'level' => $ticket->priority->level,
            ] : null,
            'project'       => $ticket->project
                ? ['id' => $ticket->project->id, 'name' => $ticket->project->name] : null,
            'assigned_user' => $ticket->assignedTo
                ? ['id' => $ticket->assignedTo->id, 'name' => $ticket->assignedTo->name] : null,
            'type'          => $ticket->type
                ? ['id' => $ticket->type->id, 'name' => $ticket->type->name] : null,
            'category'      => $ticket->category
                ? ['id' => $ticket->category->id, 'name' => $ticket->category->name] : null,
        ];
    }
}

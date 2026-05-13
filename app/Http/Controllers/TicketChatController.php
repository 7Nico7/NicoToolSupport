<?php
// app/Http/Controllers/TicketChatController.php
//
// Controlador dedicado a la vista de chat de ticket.
// Independiente de KanbanController — tiene su propio layout de datos
// porque la vista de chat necesita más info del cliente y del hilo completo.

namespace App\Http\Controllers;

use App\Models\Helpdesk;
use App\Models\MessageAttachment;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketMessage;
use App\Models\TicketStatus;
use App\Models\User;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TicketChatController extends Controller
{
    public function __construct(private readonly TicketService $ticketService) {}

    /**
     * Lista de tickets accesibles por el usuario actual.
     * Si se pasa ?ticket_id=X, Inertia renderiza ese ticket activo.
     */
    public function index(Request $request): Response
    {
        /** @var User $user */
        $user  = Auth::user();
        $query = Ticket::query()
            ->where('company_id', $user->company_id)
            ->with(['status', 'priority', 'createdBy', 'assignedTo'])
            ->withCount('messages');

        if ($user->isClient()) {
            $query->where('created_by', $user->id);
        } elseif ($user->isAgent()) {
            $agentHelpdeskIds = Helpdesk::where('company_id', $user->company_id)
                ->whereHas('users', fn ($q) => $q->where('users.id', $user->id))
                ->pluck('id');

            $query->where(
                fn ($q) => $q->whereIn('helpdesk_id', $agentHelpdeskIds)
                    ->orWhere('assigned_to', $user->id)
                    ->orWhere('created_by', $user->id)
            );
        }

        // Filtro de búsqueda opcional desde el sidebar
        if ($search = $request->string('search')->toString()) {
            $query->where(
                fn ($q) => $q->where('title', 'like', "%{$search}%")
                    ->orWhere('ticket_number', 'like', "%{$search}%")
            );
        }

        $tickets = $query->orderBy('updated_at', 'desc')->get();

        // Ticket activo (si se navega directamente a uno)
        $activeTicket = null;
        if ($ticketId = $request->integer('ticket_id')) {
            $found = $tickets->find($ticketId);
            if ($found) {
                $activeTicket = $this->loadFullTicket($found, $user);
            }
        }

        return Inertia::render('Tickets/Show', [
            'tickets'      => $tickets->map(fn (Ticket $t) => $this->serializeListItem($t)),
            'activeTicket' => $activeTicket,
            'statuses'     => TicketStatus::orderBy('order')->get(['id', 'name', 'color']),
            'can'          => [
                'create'   => in_array($user->role, ['super_admin', 'admin', 'agent']),
                'delete'   => in_array($user->role, ['super_admin', 'admin']),
                'internal' => in_array($user->role, ['super_admin', 'admin', 'agent']),
            ],
        ]);
    }

    /**
     * Carga el thread completo de un ticket vía Inertia partial reload.
     * Cuando el usuario hace click en un ítem del sidebar, se llama a esta ruta
     * y se reemplaza solo la prop `activeTicket` sin recargar la página.
     */
    public function show(Ticket $ticket): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        abort_if($ticket->company_id !== $user->company_id, 403, 'Sin acceso.');

        if ($user->isClient() && $ticket->created_by !== $user->id) {
            abort(403, 'Sin acceso.');
        }

        return response()->json($this->loadFullTicket($ticket, $user));
    }

    // ─── Helpers privados ─────────────────────────────────────────────────────

    private function loadFullTicket(Ticket $ticket, User $user): array
    {
        $ticket->load([
            ...$this->ticketService->defaultRelations(),
            'attachments.user',
            'messages.attachments.user',
            // Datos del creador del ticket para el panel derecho
            'createdBy',
        ]);

        return $this->serializeFullTicket($ticket);
    }

    /** Serialización ligera para el listado del sidebar izquierdo */
    private function serializeListItem(Ticket $ticket): array
    {
        return [
            'id'             => $ticket->id,
            'ticket_number'  => $ticket->ticket_number,
            'title'          => $ticket->title,
            'messages_count' => $ticket->messages_count,
            'updated_at'     => $ticket->updated_at->toIso8601String(),
            'created_by_name'=> $ticket->createdBy?->name,
            'status'         => $ticket->status ? [
                'id'    => $ticket->status->id,
                'name'  => $ticket->status->name,
                'color' => $ticket->status->color,
            ] : null,
            'priority'       => $ticket->priority ? [
                'id'    => $ticket->priority->id,
                'name'  => $ticket->priority->name,
                'color' => $ticket->priority->color,
                'level' => $ticket->priority->level,
            ] : null,
        ];
    }

    /** Serialización completa para la vista de chat activa */
    private function serializeFullTicket(Ticket $ticket): array
    {
        return [
            'id'            => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'title'         => $ticket->title,
            'description'   => $ticket->description,
            'due_date'      => $ticket->due_date?->toDateString(),
            'closed_at'     => $ticket->closed_at?->toIso8601String(),
            'created_at'    => $ticket->created_at->toIso8601String(),
            'updated_at'    => $ticket->updated_at->toIso8601String(),
            'status_id'     => $ticket->status_id,
            'priority_id'   => $ticket->priority_id,

            'status'        => $ticket->status
                ? ['id' => $ticket->status->id, 'name' => $ticket->status->name, 'color' => $ticket->status->color]
                : null,
            'priority'      => $ticket->priority
                ? ['id' => $ticket->priority->id, 'name' => $ticket->priority->name, 'color' => $ticket->priority->color, 'level' => $ticket->priority->level]
                : null,
            'type'          => $ticket->type
                ? ['id' => $ticket->type->id, 'name' => $ticket->type->name]
                : null,
            'category'      => $ticket->category
                ? ['id' => $ticket->category->id, 'name' => $ticket->category->name]
                : null,

            // Personas
            'assigned_user' => $ticket->assignedTo
                ? ['id' => $ticket->assignedTo->id, 'name' => $ticket->assignedTo->name, 'role' => $ticket->assignedTo->role]
                : null,
            'created_user'  => $ticket->createdBy
                ? ['id' => $ticket->createdBy->id, 'name' => $ticket->createdBy->name, 'email' => $ticket->createdBy->email]
                : null,

            // Mensajes con sus adjuntos
            'messages'      => $ticket->messages->map(fn (TicketMessage $m) => [
                'id'          => $m->id,
                'message'     => $m->message,
                'is_internal' => $m->is_internal,
                'created_at'  => $m->created_at->toIso8601String(),
                'user'        => $m->user
                    ? ['id' => $m->user->id, 'name' => $m->user->name, 'role' => $m->user->role ?? null]
                    : null,
                'attachments' => $m->attachments->map(fn (MessageAttachment $a) => [
                    'id'            => $a->id,
                    'filename'      => $a->filename,
                    'mime_type'     => $a->mime_type,
                    'file_category' => $a->fileCategory(),
                    'readable_size' => $a->readableSize(),
                    'download_url'  => $a->downloadUrl(),
                ])->values()->all(),
            ])->values()->all(),

            // Evidencias directas del ticket
            'attachments'   => $ticket->attachments->map(fn (TicketAttachment $a) => [
                'id'            => $a->id,
                'filename'      => $a->filename,
                'mime_type'     => $a->mime_type,
                'file_category' => $a->fileCategory(),
                'readable_size' => $a->readableSize(),
                'description'   => $a->description,
                'download_url'  => $a->downloadUrl(),
                'created_at'    => $a->created_at->toIso8601String(),
                'user'          => $a->user
                    ? ['id' => $a->user->id, 'name' => $a->user->name]
                    : null,
            ])->values()->all(),
        ];
    }
}

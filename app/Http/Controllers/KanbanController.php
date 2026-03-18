<?php
// app/Http/Controllers/KanbanController.php

namespace App\Http\Controllers;

use App\Events\MessageSent;                      // ← nuevo import
use App\Http\Requests\MoveTicketRequest;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Helpdesk;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketType;
use App\Models\User;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class KanbanController extends Controller
{
    public function __construct(private readonly TicketService $ticketService) {}

    // ─── Vista principal ────────────────────────────────────────────────────────

    public function index(): Response
    {
        /** @var User $user */
        $user      = Auth::user();
        $companyId = $user->company_id;

        return Inertia::render('Kanban/Index', [
            'statuses'   => TicketStatus::orderBy('order')->get(['id', 'name', 'color', 'order']),
            'priorities' => TicketPriority::orderBy('level')->get(['id', 'name', 'color', 'level']),
            'types'      => TicketType::all(['id', 'name']),
            'categories' => TicketCategory::where('company_id', $companyId)->get(['id', 'name']),
            'projects'   => Project::where('company_id', $companyId)->where('is_active', true)->get(['id', 'name']),
            'helpdesks'  => Helpdesk::where('company_id', $companyId)->get(['id', 'name']),
            'agents'     => User::where('company_id', $companyId)
                ->whereIn('role', ['agent', 'admin'])
                ->get(['id', 'name', 'role']),
            'can' => [
                'create' => in_array($user->role, ['super_admin', 'admin', 'agent']),
                'delete' => in_array($user->role, ['super_admin', 'admin']),
            ],
        ]);
    }

    // ─── Tickets ────────────────────────────────────────────────────────────────

    public function indexTickets(Request $request): JsonResponse
    {
        /** @var User $user */
        $user  = Auth::user();
        $query = Ticket::query()
            ->where('company_id', $user->company_id)
            ->with(['status', 'priority', 'type', 'category', 'assignedTo', 'createdBy'])
            ->withCount(['messages', 'activities']);

        if ($user->isClient()) {
            $query->where('created_by', $user->id);
        } elseif ($user->isAgent()) {
            $agentHelpdeskIds = Helpdesk::where('company_id', $user->company_id)
                ->whereHas('users', fn($q) => $q->where('users.id', $user->id))
                ->pluck('id');

            $query->where(
                fn($q) =>
                $q->whereIn('helpdesk_id', $agentHelpdeskIds)
                    ->orWhere('assigned_to', $user->id)
                    ->orWhere('created_by', $user->id)
            );
        }

        if ($v = $request->integer('status_id'))   $query->where('status_id', $v);
        if ($v = $request->integer('priority_id')) $query->where('priority_id', $v);
        if ($v = $request->integer('assigned_to')) $query->where('assigned_to', $v);
        if ($v = $request->integer('project_id'))  $query->where('project_id', $v);
        if ($v = $request->integer('category_id')) $query->where('category_id', $v);
        if ($v = $request->integer('type_id'))     $query->where('type_id', $v);
        if ($v = $request->string('search')->toString()) {
            $query->where(
                fn($q) =>
                $q->where('title', 'like', "%{$v}%")
                    ->orWhere('ticket_number', 'like', "%{$v}%")
            );
        }

        $tickets = $query->orderBy('created_at', 'desc')->get();

        return response()->json($tickets->map(fn(Ticket $t) => $this->serializeTicket($t)));
    }

    public function showTicket(Ticket $ticket): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        abort_if($ticket->company_id !== $user->company_id, 403, 'No tienes acceso a este ticket.');

        if ($user->isClient() && $ticket->created_by !== $user->id) {
            abort(403, 'Solo puedes ver tus propios tickets.');
        }

        $ticket->load($this->ticketService->defaultRelations());

        return response()->json($this->serializeTicket($ticket, withThread: true));
    }

    public function storeTicket(StoreTicketRequest $request): JsonResponse
    {
        $ticket = $this->ticketService->create($request->validated(), $request->user());

        return response()->json($this->serializeTicket($ticket, withThread: true), 201);
    }

    public function updateTicket(UpdateTicketRequest $request, Ticket $ticket): JsonResponse
    {
        $updated = $this->ticketService->update($ticket, $request->validated(), $request->user());

        return response()->json($this->serializeTicket($updated, withThread: true));
    }

    public function moveTicket(MoveTicketRequest $request, Ticket $ticket): JsonResponse
    {
        $status  = TicketStatus::findOrFail($request->validated('status_id'));
        $updated = $this->ticketService->move($ticket, $status, $request->user());

        return response()->json($this->serializeTicket($updated));
    }

    public function destroyTicket(Ticket $ticket): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        abort_if(
            ! in_array($user->role, ['super_admin', 'admin']) || $ticket->company_id !== $user->company_id,
            403,
            'Solo los administradores pueden eliminar tickets.'
        );

        $ticket->delete();

        return response()->json(['message' => 'Ticket eliminado']);
    }

    // ─── Mensajes ───────────────────────────────────────────────────────────────

    public function storeMessage(StoreMessageRequest $request, Ticket $ticket): JsonResponse
    {
        $this->ticketService->addMessage(
            $ticket,
            $request->validated('message'),
            $request->boolean('is_internal'),
            $request->user()
        );

        $message = $ticket->messages()->with('user')->latest()->first();

        // ✅ Broadcast al canal privado del ticket.
        //
        // ->toOthers() excluye al remitente del broadcast — él ya ve el mensaje
        // porque lo acaba de enviar (la respuesta HTTP lo devuelve).
        // El resto de usuarios suscritos al canal lo recibirán en tiempo real.
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'id'          => $message->id,
            'message'     => $message->message,
            'is_internal' => $message->is_internal,
            'created_at'  => $message->created_at->toIso8601String(),
            'user'        => $message->user
                ? ['id' => $message->user->id, 'name' => $message->user->name]
                : null,
        ], 201);
    }

    // ─── Catálogos ───────────────────────────────────────────────────────────────

    public function searchAgents(Request $request): JsonResponse
    {
        $agents = User::where('company_id', Auth::user()->company_id)
            ->whereIn('role', ['agent', 'admin'])
            ->where('name', 'like', '%' . $request->string('q') . '%')
            ->limit(10)
            ->get(['id', 'name', 'email', 'role']);

        return response()->json($agents);
    }

    // ─── Serialización centralizada ──────────────────────────────────────────────

    private function serializeTicket(Ticket $ticket, bool $withThread = false): array
    {
        $data = [
            'id'               => $ticket->id,
            'ticket_number'    => $ticket->ticket_number,
            'title'            => $ticket->title,
            'description'      => $ticket->description,
            'status_id'        => $ticket->status_id,
            'priority_id'      => $ticket->priority_id,
            'type_id'          => $ticket->type_id,
            'category_id'      => $ticket->category_id,
            'project_id'       => $ticket->project_id,
            'helpdesk_id'      => $ticket->helpdesk_id,
            'assigned_to'      => $ticket->assigned_to,
            'created_by'       => $ticket->created_by,
            'due_date'         => $ticket->due_date?->toDateString(),
            'closed_at'        => $ticket->closed_at?->toIso8601String(),
            'created_at'       => $ticket->created_at->toIso8601String(),
            'updated_at'       => $ticket->updated_at->toIso8601String(),
            'status'           => $ticket->relationLoaded('status') && $ticket->status ? [
                'id' => $ticket->status->id,
                'name' => $ticket->status->name,
                'color' => $ticket->status->color,
                'order' => $ticket->status->order,
            ] : null,
            'priority'         => $ticket->relationLoaded('priority') && $ticket->priority ? [
                'id' => $ticket->priority->id,
                'name' => $ticket->priority->name,
                'color' => $ticket->priority->color,
                'level' => $ticket->priority->level,
            ] : null,
            'type'             => $ticket->relationLoaded('type') && $ticket->type
                ? ['id' => $ticket->type->id, 'name' => $ticket->type->name] : null,
            'category'         => $ticket->relationLoaded('category') && $ticket->category
                ? ['id' => $ticket->category->id, 'name' => $ticket->category->name] : null,
            'assigned_user'    => $ticket->relationLoaded('assignedTo') && $ticket->assignedTo ? [
                'id' => $ticket->assignedTo->id,
                'name' => $ticket->assignedTo->name,
            ] : null,
            'created_user'     => $ticket->relationLoaded('createdBy') && $ticket->createdBy ? [
                'id' => $ticket->createdBy->id,
                'name' => $ticket->createdBy->name,
            ] : null,
            'messages_count'   => $ticket->messages_count ?? ($ticket->relationLoaded('messages') ? $ticket->messages->count() : 0),
            'activities_count' => $ticket->activities_count ?? ($ticket->relationLoaded('activities') ? $ticket->activities->count() : 0),
        ];

        if ($withThread) {
            $data['messages'] = $ticket->relationLoaded('messages')
                ? $ticket->messages->map(fn($m) => [
                    'id'          => $m->id,
                    'message'     => $m->message,
                    'is_internal' => $m->is_internal,
                    'created_at'  => $m->created_at->toIso8601String(),
                    'user'        => $m->relationLoaded('user') && $m->user
                        ? ['id' => $m->user->id, 'name' => $m->user->name] : null,
                ])->values()->all()
                : [];

            $data['activities'] = $ticket->relationLoaded('activities')
                ? $ticket->activities->map(fn($a) => [
                    'id'          => $a->id,
                    'action'      => $a->action,
                    'description' => $a->description,
                    'created_at'  => $a->created_at->toIso8601String(),
                    'user'        => $a->relationLoaded('user') && $a->user
                        ? ['id' => $a->user->id, 'name' => $a->user->name] : null,
                ])->values()->all()
                : [];
        }

        return $data;
    }
}

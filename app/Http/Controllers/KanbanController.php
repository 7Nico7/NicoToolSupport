<?php
// app/Http/Controllers/KanbanController.php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Requests\MoveTicketRequest;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Company;
use App\Models\Helpdesk;
use App\Models\MessageAttachment;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketCategory;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketType;
use App\Models\User;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

if (! defined('KANBAN_ALLOWED_MIMES')) {
    define('KANBAN_ALLOWED_MIMES', [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'application/pdf',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/msword',
        'text/csv',
        'application/zip',
        'application/x-zip-compressed',
    ]);
}

class KanbanController extends Controller
{
    public function __construct(private readonly TicketService $ticketService) {}

    // ─── Vista principal ────────────────────────────────────────────────────────

    public function index(): Response
    {
        /** @var User $user */
        $user         = Auth::user();
        $companyId    = $user->company_id;
        $company      = $user->company;
        $isSuperAdmin = $user->isSuperAdmin();

        return Inertia::render('Kanban/Index', [
            'statuses'   => TicketStatus::orderBy('order')->get(['id', 'name', 'color', 'order']),
            'priorities' => TicketPriority::orderBy('level')->get(['id', 'name', 'color', 'level']),
            'types'      => TicketType::all(['id', 'name']),
            'company'    => $company,
            'categories'   => TicketCategory::where('company_id', $companyId)->get(['id', 'name']),
            'projects'     => Project::where('company_id', $companyId)->where('is_active', true)->get(['id', 'name']),
            'helpdesks'    => Helpdesk::where('company_id', $companyId)->get(['id', 'name']),
            // super_admin: catálogos globales con company_id para filtrar en los modales client-side
            'allProjects'  => $isSuperAdmin
                ? Project::where('is_active', true)->orderBy('name')->get(['id', 'name', 'company_id'])
                : [],
            'allHelpdesks' => $isSuperAdmin
                ? Helpdesk::orderBy('name')->get(['id', 'name', 'company_id'])
                : [],
            'allCategories' => $isSuperAdmin
                ? TicketCategory::orderBy('name')->get(['id', 'name', 'company_id'])
                : [],
            'agents'     => User::where('company_id', $companyId)
                ->whereIn('role', ['agent', 'admin'])
                ->get(['id', 'name', 'role']),
            // Solo super_admin recibe el listado de compañías para el filtro
            'companies'    => $isSuperAdmin
                ? Company::orderBy('name')->get(['id', 'name'])
                : [],
            'isSuperAdmin' => $isSuperAdmin,
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
            ->with(['status', 'priority', 'type', 'category', 'assignedTo', 'createdBy'])
            ->withCount(['messages', 'activities']);

        // ── Scope por rol ──────────────────────────────────────────────────────
        if ($user->isSuperAdmin()) {
            // super_admin ve tickets de todas las compañías.
            // El filtro company_id (abajo) lo acota si el usuario lo selecciona.
        } elseif ($user->isClient()) {
            $query->where('company_id', $user->company_id)
                ->where('created_by', $user->id);
        } elseif ($user->isAgent()) {
            $agentHelpdeskIds = Helpdesk::where('company_id', $user->company_id)
                ->whereHas('users', fn($q) => $q->where('users.id', $user->id))
                ->pluck('id');

            $query->where('company_id', $user->company_id)
                ->where(
                    fn($q) =>
                    $q->whereIn('helpdesk_id', $agentHelpdeskIds)
                        ->orWhere('assigned_to', $user->id)
                        ->orWhere('created_by', $user->id)
                );
        } else {
            // admin → todos los tickets de su compañía
            $query->where('company_id', $user->company_id);
        }

        if ($v = $request->integer('status_id'))   $query->where('status_id', $v);
        if ($v = $request->integer('priority_id')) $query->where('priority_id', $v);
        if ($v = $request->integer('assigned_to')) $query->where('assigned_to', $v);
        if ($v = $request->integer('project_id'))  $query->where('project_id', $v);
        if ($v = $request->integer('category_id')) $query->where('category_id', $v);
        if ($v = $request->integer('type_id'))     $query->where('type_id', $v);
        // company_id solo aplica para super_admin — cambia el scope de la query
        if ($user->isSuperAdmin() && ($v = $request->integer('company_id'))) {
            $query->where('company_id', $v);
        }
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
        // super_admin puede ver tickets de cualquier compañía
        if (! $user->isSuperAdmin()) {
            abort_if($ticket->company_id !== $user->company_id, 403, 'No tienes acceso a este ticket.');
        }

        if ($user->isClient() && $ticket->created_by !== $user->id) {
            abort(403, 'Solo puedes ver tus propios tickets.');
        }

        $ticket->load([
            ...$this->ticketService->defaultRelations(),
            'attachments.user',
            'messages.attachments.user',
        ]);

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
            ! in_array($user->role, ['super_admin', 'admin']),
            403,
            'Solo los administradores pueden eliminar tickets.'
        );

        // admin solo puede eliminar tickets de su compañía; super_admin cualquiera
        if (! $user->isSuperAdmin()) {
            abort_if($ticket->company_id !== $user->company_id, 403);
        }


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

        $serializedAttachments = [];

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $mimeType = $file->getMimeType();
                if (! in_array($mimeType, KANBAN_ALLOWED_MIMES)) continue;

                $path = $file->storeAs(
                    "messages/{$message->id}",
                    Str::uuid() . '.' . $file->getClientOriginalExtension(),
                    'local'
                );

                $att = MessageAttachment::create([
                    'ticket_message_id' => $message->id,
                    'user_id'           => $request->user()->id,
                    'filename'          => $file->getClientOriginalName(),
                    'path'              => $path,
                    'disk'              => 'local',
                    'mime_type'         => $mimeType,
                    'size'              => $file->getSize(),
                ]);

                $att->load('user');

                $serializedAttachments[] = [
                    'id'            => $att->id,
                    'filename'      => $att->filename,
                    'mime_type'     => $att->mime_type,
                    'file_category' => $att->fileCategory(),
                    'readable_size' => $att->readableSize(),
                    'size'          => $att->size,
                    'download_url'  => $att->downloadUrl(),
                    'created_at'    => $att->created_at->toIso8601String(),
                    'user'          => $att->user
                        ? ['id' => $att->user->id, 'name' => $att->user->name] : null,
                ];
            }
        }

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'id'          => $message->id,
            'message'     => $message->message,
            'is_internal' => $message->is_internal,
            'created_at'  => $message->created_at->toIso8601String(),
            'user'        => $message->user
                ? ['id' => $message->user->id, 'name' => $message->user->name] : null,
            'attachments' => $serializedAttachments,
        ], 201);
    }

    // ─── Catálogos ───────────────────────────────────────────────────────────────

    public function searchAgents(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        // super_admin puede buscar agentes de cualquier compañía.
        // El frontend pasa company_id cuando hay una compañía seleccionada en el modal.
        $companyId = $user->isSuperAdmin() && $request->filled('company_id')
            ? $request->integer('company_id')
            : $user->company_id;

        $agents = User::where('company_id', $companyId)
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
            'messages_count'   => $ticket->messages_count
                ?? ($ticket->relationLoaded('messages') ? $ticket->messages->count() : 0),
            'activities_count' => $ticket->activities_count
                ?? ($ticket->relationLoaded('activities') ? $ticket->activities->count() : 0),
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
                    'attachments' => $m->relationLoaded('attachments')
                        ? $m->attachments->map(fn($a) => [
                            'id'            => $a->id,
                            'filename'      => $a->filename,
                            'mime_type'     => $a->mime_type,
                            'file_category' => $a->fileCategory(),
                            'readable_size' => $a->readableSize(),
                            'size'          => $a->size,
                            'download_url'  => $a->downloadUrl(),
                            'created_at'    => $a->created_at->toIso8601String(),
                            'user'          => $a->relationLoaded('user') && $a->user
                                ? ['id' => $a->user->id, 'name' => $a->user->name] : null,
                        ])->values()->all()
                        : [],
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

            $data['attachments'] = $ticket->relationLoaded('attachments')
                ? $ticket->attachments->map(fn($a) => [
                    'id'            => $a->id,
                    'filename'      => $a->filename,
                    'mime_type'     => $a->mime_type,
                    'file_category' => $a->fileCategory(),
                    'readable_size' => $a->readableSize(),
                    'size'          => $a->size,
                    'description'   => $a->description,
                    'download_url'  => $a->downloadUrl(),
                    'created_at'    => $a->created_at->toIso8601String(),
                    'user'          => $a->relationLoaded('user') && $a->user
                        ? ['id' => $a->user->id, 'name' => $a->user->name] : null,
                ])->values()->all()
                : [];
        }

        return $data;
    }
}

<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Ticket;
use App\Models\User;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});



Broadcast::channel('ticket.{ticketId}', function (User $user, int $ticketId): bool|array {
    $ticket = Ticket::find($ticketId);

    if (! $ticket) {
        return false;
    }

    // Compañía diferente → acceso denegado siempre
    if ($ticket->company_id !== $user->company_id) {
        return false;
    }

    // Cliente → solo sus propios tickets
    if ($user->isClient()) {
        return $ticket->created_by === $user->id;
    }

    // Agente → mismo scope que indexTickets
    if ($user->isAgent()) {
        $agentHelpdeskIds = \App\Models\Helpdesk::where('company_id', $user->company_id)
            ->whereHas('users', fn ($q) => $q->where('users.id', $user->id))
            ->pluck('id');

        return $agentHelpdeskIds->contains($ticket->helpdesk_id)
            || $ticket->assigned_to === $user->id
            || $ticket->created_by  === $user->id;
    }

    // admin / super_admin → misma compañía (ya validado arriba)
    return true;
});

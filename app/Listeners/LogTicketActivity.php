<?php
// app/Listeners/LogTicketActivity.php

namespace App\Listeners;

use App\Events\TicketActivityLogged;
use App\Events\TicketMoved;

class LogTicketActivity
{
    /**
     * Escucha TicketActivityLogged y persiste en la tabla ticket_activities.
     */
    public function handleActivityLogged(TicketActivityLogged $event): void
    {
        $event->ticket->activities()->create([
            'user_id'     => $event->user->id,
            'action'      => $event->action,
            'description' => $event->description,
        ]);
    }

    /**
     * Escucha TicketMoved y persiste el cambio de estado.
     */
    public function handleTicketMoved(TicketMoved $event): void
    {
        $from = $event->fromStatus?->name ?? 'Sin estado';
        $to   = $event->toStatus->name;

        $event->ticket->activities()->create([
            'user_id'     => $event->movedBy->id,
            'action'      => 'moved',
            'description' => "Movido de \"{$from}\" a \"{$to}\"",
        ]);
    }
}

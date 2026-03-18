<?php
// app/Events/TicketMoved.php

namespace App\Events;

use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketMoved
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Ticket $ticket,
        public readonly ?TicketStatus $fromStatus,
        public readonly TicketStatus $toStatus,
        public readonly User $movedBy,
    ) {}
}

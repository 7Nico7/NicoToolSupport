<?php
// app/Events/TicketActivityLogged.php

namespace App\Events;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketActivityLogged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Ticket $ticket,
        public readonly User $user,
        public readonly string $action,
        public readonly string $description,
    ) {}
}

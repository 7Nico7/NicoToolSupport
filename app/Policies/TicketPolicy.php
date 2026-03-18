<?php
// app/Policies/TicketPolicy.php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    /**
     * Los admins pueden hacer todo dentro de su compañía.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return null;
    }

    /**
     * Ver tickets: agentes ven todos los de su compañía;
     * clientes solo ven los que crearon.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        if ($ticket->company_id !== $user->company_id) {
            return false;
        }

        if ($user->isAgent()) {
            return true;
        }

        // Cliente solo ve sus propios tickets
        return $ticket->created_by === $user->id;
    }

    /**
     * Crear tickets: cualquier usuario de la compañía puede crearlos.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Actualizar tickets: agentes pueden actualizar los de su compañía;
     * clientes solo pueden actualizar los que crearon (y solo campos permitidos).
     */
    public function update(User $user, Ticket $ticket): bool
    {
        if ($ticket->company_id !== $user->company_id) {
            return false;
        }

        if ($user->isAgent()) {
            return true;
        }

        return $ticket->created_by === $user->id;
    }

    /**
     * Mover columnas en el Kanban: solo agentes y admins.
     */
    public function move(User $user, Ticket $ticket): bool
    {
        return $user->isAgent() && $ticket->company_id === $user->company_id;
    }

    /**
     * Eliminar: solo admins (manejado en before()).
     */
    public function delete(User $user, Ticket $ticket): bool
    {
        return false; // Solo admins — cubierto por before()
    }
}

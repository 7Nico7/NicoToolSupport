<?php
// app/Http/Requests/MoveTicketRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MoveTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ticket = $this->route('ticket');
        $user   = $this->user();

        // super_admin puede mover tickets de cualquier compañía
        if ($user->isSuperAdmin()) return true;

        return $ticket->company_id === $user->company_id
            && in_array($user->role, ['agent', 'admin']);
    }

    public function rules(): array
    {
        return [
            'status_id' => ['required', 'exists:ticket_statuses,id'],
        ];
    }
}

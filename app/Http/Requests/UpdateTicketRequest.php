<?php
// app/Http/Requests/UpdateTicketRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ticket = $this->route('ticket');
        $user   = $this->user();

        // Verificación manual: mismo company_id
        if ($ticket->company_id !== $user->company_id) {
            return false;
        }

        // Clientes solo pueden actualizar sus propios tickets
        if ($user->isClient() && $ticket->created_by !== $user->id) {
            return false;
        }

        return true;
    }

    public function rules(): array
    {
        $companyId = $this->user()->company_id;

        return [
            'title'       => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'status_id'   => ['sometimes', 'exists:ticket_statuses,id'],
            'priority_id' => ['sometimes', 'nullable', 'exists:ticket_priorities,id'],
            'type_id'     => ['sometimes', 'nullable', 'exists:ticket_types,id'],
            'category_id' => ['sometimes', 'nullable', "exists:ticket_categories,id,company_id,{$companyId}"],
            'project_id'  => ['sometimes', 'nullable', "exists:projects,id,company_id,{$companyId}"],
            'helpdesk_id' => ['sometimes', 'nullable', "exists:helpdesks,id,company_id,{$companyId}"],
            'assigned_to' => ['sometimes', 'nullable', "exists:users,id,company_id,{$companyId}"],
            'due_date'    => ['sometimes', 'nullable', 'date'],
        ];
    }
}

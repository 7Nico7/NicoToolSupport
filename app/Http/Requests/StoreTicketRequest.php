<?php
// app/Http/Requests/StoreTicketRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        // El middleware 'auth' ya garantiza que hay sesión activa.
        // La restricción de company_id se aplica en el Service.
        return true;
    }

    public function rules(): array
    {
        $companyId = $this->user()->company_id;

        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status_id'   => ['required', 'exists:ticket_statuses,id'],
            'priority_id' => ['nullable', 'exists:ticket_priorities,id'],
            'type_id'     => ['nullable', 'exists:ticket_types,id'],
            'category_id' => ['nullable', "exists:ticket_categories,id,company_id,{$companyId}"],
            'project_id'  => ['nullable', "exists:projects,id,company_id,{$companyId}"],
            'helpdesk_id' => ['nullable', "exists:helpdesks,id,company_id,{$companyId}"],
            'assigned_to' => ['nullable', "exists:users,id,company_id,{$companyId}"],
            'due_date'    => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'    => 'El título del ticket es obligatorio.',
            'status_id.required'=> 'Debes seleccionar un estado.',
            'due_date.after'    => 'La fecha de vencimiento debe ser futura.',
        ];
    }
}

<?php
// app/Http/Requests/StoreMessageRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ticket = $this->route('ticket');
        $user   = $this->user();

        if ($ticket->company_id !== $user->company_id) {
            return false;
        }

        // Clientes solo pueden responder sus propios tickets
        if ($user->isClient() && $ticket->created_by !== $user->id) {
            return false;
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'message'     => ['required', 'string', 'max:5000'],
            'is_internal' => ['boolean'],
        ];
    }
}

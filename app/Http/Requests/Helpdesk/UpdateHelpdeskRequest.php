<?php
// app/Http/Requests/Helpdesk/UpdateHelpdeskRequest.php

namespace App\Http\Requests\Helpdesk;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHelpdeskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // super_admin puede cambiar la compañía del helpdesk desde el form.
        // Los demás roles usan siempre su propia compañía.
        $companyId = $this->user()->isSuperAdmin()
            ? ($this->integer('company_id') ?: $this->route('helpdesk')?->company_id)
            : $this->user()->company_id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('helpdesks')
                    ->where('company_id', $companyId)
                    ->ignore($this->route('helpdesk')?->id),
            ],
            'agent_ids'   => ['nullable', 'array'],
            'agent_ids.*' => [
                'integer',
                Rule::exists('users', 'id')
                    ->where('company_id', $companyId)
                    ->whereIn('role', ['agent', 'admin']),
            ],
            'company_id' => [
                'nullable',
                'exists:companies,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'El nombre del helpdesk es obligatorio.',
            'name.unique'        => 'Ya existe un helpdesk con este nombre en esta compañía.',
            'name.max'           => 'El nombre no puede superar los 255 caracteres.',
            'agent_ids.*.exists' => 'Uno o más agentes seleccionados no son válidos.',
        ];
    }
}

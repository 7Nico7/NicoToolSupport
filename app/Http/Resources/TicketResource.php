<?php
// app/Http/Resources/TicketResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'ticket_number' => $this->ticket_number,
            'title'         => $this->title,
            'description'   => $this->description,
            'status_id'     => $this->status_id,
            'priority_id'   => $this->priority_id,
            'type_id'       => $this->type_id,
            'category_id'   => $this->category_id,
            'project_id'    => $this->project_id,
            'helpdesk_id'   => $this->helpdesk_id,
            'assigned_to'   => $this->assigned_to,
            'created_by'    => $this->created_by,
            'due_date'      => $this->due_date?->toDateString(),
            'closed_at'     => $this->closed_at?->toIso8601String(),
            'created_at'    => $this->created_at->toIso8601String(),
            'updated_at'    => $this->updated_at->toIso8601String(),

            // Relaciones (solo si están cargadas)
            'status'        => new TicketStatusResource($this->whenLoaded('status')),
            'priority'      => $this->whenLoaded('priority', fn() => [
                'id'    => $this->priority->id,
                'name'  => $this->priority->name,
                'level' => $this->priority->level,
                'color' => $this->priority->color,
            ]),
            'type'          => $this->whenLoaded('type', fn() => [
                'id'   => $this->type->id,
                'name' => $this->type->name,
            ]),
            'category'      => $this->whenLoaded('category', fn() => [
                'id'   => $this->category->id,
                'name' => $this->category->name,
            ]),
            'project'       => $this->whenLoaded('project', fn() => [
                'id'   => $this->project->id,
                'name' => $this->project->name,
            ]),
            'helpdesk'      => $this->whenLoaded('helpdesk', fn() => [
                'id'   => $this->helpdesk->id,
                'name' => $this->helpdesk->name,
            ]),
            'assigned_user' => $this->whenLoaded('assignedTo', fn() => [
                'id'   => $this->assignedTo->id,
                'name' => $this->assignedTo->name,
            ]),
            'created_user'  => $this->whenLoaded('createdBy', fn() => [
                'id'   => $this->createdBy->id,
                'name' => $this->createdBy->name,
            ]),
            'messages'      => TicketMessageResource::collection($this->whenLoaded('messages')),
            'activities'    => TicketActivityResource::collection($this->whenLoaded('activities')),

            // Conteos útiles para la tarjeta del Kanban
            'messages_count'  => $this->whenCounted('messages'),
            'activities_count'=> $this->whenCounted('activities'),
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'project_id',
        'ticket_number',
        'title',
        'description',
        'status_id',
        'priority_id',
        'type_id',
        'category_id',
        'created_by',
        'assigned_to',
        'helpdesk_id',
        'due_date',
        'closed_at',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'closed_at' => 'datetime',
    ];

    // Relaciones
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function status()
    {
        return $this->belongsTo(TicketStatus::class, 'status_id');
    }

    public function priority()
    {
        return $this->belongsTo(TicketPriority::class, 'priority_id');
    }

    public function type()
    {
        return $this->belongsTo(TicketType::class, 'type_id');
    }

    public function category()
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function helpdesk()
    {
        return $this->belongsTo(Helpdesk::class);
    }

    public function activities()
    {
        return $this->hasMany(TicketActivity::class);
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }

        public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class)->orderBy('created_at', 'asc');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relaciones
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function helpdesks()
    {
        return $this->hasMany(Helpdesk::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function ticketCategories()
    {
        return $this->hasMany(TicketCategory::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}

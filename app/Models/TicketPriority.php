<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketPriority extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'level',
        'color',
    ];

    protected $casts = [
        'level' => 'integer',
    ];

    // Relaciones
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'priority_id');
    }
}

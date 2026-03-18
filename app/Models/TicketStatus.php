<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    // Relaciones
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'status_id');
    }
}

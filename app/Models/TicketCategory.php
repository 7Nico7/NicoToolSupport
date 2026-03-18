<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
    ];

    // Relaciones
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'category_id');
    }
}

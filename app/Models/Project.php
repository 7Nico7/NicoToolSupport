<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'company_id',
        'created_by',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relaciones
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    /**
     * Los atributos que deben ocultarse para la serialización.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Relación: Un usuario pertenece a una compañía.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Verificar si el usuario es administrador.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Verificar si el usuario es agente.
     */
    public function isAgent(): bool
    {
        return $this->role === 'agent';
    }

    /**
     * Verificar si el usuario es cliente.
     */
    public function isClient(): bool
    {
        return $this->role === 'client';
    }

        public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }
}

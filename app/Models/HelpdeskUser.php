<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HelpdeskUser extends Model
{
    protected $table = 'helpdesk_user';

    protected $fillable = [
        'helpdesk_id',
        'user_id',
    ];

    /**
     * Relación con Helpdesk
     */
    public function helpdesk(): BelongsTo
    {
        return $this->belongsTo(Helpdesk::class, 'helpdesk_id');
    }

    /**
     * Relación con User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

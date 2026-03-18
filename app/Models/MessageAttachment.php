<?php
// app/Models/MessageAttachment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class MessageAttachment extends Model
{
    protected $fillable = [
        'ticket_message_id',
        'user_id',
        'filename',
        'path',
        'disk',
        'mime_type',
        'size',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function message(): BelongsTo
    {
        return $this->belongsTo(TicketMessage::class, 'ticket_message_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * URL de descarga segura con expiración.
     * Misma lógica que TicketAttachment — disco local usa ruta firmada,
     * cloud usa temporaryUrl() nativo del driver.
     */
    public function downloadUrl(int $minutes = 5): string
    {
        if ($this->disk === 'local') {
            return URL::temporarySignedRoute(
                'attachments.message.download',
                now()->addMinutes($minutes),
                ['attachment' => $this->id]
            );
        }

            if (method_exists($this->disk, 'temporaryUrl')) {
        return $this->disk->temporaryUrl($this->path, now()->addMinutes($minutes));
    }

    return $this->disk->url($this->path);
  //return Storage::disk($this->disk)->url($this->path);
        /** @var \Illuminate\Contracts\Filesystem\Cloud $cloudDisk */
        $cloudDisk = Storage::disk($this->disk);
       // return $cloudDisk->temporaryUrl($this->path, now()->addMinutes($minutes));

    }

    /**
     * Tamaño legible: "2.4 MB", "340 KB", "800 B".
     */
    public function readableSize(): string
    {
        return match (true) {
            $this->size >= 1_048_576 => round($this->size / 1_048_576, 1) . ' MB',
            $this->size >= 1_024     => round($this->size / 1_024,     1) . ' KB',
            default                  => $this->size . ' B',
        };
    }

    /**
     * Categoría del archivo para el ícono en el frontend.
     */
    public function fileCategory(): string
    {
        return match (true) {
            str_starts_with($this->mime_type, 'image/')         => 'image',
            $this->mime_type === 'application/pdf'               => 'pdf',
            str_contains($this->mime_type, 'spreadsheet'),
            str_contains($this->mime_type, 'excel')              => 'spreadsheet',
            str_contains($this->mime_type, 'word'),
            str_contains($this->mime_type, 'document')           => 'document',
            default                                              => 'file',
        };
    }
}

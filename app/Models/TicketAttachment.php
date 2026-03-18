<?php
// app/Models/TicketAttachment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class TicketAttachment extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'filename',
        'path',
        'disk',
        'mime_type',
        'size',
        'description',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * URL de descarga segura con expiración.
     *
     * — Disco local: URL firmada de Laravel que apunta a una ruta de descarga.
     *   El archivo nunca es accesible directamente, pasa por el controlador
     *   que valida la firma y sirve el stream.
     *
     * — Disco S3 (o cualquier cloud): temporaryUrl() nativo del SDK.
     *   El SDK genera la URL firmada directamente contra el bucket.
     */
    public function downloadUrl(int $minutes = 5): string
    {
        if ($this->disk === 'local') {
            // Ruta firmada: /attachments/ticket/{id}/download?signature=...
            // La ruta debe existir en web.php (ver routes/web.php incluido)
            return URL::temporarySignedRoute(
                'attachments.ticket.download',
                now()->addMinutes($minutes),
                ['attachment' => $this->id]
            );
        }

                    if (method_exists($this->disk, 'temporaryUrl')) {
        return $this->disk->temporaryUrl($this->path, now()->addMinutes($minutes));
    }

    return $this->disk->url($this->path);

        // S3 / GCS / cualquier cloud driver con soporte nativo.
        // @var Cloud  — Storage::disk() retorna la interfaz Filesystem que no declara
        // temporaryUrl(). El cast al contrato Cloud expone el método al IDE sin
        // cambiar el comportamiento en runtime.
        /** @var \Illuminate\Contracts\Filesystem\Cloud $cloudDisk */
        $cloudDisk = Storage::disk($this->disk);
      //  return $cloudDisk->temporaryUrl($this->path, now()->addMinutes($minutes));
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
     * Retorna: 'image' | 'pdf' | 'spreadsheet' | 'document' | 'file'
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

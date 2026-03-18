<?php
// app/Http/Controllers/AttachmentController.php

namespace App\Http\Controllers;

use App\Models\MessageAttachment;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

// Tipos MIME permitidos — lista blanca estricta
// Nunca permitir ejecutables: .php, .js, .exe, .sh, etc.
const ALLOWED_MIMES = [
    'image/jpeg', 'image/png', 'image/gif', 'image/webp',
    'application/pdf',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
    'application/vnd.ms-excel',                                           // .xls
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
    'application/msword',                                                 // .doc
    'text/csv',
    'application/zip',
    'application/x-zip-compressed',
];

const MAX_FILE_BYTES = 10 * 1024 * 1024; // 10 MB por archivo

class AttachmentController extends Controller
{
    // ─── Subida: evidencia del ticket ────────────────────────────────────────

    public function storeTicketAttachment(Request $request, Ticket $ticket): JsonResponse
    {
        $user = $request->user();

        abort_if($ticket->company_id !== $user->company_id, 403, 'Sin acceso.');

        $request->validate([
            'file'        => ['required', 'file', 'max:' . (MAX_FILE_BYTES / 1024)],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $file     = $request->file('file');
        $mimeType = $file->getMimeType();

        abort_if(! in_array($mimeType, ALLOWED_MIMES), 422, 'Tipo de archivo no permitido.');

        // Nombre aleatorio — nunca usar el nombre original como path
        $path = $file->storeAs(
            "tickets/{$ticket->id}",
            Str::uuid() . '.' . $file->getClientOriginalExtension(),
            'local'
        );

        $attachment = TicketAttachment::create([
            'ticket_id'   => $ticket->id,
            'user_id'     => $user->id,
            'filename'    => $file->getClientOriginalName(),
            'path'        => $path,
            'disk'        => 'local',
            'mime_type'   => $mimeType,
            'size'        => $file->getSize(),
            'description' => $request->input('description'),
        ]);

        $attachment->load('user');

        return response()->json($this->serializeTicketAttachment($attachment), 201);
    }

    // ─── Eliminar: evidencia del ticket ──────────────────────────────────────

    public function destroyTicketAttachment(Request $request, TicketAttachment $attachment): JsonResponse
    {
        $user = $request->user();

        $attachment->loadMissing('ticket');

        abort_if($attachment->ticket->company_id !== $user->company_id, 403, 'Sin acceso.');

        // Solo admin, super_admin o el dueño del archivo pueden eliminarlo
        $canDelete = in_array($user->role, ['admin', 'super_admin'])
            || $attachment->user_id === $user->id;

        abort_if(! $canDelete, 403, 'No tienes permiso para eliminar este archivo.');

        Storage::disk($attachment->disk)->delete($attachment->path);
        $attachment->delete();

        return response()->json(['message' => 'Archivo eliminado.']);
    }

    // ─── Subida: adjunto de mensaje ───────────────────────────────────────────

    public function storeMessageAttachment(Request $request, TicketMessage $message): JsonResponse
    {
        $user = $request->user();

        $message->loadMissing('ticket');
        abort_if($message->ticket->company_id !== $user->company_id, 403, 'Sin acceso.');

        $request->validate([
            'files'   => ['required', 'array', 'max:5'],
            'files.*' => ['required', 'file', 'max:' . (MAX_FILE_BYTES / 1024)],
        ]);

        $attachments = [];

        foreach ($request->file('files') as $file) {
            $mimeType = $file->getMimeType();
            if (! in_array($mimeType, ALLOWED_MIMES)) continue; // saltar tipos no permitidos

            $path = $file->storeAs(
                "messages/{$message->id}",
                Str::uuid() . '.' . $file->getClientOriginalExtension(),
                'local'
            );

            $att = MessageAttachment::create([
                'ticket_message_id' => $message->id,
                'user_id'           => $user->id,
                'filename'          => $file->getClientOriginalName(),
                'path'              => $path,
                'disk'              => 'local',
                'mime_type'         => $mimeType,
                'size'              => $file->getSize(),
            ]);

            $att->load('user');
            $attachments[] = $this->serializeMessageAttachment($att);
        }

        return response()->json($attachments, 201);
    }

    // ─── Eliminar: adjunto de mensaje ─────────────────────────────────────────

    public function destroyMessageAttachment(Request $request, MessageAttachment $attachment): JsonResponse
    {
        $user = $request->user();

        $attachment->loadMissing('message.ticket');
        abort_if($attachment->message->ticket->company_id !== $user->company_id, 403, 'Sin acceso.');

        $canDelete = in_array($user->role, ['admin', 'super_admin'])
            || $attachment->user_id === $user->id;

        abort_if(! $canDelete, 403, 'No tienes permiso para eliminar este archivo.');

        Storage::disk($attachment->disk)->delete($attachment->path);
        $attachment->delete();

        return response()->json(['message' => 'Archivo eliminado.']);
    }

    // ─── Descarga local: evidencia del ticket ─────────────────────────────────
    // (ruta protegida por middleware 'signed' en web.php)

    public function downloadTicketAttachment(
        Request $request,
        TicketAttachment $attachment
    ): StreamedResponse {
        $attachment->loadMissing('ticket');

        abort_if(
            $attachment->ticket->company_id !== $request->user()->company_id,
            403, 'Sin acceso.'
        );

        abort_unless(
            Storage::disk($attachment->disk)->exists($attachment->path),
            404, 'Archivo no encontrado.'
        );

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk($attachment->disk);

        return $disk->download($attachment->path, $attachment->filename);
    }

    // ─── Descarga local: adjunto de mensaje ───────────────────────────────────

    public function downloadMessageAttachment(
        Request $request,
        MessageAttachment $attachment
    ): StreamedResponse {
        $attachment->loadMissing('message.ticket');

        abort_if(
            $attachment->message->ticket->company_id !== $request->user()->company_id,
            403, 'Sin acceso.'
        );

        abort_unless(
            Storage::disk($attachment->disk)->exists($attachment->path),
            404, 'Archivo no encontrado.'
        );

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk($attachment->disk);

        return $disk->download($attachment->path, $attachment->filename);
    }

    // ─── Serialización ────────────────────────────────────────────────────────

    private function serializeTicketAttachment(TicketAttachment $att): array
    {
        return [
            'id'           => $att->id,
            'filename'     => $att->filename,
            'mime_type'    => $att->mime_type,
            'file_category'=> $att->fileCategory(),
            'readable_size'=> $att->readableSize(),
            'size'         => $att->size,
            'description'  => $att->description,
            'download_url' => $att->downloadUrl(),
            'created_at'   => $att->created_at->toIso8601String(),
            'user'         => $att->user
                ? ['id' => $att->user->id, 'name' => $att->user->name]
                : null,
        ];
    }

    private function serializeMessageAttachment(MessageAttachment $att): array
    {
        return [
            'id'           => $att->id,
            'filename'     => $att->filename,
            'mime_type'    => $att->mime_type,
            'file_category'=> $att->fileCategory(),
            'readable_size'=> $att->readableSize(),
            'size'         => $att->size,
            'download_url' => $att->downloadUrl(),
            'created_at'   => $att->created_at->toIso8601String(),
            'user'         => $att->user
                ? ['id' => $att->user->id, 'name' => $att->user->name]
                : null,
        ];
    }
}

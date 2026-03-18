<?php
// routes/web.php  — añadir estas rutas al archivo existente
//
// Estas rutas solo son necesarias para el disco LOCAL.
// Con S3 el cliente descarga directo del bucket con la URL firmada del SDK.
//
// Protección en capas:
//   1. auth        → usuario autenticado
//   2. signed      → URL no manipulada y no expirada
//   3. Controlador → usuario pertenece a la misma compañía que el ticket

use App\Http\Controllers\AttachmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'signed'])->group(function () {

    // Descarga de evidencia directa del ticket
    Route::get(
        '/attachments/ticket/{attachment}/download',
        [AttachmentController::class, 'downloadTicketAttachment']
    )->name('attachments.ticket.download');

    // Descarga de evidencia de mensaje
    Route::get(
        '/attachments/message/{attachment}/download',
        [AttachmentController::class, 'downloadMessageAttachment']
    )->name('attachments.message.download');

});

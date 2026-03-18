<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\KanbanController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    // ── Vista Inertia ──────────────────────────────────────────────────────────
    Route::get('/kanban', [KanbanController::class, 'index'])->name('kanban');

    // ── API del módulo Kanban ──────────────────────────────────────────────────
    Route::prefix('api/kanban')->name('api.kanban.')->group(function () {

        // Tickets
        Route::get('tickets',                [KanbanController::class, 'indexTickets'])->name('tickets.index');
        Route::post('tickets',               [KanbanController::class, 'storeTicket'])->name('tickets.store');
        Route::get('tickets/{ticket}',       [KanbanController::class, 'showTicket'])->name('tickets.show');
        Route::put('tickets/{ticket}',       [KanbanController::class, 'updateTicket'])->name('tickets.update');
        Route::patch('tickets/{ticket}/move',[KanbanController::class, 'moveTicket'])->name('tickets.move');
        Route::delete('tickets/{ticket}',    [KanbanController::class, 'destroyTicket'])->name('tickets.destroy');

        // Mensajes
        Route::post('tickets/{ticket}/messages', [KanbanController::class, 'storeMessage'])->name('messages.store');

        // Lookup / catálogos
        Route::get('agents/search', [KanbanController::class, 'searchAgents'])->name('agents.search');



        // ── Adjuntos del ticket (evidencias directas) ← NUEVO ────────────────
        Route::post  ('tickets/{ticket}/attachments',      [AttachmentController::class, 'storeTicketAttachment']);
        Route::delete('ticket-attachments/{attachment}',   [AttachmentController::class, 'destroyTicketAttachment']);

        // ── Adjuntos de mensaje ← NUEVO ───────────────────────────────────────
        Route::post  ('messages/{message}/attachments',    [AttachmentController::class, 'storeMessageAttachment']);
        Route::delete('message-attachments/{attachment}',  [AttachmentController::class, 'destroyMessageAttachment']);
    });
});

/*
|--------------------------------------------------------------------------
| EventServiceProvider — registrar listeners
|--------------------------------------------------------------------------
| En app/Providers/EventServiceProvider.php agregar:
|
| protected $listen = [
|     \App\Events\TicketActivityLogged::class => [
|         [\App\Listeners\LogTicketActivity::class, 'handleActivityLogged'],
|     ],
|     \App\Events\TicketMoved::class => [
|         [\App\Listeners\LogTicketActivity::class, 'handleTicketMoved'],
|     ],
| ];
|--------------------------------------------------------------------------
*/

<?php
// routes/tickets.php
// Incluir en routes/web.php con: require __DIR__ . '/tickets.php';

use App\Http\Controllers\TicketChatController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    // Vista de chat: lista + ticket activo (Inertia)
    Route::get('/tickets', [TicketChatController::class, 'index'])
        ->name('tickets.chat');

    // Carga el thread completo de un ticket vía axios (partial reload del sidebar)
    Route::get('/api/tickets/{ticket}', [TicketChatController::class, 'show'])
        ->name('tickets.chat.show');
});

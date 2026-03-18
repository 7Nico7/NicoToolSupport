<?php

use App\Http\Controllers\KanbanController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
// ── Gantt ──────────────────────────────────────────────────────────────────────
Route::get('/gantt', [App\Http\Controllers\GanttController::class, 'index'])->name('gantt');

Route::prefix('api/gantt')->name('api.gantt.')->group(function () {
    Route::get('tickets', [App\Http\Controllers\GanttController::class, 'tickets'])->name('tickets');
});


});

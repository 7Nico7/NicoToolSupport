<?php
// routes/helpdesks.php
// Incluir en routes/web.php con: require __DIR__ . '/helpdesks.php';

use App\Http\Controllers\HelpdeskController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    Route::resource('helpdesks', HelpdeskController::class)
        ->except(['show']); // No necesitamos vista de detalle individual

});

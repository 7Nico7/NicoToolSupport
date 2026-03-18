<?php
// routes/web.php — agregar dentro del grupo auth existente
//
// Permisos por método:
//   index / show               → todos los roles autenticados (el controller filtra por rol)
//   create / store             → admin (el controller hace abort_if)
//   edit / update              → admin, agent (el controller hace abort_if client)
//   destroy (desactivar)       → admin (el controller hace abort_if)
//
// El ResourceController omite `show` porque el módulo no tiene vista de detalle
// (se edita directamente desde el listado).

use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    Route::resource('projects', ProjectController::class)
         ->except(['show']);     // sin vista de detalle individual

});

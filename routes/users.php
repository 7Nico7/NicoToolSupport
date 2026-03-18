<?php
// routes/web.php — agregar dentro del grupo auth existente
//
// Acceso por método:
//   index            → super_admin, admin, agent  (controller: abort_if client)
//   create / store   → super_admin, admin         (controller: abort_if agent/client)
//   edit / update    → super_admin, admin         (controller: abort_if agent/client)
//   destroy          → super_admin, admin         (controller: abort_if agent/client + no self)

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    Route::resource('users', UserController::class)
         ->except(['show']);

});

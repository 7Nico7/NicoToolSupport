<?php
// routes/web.php — agregar dentro del grupo auth existente
//
// Acceso por método:
//   index            → todos (super_admin, admin, agent, client)
//   create / store   → super_admin únicamente (controller: abort_if)
//   edit / update    → super_admin, admin, agent (controller: abort_if client)
//   destroy          → super_admin únicamente (controller: abort_if)

use App\Http\Controllers\CompanyController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    Route::resource('companies', CompanyController::class)
         ->except(['show']);     // sin vista de detalle individual

});

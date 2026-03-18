<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. CARGA DE RUTAS PÚBLICAS (Sitio web, Nosotros, Servicios, etc.)
// Esto manejará la ruta '/' automáticamente desde PublicController
require __DIR__.'/public.php';

// 2. DASHBOARD (Privado)
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 3. PERFIL Y RUTAS DE USUARIO AUTENTICADO
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Aquí puedes meter el resto de tus módulos protegidos
    require __DIR__.'/projects.php';
    require __DIR__.'/companies.php';
    require __DIR__.'/users.php';
    require __DIR__.'/kanban.php';
    require __DIR__.'/gantt.php';
    require __DIR__.'/attachment.php';
});

// 4. AUTENTICACIÓN (Login, Registro, Logout)
require __DIR__.'/auth.php';

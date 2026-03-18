<?php

use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

// ── SITIO PÚBLICO (Corporativo) ──────────────────────────────────────────────
// Se encarga de la cara externa de la empresa.
Route::name('public.')->group(function () {

    // Página de inicio (Portada)
    Route::get('/', [PublicController::class, 'home'])->name('home');

    // Empresa
    Route::prefix('nosotros')->group(function () {
        Route::get('/', [PublicController::class, 'nosotros'])->name('nosotros');
        Route::get('/mision-vision', [PublicController::class, 'misionVision'])->name('mision-vision');
        Route::get('/valores', [PublicController::class, 'valores'])->name('valores');
        Route::get('/equipo', [PublicController::class, 'equipo'])->name('equipo');
    });

    // Servicios
    Route::prefix('servicios')->group(function () {
        Route::get('/', [PublicController::class, 'servicios'])->name('servicios');
        Route::get('/desarrollo', [PublicController::class, 'desarrollo'])->name('servicios.desarrollo');
        Route::get('/movil', [PublicController::class, 'movil'])->name('servicios.movil');
        Route::get('/soporte', [PublicController::class, 'soporte'])->name('servicios.soporte');
        Route::get('/consultoria', [PublicController::class, 'consultoria'])->name('servicios.consultoria');
    });

    // Portafolio y Ejemplos
    Route::prefix('ejemplos')->group(function () {
        Route::get('/cotizaciones', [PublicController::class, 'cotizaciones'])->name('ejemplos.cotizaciones');
        Route::get('/facturacion', [PublicController::class, 'facturacion'])->name('ejemplos.facturacion');
        Route::get('/inventario', [PublicController::class, 'inventario'])->name('ejemplos.inventario');
        Route::get('/portafolio', [PublicController::class, 'portafolio'])->name('ejemplos.portafolio');
    });

    // Contacto
    Route::get('/contacto', [PublicController::class, 'contacto'])->name('contacto');
    Route::post('/contacto', [PublicController::class, 'contactoEnviar'])->name('contacto.enviar');
});

// ── ÁREA DE DEMOSTRACIÓN (Demos para clientes) ──────────────────────────────
// He unificado ambos bloques 'demo' para evitar confusiones.
Route::prefix('demo')->name('demo.')->group(function () {

    // Dashboard y General
    Route::get('/', [PublicController::class, 'demoDashboard'])->name('dashboard');

    // Clientes
    Route::prefix('clientes')->group(function () {
        Route::get('/', [PublicController::class, 'demoClientes'])->name('clientes');
        Route::get('/crear', function () {
            return view('demos.clientes_form', ['title' => 'Nuevo Cliente - Demo', 'module' => 'clientes', 'action' => 'create']);
        })->name('clientes.create');
    });

    // Inventario
    Route::prefix('inventario')->group(function () {
        Route::get('/', [PublicController::class, 'demoInventario'])->name('inventario');
        Route::get('/formulario', function () {
            return view('demos.inventario_form', ['title' => 'Formulario de Inventario - Demo', 'module' => 'inventario']);
        })->name('inventario.form');
        Route::get('/existencia', [PublicController::class, 'demoExistencia'])->name('inventario.existencia');
        Route::get('/movimiento', function () {
            return view('demos.inventario_movimiento');
        })->name('inventario.movimiento.create');
    });

    // Cotizaciones y Facturación
    Route::get('/cotizaciones', [PublicController::class, 'demoCotizaciones'])->name('cotizaciones');
    Route::get('/cotizaciones/crear', function () {
        return view('demos.cotizaciones_form', ['title' => 'Nueva Cotización - Demo', 'module' => 'cotizaciones']);
    })->name('cotizaciones.create');

    Route::get('/facturacion', [PublicController::class, 'demoFacturacion'])->name('facturacion');
    Route::get('/facturacion/crear', function () {
        return view('demos.facturacion_form', ['title' => 'Nueva Factura - Demo', 'module' => 'facturacion']);
    })->name('facturacion.create');
});

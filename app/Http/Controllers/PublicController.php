<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicController extends Controller
{
    // ── Sitio público ─────────────────────────────────────────────────────────

    public function home()
    {
        return view('home');
    }

    public function nosotros()
    {
        return view('public.nosotros');
    }

    public function misionVision()
    {
        return view('public.mision-vision');
    }

    public function valores()
    {
        return view('public.valores');
    }

    public function equipo()
    {
        return view('public.equipo');
    }

    public function servicios()
    {
        return view('public.servicios');
    }

    public function desarrollo()
    {
        return view('public.servicios.desarrollo');
    }

    public function movil()
    {
        return view('public.servicios.movil');
    }

    public function soporte()
    {
        return view('public.servicios.soporte');
    }

    public function consultoria()
    {
        // Vista pendiente — por ahora redirige a servicios
        return redirect()->route('public.servicios');
    }

    public function cotizaciones()
    {
        return view('public.ejemplos.cotizaciones');
    }

    public function facturacion()
    {
        return view('public.ejemplos.facturacion');
    }

    public function inventario()
    {
        return view('public.ejemplos.inventario');
    }

    public function portafolio()
    {
        return view('public.ejemplos.portafolio');
    }

    public function contacto()
    {
        return view('public.contacto');
    }

    public function contactoEnviar(Request $request)
    {
        $request->validate([
            'nombre'  => 'required|string|max:150',
            'email'   => 'required|email|max:150',
            'telefono'=> 'nullable|string|max:20',
            'mensaje' => 'required|string|max:2000',
        ]);

        // TODO: enviar email con Mail::to(...)->send(new ContactoMailable($request->all()))
        // Por ahora solo redirige con mensaje de éxito

        return redirect()->route('public.contacto')
            ->with('success', '¡Mensaje enviado! Te contactaremos pronto.');
    }

    // ── Demo ──────────────────────────────────────────────────────────────────

    public function demoDashboard()
    {
        return view('demo.dashboard', ['title' => 'Dashboard']);
    }

    public function demoClientes()
    {
        return view('demo.clientes', ['title' => 'Clientes']);
    }

    public function demoInventario()
    {
        return view('demo.inventario', ['title' => 'Inventario']);
    }

    public function demoExistencia()
    {
        return view('demo.inventario-existencia', ['title' => 'Existencia de Inventario']);
    }

    public function demoCotizaciones()
    {
        return view('demo.cotizaciones', ['title' => 'Cotizaciones']);
    }

    public function demoFacturacion()
    {
        return view('demo.facturacion', ['title' => 'Facturación']);
    }
}

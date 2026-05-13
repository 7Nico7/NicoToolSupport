<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Services\Dashboard\DashboardService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $service
    ) {}

    public function index(): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return Inertia::render('Dashboard/Index', [
            'dashboard' => $this->service->getData($user),
        ]);
    }
}

<?php
// app/Http/Controllers/HelpdeskController.php

namespace App\Http\Controllers;

use App\Http\Requests\Helpdesk\StoreHelpdeskRequest;
use App\Http\Requests\Helpdesk\UpdateHelpdeskRequest;
use App\Models\Company;
use App\Models\Helpdesk;
use App\Models\User;
use App\Services\Helpdesk\HelpdeskService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class HelpdeskController extends Controller
{
    public function __construct(
        private readonly HelpdeskService $service
    ) {}

    // ── Index ─────────────────────────────────────────────────────────────────

    public function index(Request $request): Response
    {
        /** @var User $user */
        $user         = Auth::user();
        $isSuperAdmin = $user->isSuperAdmin();

        $this->authorizeRole($user, ['super_admin', 'admin', 'agent']);

        $helpdesks = $this->service->paginate(
            user:    $user,
            search:  $request->string('search')->toString(),
            perPage: 5,
            // super_admin puede filtrar por company_id opcional
            companyId: $isSuperAdmin ? $request->integer('company_id') ?: null : null,
        );

        return Inertia::render('Helpdesks/Index', [
            'helpdesks' => $helpdesks,
            'agents'    => $this->service->availableAgents($user->company_id),
            'filters'   => $request->only(['search', 'company_id']),
            'companies' => $isSuperAdmin
                ? Company::orderBy('name')->get(['id', 'name'])
                : [],
            'can'       => $this->can($user),
        ]);
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function create(): Response
    {
        /** @var User $user */
        $user         = Auth::user();
        $isSuperAdmin = $user->isSuperAdmin();

        $this->authorizeRole($user, ['super_admin', 'admin']);

        return Inertia::render('Helpdesks/Form', [
            'agents'    => $isSuperAdmin ? [] : $this->service->availableAgents($user->company_id),
            // super_admin: todos los agentes con company_id para filtrar client-side
            'allAgents' => $isSuperAdmin
                ? User::whereIn('role', ['agent', 'admin'])
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get(['id', 'name', 'company_id'])
                : [],
            'companies' => $isSuperAdmin
                ? Company::orderBy('name')->get(['id', 'name'])
                : [],
            'can'       => $this->can($user),
        ]);
    }

    // ── Store ─────────────────────────────────────────────────────────────────

    public function store(StoreHelpdeskRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $this->authorizeRole($user, ['super_admin', 'admin']);

        $this->service->create($request->validated(), $user);

        return redirect()
            ->route('helpdesks.index')
            ->with('success', 'Helpdesk creado correctamente.');
    }

    // ── Edit ──────────────────────────────────────────────────────────────────

    public function edit(Helpdesk $helpdesk): Response
    {
        /** @var User $user */
        $user         = Auth::user();
        $isSuperAdmin = $user->isSuperAdmin();

        $this->authorizeRole($user, ['super_admin', 'admin']);
        if (! $isSuperAdmin) {
            $this->authorizeCompany($user, $helpdesk);
        }

        $helpdesk->load('users');

        return Inertia::render('Helpdesks/Form', [
            'helpdesk' => [
                'id'         => $helpdesk->id,
                'name'       => $helpdesk->name,
                'company_id' => $helpdesk->company_id,
                'agent_ids'  => $helpdesk->users->pluck('id')->toArray(),
            ],
            'agents'    => $isSuperAdmin ? [] : $this->service->availableAgents($user->company_id),
            'allAgents' => $isSuperAdmin
                ? User::whereIn('role', ['agent', 'admin'])
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get(['id', 'name', 'company_id'])
                : [],
            'companies' => $isSuperAdmin
                ? Company::orderBy('name')->get(['id', 'name'])
                : [],
            'can'       => $this->can($user),
        ]);
    }

    // ── Update ────────────────────────────────────────────────────────────────

    public function update(UpdateHelpdeskRequest $request, Helpdesk $helpdesk): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $this->authorizeRole($user, ['super_admin', 'admin']);
        if (! $user->isSuperAdmin()) {
            $this->authorizeCompany($user, $helpdesk);
        }

        $this->service->update($helpdesk, $request->validated());

        return redirect()
            ->route('helpdesks.index')
            ->with('success', 'Helpdesk actualizado correctamente.');
    }

    // ── Destroy ───────────────────────────────────────────────────────────────

    public function destroy(Helpdesk $helpdesk): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $this->authorizeRole($user, ['super_admin', 'admin']);
        if (! $user->isSuperAdmin()) {
            $this->authorizeCompany($user, $helpdesk);
        }

        try {
            $this->service->delete($helpdesk);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('helpdesks.index')
            ->with('success', 'Helpdesk eliminado correctamente.');
    }

    // ── Helpers de autorización ───────────────────────────────────────────────

    private function authorizeRole(User $user, array $roles): void
    {
        abort_if(
            ! in_array($user->role, $roles),
            403,
            'No tienes permiso para realizar esta acción.'
        );
    }

    private function authorizeCompany(User $user, Helpdesk $helpdesk): void
    {
        abort_if(
            $helpdesk->company_id !== $user->company_id,
            403,
            'No tienes acceso a este helpdesk.'
        );
    }

    private function can(User $user): array
    {
        $isManager = in_array($user->role, ['super_admin', 'admin']);

        return [
            'create' => $isManager,
            'edit'   => $isManager,
            'delete' => $isManager,
        ];
    }
}

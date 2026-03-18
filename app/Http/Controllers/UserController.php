<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;


class UserController extends Controller
{

    public function search(Request $request)
    {
        $query = $request->get('q');

        $users = User::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit(10) // Limita resultados para no saturar la interfaz
            ->get(['id', 'name', 'email']); // Solo campos necesarios

        return response()->json($users);
    }

        public function index(Request $request): Response
    {
        /** @var User $authUser */
        $authUser = Auth::user();

        abort_if($authUser->isClient(), 403);

        $query = User::with('company:id,name')
            ->orderBy('name');

        // ── Scope por rol ──────────────────────────────────────────────────────
        if (!$authUser->isSuperAdmin()) {
            $query->where('company_id', $authUser->company_id);
        }

        // ── Filtros ───────────────────────────────────────────────────────────
        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name',  'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->string('role'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', (bool) $request->integer('is_active'));
        }

        if ($authUser->isSuperAdmin() && $request->filled('company_id')) {
            $query->where('company_id', $request->integer('company_id'));
        }

        return Inertia::render('Users/Index', [
            'users'     => $query->paginate(5)->withQueryString(),
            'filters'   => $request->only(['search', 'role', 'is_active', 'company_id']),
            'companies' => $authUser->isSuperAdmin()
                ? Company::orderBy('name')->get(['id', 'name'])
                : [],
            'can'       => $this->permissions($authUser),
        ]);
    }

    // ─── Create ──────────────────────────────────────────────────────────────────

    public function create(): Response
    {
        /** @var User $authUser */
        $authUser = Auth::user();

        abort_if($authUser->isAgent() || $authUser->isClient(), 403);

        return Inertia::render('Users/Form', [
            'user'      => null,
            'companies' => $authUser->isSuperAdmin()
                ? Company::orderBy('name')->get(['id', 'name'])
                : [$authUser->company()->first(['id', 'name'])],
            'roles'     => $this->availableRoles($authUser),
            'can'       => $this->permissions($authUser),
        ]);
    }

    // ─── Store ───────────────────────────────────────────────────────────────────

    public function store(Request $request): RedirectResponse
    {
        /** @var User $authUser */
        $authUser = Auth::user();

        abort_if($authUser->isAgent() || $authUser->isClient(), 403);

        $validated = $this->validateUser($request, $authUser);

        // Admin siempre asigna a su propia compañía
        if (!$authUser->isSuperAdmin()) {
            $validated['company_id'] = $authUser->company_id;
        }

        User::create($validated);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    // ─── Edit ────────────────────────────────────────────────────────────────────

    public function edit(User $user): Response
    {
        /** @var User $authUser */
        $authUser = Auth::user();

        abort_if($authUser->isAgent() || $authUser->isClient(), 403);
        $this->authorizeUserAccess($authUser, $user);

        return Inertia::render('Users/Form', [
            'user'      => $user->only(['id', 'company_id', 'name', 'email', 'role', 'is_active']),
            'companies' => $authUser->isSuperAdmin()
                ? Company::orderBy('name')->get(['id', 'name'])
                : [$authUser->company()->first(['id', 'name'])],
            'roles'     => $this->availableRoles($authUser),
            'can'       => $this->permissions($authUser),
        ]);
    }

    // ─── Update ──────────────────────────────────────────────────────────────────

    public function update(Request $request, User $user): RedirectResponse
    {
        /** @var User $authUser */
        $authUser = Auth::user();

        abort_if($authUser->isAgent() || $authUser->isClient(), 403);
        $this->authorizeUserAccess($authUser, $user);

        $validated = $this->validateUser($request, $authUser, $user);

        // Admin no puede reasignar a otra compañía
        if (!$authUser->isSuperAdmin()) {
            unset($validated['company_id']);
        }

        // Si no viene contraseña nueva, se conserva la actual
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    // ─── Destroy (desactivar) ────────────────────────────────────────────────────

    /**
     * Desactiva el usuario (is_active = false).
     * No se puede desactivar a sí mismo.
     */
    public function destroy(User $user): RedirectResponse
    {
        /** @var User $authUser */
        $authUser = Auth::user();

        abort_if($authUser->isAgent() || $authUser->isClient(), 403);
        abort_if($authUser->id === $user->id, 403, 'No puedes desactivarte a ti mismo.');
        $this->authorizeUserAccess($authUser, $user);

        $user->update(['is_active' => false]);

        return back()->with('success', 'Usuario desactivado.');
    }

    // ─── Helpers internos ────────────────────────────────────────────────────────

    private function authorizeUserAccess(User $authUser, User $target): void
    {
        if ($authUser->isSuperAdmin()) return;

        // Admin solo gestiona usuarios de su compañía
        abort_if($target->company_id !== $authUser->company_id, 403);

        // Admin no puede tocar a otros super_admin
        abort_if($target->isSuperAdmin(), 403);
    }

    private function validateUser(Request $request, User $authUser, ?User $user = null): array
    {
        $isUpdate = $user !== null;

        return $request->validate([
            'company_id' => $authUser->isSuperAdmin()
                ? ['required', 'exists:companies,id']
                : ['nullable'],
            'name'       => ['required', 'string', 'max:255'],
            'email'      => [
                'required', 'email', 'max:255',
                $isUpdate
                    ? Rule::unique('users')->ignore($user->id)
                    : Rule::unique('users'),
            ],
            'role'       => ['required', Rule::in($this->availableRoles($authUser))],
            'password'   => $isUpdate
                ? ['nullable', 'string', 'min:8', 'confirmed']
                : ['required', 'string', 'min:8', 'confirmed'],
            'is_active'  => ['boolean'],
        ]);
    }

    /**
     * Roles que el usuario autenticado puede asignar.
     * super_admin → todos; admin → todos excepto super_admin.
     */
    private function availableRoles(User $authUser): array
    {
        $roles = ['admin', 'agent', 'client'];

        if ($authUser->isSuperAdmin()) {
            array_unshift($roles, 'super_admin');
        }

        return $roles;
    }

    private function permissions(User $authUser): array
    {
        return [
            'create'     => $authUser->isSuperAdmin() || $authUser->isAdmin(),
            'update'     => $authUser->isSuperAdmin() || $authUser->isAdmin(),
            'deactivate' => $authUser->isSuperAdmin() || $authUser->isAdmin(),
        ];
    }
}

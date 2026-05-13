<?php
// app/Http/Controllers/ProjectController.php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\User;

class ProjectController extends Controller
{
    public function index(Request $request): Response
    {
        /** @var User $user */
        $user         = Auth::user();
        $isSuperAdmin = $user->isSuperAdmin();

        $query = Project::with(['creator:id,name', 'company:id,name'])
            ->orderBy('name');

        // ── Scope por rol ──────────────────────────────────────────────────────
        if ($isSuperAdmin) {
            // super_admin ve proyectos de todas las compañías
            if ($v = $request->integer('company_id')) {
                $query->where('company_id', $v);
            }
        } elseif ($user->role === 'client') {
            $query->where('company_id', $user->company_id)
                  ->where('created_by', $user->id);
        } else {
            // admin / agent
            $query->where('company_id', $user->company_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', (bool) $request->integer('is_active'));
        }

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where('name', 'like', "%{$search}%");
        }

        return Inertia::render('Projects/Index', [
            'projects'  => $query->paginate(5)->withQueryString(),
            'filters'   => $request->only(['search', 'is_active', 'company_id']),
            'companies' => $isSuperAdmin
                ? Company::orderBy('name')->get(['id', 'name'])
                : [],
            'can'       => $this->permissions($user),
        ]);
    }

    public function create(): Response
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless(in_array($user->role, ['super_admin', 'admin']), 403);

        return Inertia::render('Projects/Form', [
            'project'   => null,
            'company'   => $user->isSuperAdmin() ? null : $user->company()->first(['id', 'name']),
            'companies' => $user->isSuperAdmin()
                ? Company::orderBy('name')->get(['id', 'name'])
                : [],
            'can'       => $this->permissions($user),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless(in_array($user->role, ['super_admin', 'admin']), 403);

        $rules = [
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['nullable', 'email', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active'   => ['boolean'],
        ];

        if ($user->isSuperAdmin()) {
            $rules['company_id'] = ['required', 'exists:companies,id'];
        }

        $validated = $request->validate($rules);

        Project::create([
            ...$validated,
            'company_id' => $user->isSuperAdmin()
                ? $validated['company_id']
                : $user->company_id,
            'created_by' => Auth::id(),
            'is_active'  => $validated['is_active'] ?? true,
        ]);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Proyecto creado correctamente.');
    }

    public function edit(Project $project): Response
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless(in_array($user->role, ['super_admin', 'admin']), 403);
        // super_admin puede editar proyectos de cualquier compañía
        if (! $user->isSuperAdmin()) {
            abort_if($project->company_id !== $user->company_id, 403);
        }

        return Inertia::render('Projects/Form', [
            'project'   => $project,
            'company'   => $project->company()->first(['id', 'name']),
            'companies' => $user->isSuperAdmin()
                ? Company::orderBy('name')->get(['id', 'name'])
                : [],
            'can'       => $this->permissions($user),
        ]);
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless(in_array($user->role, ['super_admin', 'admin']), 403);
        if (! $user->isSuperAdmin()) {
            abort_if($project->company_id !== $user->company_id, 403);
        }

        $rules = [
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['nullable', 'email', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active'   => ['boolean'],
        ];

        if ($user->isSuperAdmin()) {
            $rules['company_id'] = ['required', 'exists:companies,id'];
        }

        $validated = $request->validate($rules);

        if ($user->role === 'agent') {
            unset($validated['is_active']);
        }

        $project->update($validated);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Proyecto actualizado correctamente.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless(in_array($user->role, ['super_admin', 'admin']), 403);
        if (! $user->isSuperAdmin()) {
            abort_if($project->company_id !== $user->company_id, 403);
        }

        $project->update(['is_active' => false]);

        return back()->with('success', 'Proyecto desactivado.');
    }

    private function permissions(User $user): array
    {
        $isPowerUser = in_array($user->role, ['super_admin', 'admin']);

        return [
            'create'     => $isPowerUser,
            'update'     => $isPowerUser || $user->role === 'agent',
            'deactivate' => $isPowerUser,
        ];
    }
}

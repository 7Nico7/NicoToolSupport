<?php
// app/Http/Controllers/ProjectController.php
//
// Módulo Proyectos — CRUD con scope multi-tenant y gate por rol.
//
// Roles:
//   admin  → CRUD completo (incluyendo desactivar)
//   agent  → index + show + edit + update (sin destroy)
//   client → solo index (filtrado por created_by)
//
// Patrones aplicados:
//   Thin Controller  — orquesta, no procesa lógica de negocio
//   Template Method  — scope → filter → paginate → serialize
//   Gate manual      — abort_if / abort_unless (sin Policy, consistente con el resto del proyecto)

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\User;

class ProjectController extends Controller
{
    // ─── Vista principal ─────────────────────────────────────────────────────────

    /**
     * Lista paginada con filtros: búsqueda por nombre/email e is_active.
     * Scope por rol: admin/agent ven todos; client solo los suyos.
     */
    public function index(Request $request): Response
    {
        $user = Auth::user();

        $query = Project::with(['creator:id,name', 'company:id,name'])
            ->where('company_id', $user->company_id)
            ->orderBy('name');

        // ── Scope por rol ──────────────────────────────────────────────────────
        if ($user->role === 'client') {
            $query->where('created_by', $user->id);
        }
        // admin / agent → ven todos los proyectos de la compañía

        // ── Filtro: estado ────────────────────────────────────────────────────
        if ($request->filled('is_active')) {
            $query->where('is_active', (bool) $request->integer('is_active'));
        }

        // ── Filtro: búsqueda por nombre o email ───────────────────────────────
        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name',  'like', "%{$search}%");
                //     ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return Inertia::render('Projects/Index', [
            'projects' => $query->paginate(5)->withQueryString(),
            'filters'  => $request->only(['search', 'is_active']),
            'can'      => $this->permissions($user),
        ]);
    }

    // ─── Formulario de creación ──────────────────────────────────────────────────

    /** Solo admin puede crear proyectos. */
    public function create(): Response
    {
        /** @var User $user */
        $user = Auth::user();
       abort_unless(in_array($user->role, ['super_admin', 'admin']), 403);


        return Inertia::render('Projects/Form', [
            'project' => null,
            'company' => $user->company()->first(['id', 'name']),
            'can'     => $this->permissions($user),
        ]);
    }

    // ─── Guardar nuevo ───────────────────────────────────────────────────────────

    public function store(Request $request): RedirectResponse
    {

    abort_unless(in_array(Auth::user()->role, ['super_admin', 'admin']), 403);


        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['nullable', 'email', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active'   => ['boolean'],
        ]);

        Project::create([
            ...$validated,
            'company_id' => Auth::user()->company_id,
            'created_by' => Auth::id(),
            'is_active'  => $validated['is_active'] ?? true,
        ]);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Proyecto creado correctamente.');
    }

    // ─── Formulario de edición ───────────────────────────────────────────────────

    /** Admin y agent pueden editar. Client: 403. */
    public function edit(Project $project): Response
    {
        $user = Auth::user();

       abort_unless(in_array($user->role, ['super_admin', 'admin']), 403);
        abort_if($project->company_id !== $user->company_id, 403);

        return Inertia::render('Projects/Form', [
            'project' => $project,
            'company' => $project->company()->first(['id', 'name']),
            'can'     => $this->permissions($user),
        ]);
    }

    // ─── Actualizar ──────────────────────────────────────────────────────────────

    public function update(Request $request, Project $project): RedirectResponse
    {
        $user = Auth::user();

       abort_unless(in_array($user->role, ['super_admin', 'admin']), 403);
        abort_if($project->company_id !== $user->company_id, 403);

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['nullable', 'email', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            // is_active solo lo puede cambiar el admin desde el form
            'is_active'   => ['boolean'],
        ]);

        // Agentes no pueden cambiar is_active
        if ($user->role === 'agent') {
            unset($validated['is_active']);
        }

        $project->update($validated);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Proyecto actualizado correctamente.');
    }

    // ─── Desactivar (soft-delete) ────────────────────────────────────────────────

    /**
     * No elimina el registro — lo desactiva (is_active = false).
     * Solo admin puede desactivar.
     */
    public function destroy(Project $project): RedirectResponse
    {
        $user = Auth::user();

       abort_unless(in_array($user->role, ['super_admin', 'admin']), 403);
        abort_if($project->company_id !== $user->company_id, 403);

        $project->update(['is_active' => false]);

        return back()->with('success', 'Proyecto desactivado.');
    }

    // ─── Helper interno ──────────────────────────────────────────────────────────

    /**
     * Objeto de permisos que se pasa a las páginas Inertia.
     * El frontend lo usa para mostrar/ocultar botones y acciones.
     */
    private function permissions(User $user): array
    {
        // Definimos quién es "Especial"
        $isPowerUser = in_array($user->role, ['super_admin', 'admin']);

        return [
            'create'     => $isPowerUser,
            'update'     => $isPowerUser || $user->role === 'agent',
            'deactivate' => $isPowerUser,
        ];
    }
}

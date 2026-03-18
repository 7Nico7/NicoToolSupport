<?php
// app/Http/Controllers/CompanyController.php
//
// Módulo Compañías — CRUD con scope por rol.
//
// Roles:
//   super_admin → ve todas las compañías, CRUD completo
//   admin       → ve y edita solo su propia compañía (su company_id)
//   agent       → ve y actualiza solo su compañía; sin crear ni desactivar
//   client      → solo ve su compañía (index, sin formulario de edición)
//
// Patrones aplicados:
//   Thin Controller  — orquesta, no procesa
//   Template Method  — scope → filter → paginate → render
//   Gate manual      — abort_if consistente con el resto del proyecto

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\User;

class CompanyController extends Controller
{
    // ─── Index ───────────────────────────────────────────────────────────────────

    /**
     * super_admin → todas las compañías con filtros.
     * admin/agent/client → solo su propia compañía (sin filtros de búsqueda amplios).
     */
    public function index(Request $request): Response
    {
        /** @var User $user */
        $user = Auth::user();

        $query = Company::query()->orderBy('name');

        // ── Scope por rol ──────────────────────────────────────────────────────
        if (!$user->isSuperAdmin()) {
            // admin, agent y client solo ven su compañía
            $query->where('id', $user->company_id);
        }

        // ── Filtros (solo significativos para super_admin) ─────────────────────
        if ($search = $request->string('search')->trim()->toString()) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', (bool) $request->integer('is_active'));
        }

        return Inertia::render('Companies/Index', [
            'companies' => $query->paginate(15)->withQueryString(),
            'filters'   => $request->only(['search', 'is_active']),
            'can'       => $this->permissions($user),
        ]);
    }

    // ─── Create ──────────────────────────────────────────────────────────────────

    /** Solo super_admin puede crear nuevas compañías. */
    public function create(): Response
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if(!$user->isSuperAdmin(), 403);

        return Inertia::render('Companies/Form', [
            'company' => null,
            'can'     => $this->permissions($user),
        ]);
    }

    // ─── Store ───────────────────────────────────────────────────────────────────

    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if(!$user->isSuperAdmin(), 403);

        $validated = $this->validateCompany($request);

        // Crear primero sin logo para obtener el id
        $company = Company::create([
            ...$validated,
            'slug'      => Str::slug($validated['name']),
            'logo'      => null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Ahora guardar el logo en logos/{id}/ con el id real
        if ($request->hasFile('logo')) {
            $company->update(['logo' => $this->handleLogoUpload($request, $company->id)]);
        }

        return redirect()
            ->route('companies.index')
            ->with('success', 'Compañía creada correctamente.');
    }

    // ─── Edit ────────────────────────────────────────────────────────────────────

    /**
     * super_admin → cualquier compañía.
     * admin/agent → solo su propia compañía.
     * client      → 403.
     */
    public function edit(Company $company): Response
    {
        /** @var User $user */
        $user = Auth::user();

        abort_if($user->isClient(), 403);
        $this->authorizeCompanyAccess($user, $company);

        return Inertia::render('Companies/Form', [
            'company' => $company,
            'can'     => $this->permissions($user),
        ]);
    }

    // ─── Update ──────────────────────────────────────────────────────────────────

    public function update(Request $request, Company $company): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        abort_if($user->isClient(), 403);
        $this->authorizeCompanyAccess($user, $company);

        $validated = $this->validateCompany($request, $company);

        // El slug se recalcula cuando cambia el nombre
        if ($validated['name'] !== $company->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Logo: reemplazar si viene uno nuevo
        if ($request->hasFile('logo')) {
            $this->deleteLogo($company->logo);
            $validated['logo'] = $this->handleLogoUpload($request, $company->id);
        }

        // is_active solo lo modifica super_admin
        if (!$user->isSuperAdmin()) {
            unset($validated['is_active']);
        }

        $company->update($validated);

        return redirect()
            ->route('companies.index')
            ->with('success', 'Compañía actualizada correctamente.');
    }

    // ─── Destroy (desactivar) ────────────────────────────────────────────────────

    /**
     * No elimina — desactiva (is_active = false).
     * Solo super_admin puede desactivar compañías.
     */
    public function destroy(Company $company): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if(!$user->isSuperAdmin(), 403);

        $company->update(['is_active' => false]);

        return back()->with('success', 'Compañía desactivada.');
    }

    // ─── Helpers internos ────────────────────────────────────────────────────────

    /**
     * Verifica que el usuario tenga acceso a la compañía solicitada.
     * super_admin → siempre.
     * admin/agent → solo su company_id.
     */
    private function authorizeCompanyAccess(\App\Models\User $user, Company $company): void
    {
        if ($user->isSuperAdmin()) return;

        abort_if($company->id !== $user->company_id, 403);
    }

    /**
     * Validación compartida store/update.
     * El slug se omite del request (se calcula desde name).
     */
    private function validateCompany(Request $request, ?Company $company = null): array
    {
        return $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            // 'image' rule rechaza SVG porque no es un bitmap — usamos mimes explícito
            'logo'      => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif,svg', 'max:2048'],
            'is_active' => ['boolean'],
        ]);
    }

    /**
     * Guarda el logo en storage/logos/{company_id}/ y devuelve la ruta relativa.
     * Para nuevas compañías (sin id todavía) usa la carpeta 'logos/tmp' —
     * el método storeWithCompanyId() lo mueve tras el insert.
     * En la práctica siempre se llama con $companyId en store() y update().
     */
    private function handleLogoUpload(Request $request, ?int $companyId = null): ?string
    {
        if (!$request->hasFile('logo')) return null;

        $folder = $companyId ? "logos/{$companyId}" : 'logos/tmp';

        return $request->file('logo')->store($folder, 'public');
    }

    /** Elimina el logo anterior del disco si existe. */
    private function deleteLogo(?string $path): void
    {
        if ($path) Storage::disk('public')->delete($path);
    }

    /**
     * Permisos serializados para el frontend.
     */
    private function permissions(\App\Models\User $user): array
    {
        return [
            'create'     => $user->isSuperAdmin(),
            'update'     => $user->isSuperAdmin() || $user->isAdmin() || $user->isAgent(),
            'deactivate' => $user->isSuperAdmin(),
        ];
    }
}

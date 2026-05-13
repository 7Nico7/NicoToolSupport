<?php
// app/Http/Controllers/TicketCategoryController.php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class TicketCategoryController extends Controller
{
    public function index(Request $request): Response
    {
        /** @var User $user */
        $user         = Auth::user();
        $isSuperAdmin = $user->isSuperAdmin();

        abort_if($user->isClient(), 403);

        $query = TicketCategory::with('company:id,name')
            ->withCount('tickets')
            ->orderBy('name');

        // super_admin ve todas; el resto solo las de su compañía
        if ($isSuperAdmin) {
            if ($v = $request->integer('company_id')) {
                $query->where('company_id', $v);
            }
        } else {
            $query->where('company_id', $user->company_id);
        }

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where('name', 'like', "%{$search}%");
        }

        return Inertia::render('Categories/Index', [
            'categories' => $query->paginate(5)->withQueryString(),
            'filters'    => $request->only(['search', 'company_id']),
            'companies'  => $isSuperAdmin
                ? Company::orderBy('name')->get(['id', 'name'])
                : [],
            'can' => $this->permissions($user),
        ]);
    }

    public function create(): Response
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless(in_array($user->role, ['super_admin', 'admin']), 403);

        return Inertia::render('Categories/Form', [
            'category'  => null,
            'companies' => $user->isSuperAdmin()
                ? Company::orderBy('name')->get(['id', 'name'])
                : [],
            'company' => $user->isSuperAdmin() ? null : $user->company()->first(['id', 'name']),
            'can'     => $this->permissions($user),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless(in_array($user->role, ['super_admin', 'admin']), 403);

        $companyId = $user->isSuperAdmin()
            ? $request->integer('company_id') ?: $user->company_id
            : $user->company_id;

        $request->validate([
            'name'       => ['required', 'string', 'max:100',
                             \Illuminate\Validation\Rule::unique('ticket_categories')
                                ->where('company_id', $companyId)],
            'company_id' => $user->isSuperAdmin()
                ? ['required', 'exists:companies,id']
                : ['nullable'],
        ]);

        TicketCategory::create([
            'name'       => $request->string('name')->trim()->toString(),
            'company_id' => $companyId,
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function edit(TicketCategory $category): Response
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless(in_array($user->role, ['super_admin', 'admin']), 403);
        if (! $user->isSuperAdmin()) {
            abort_if($category->company_id !== $user->company_id, 403);
        }

        return Inertia::render('Categories/Form', [
            'category'  => $category,
            'companies' => $user->isSuperAdmin()
                ? Company::orderBy('name')->get(['id', 'name'])
                : [],
            'company' => $category->company()->first(['id', 'name']),
            'can'     => $this->permissions($user),
        ]);
    }

    public function update(Request $request, TicketCategory $category): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless(in_array($user->role, ['super_admin', 'admin']), 403);
        if (! $user->isSuperAdmin()) {
            abort_if($category->company_id !== $user->company_id, 403);
        }

        $companyId = $user->isSuperAdmin()
            ? ($request->integer('company_id') ?: $category->company_id)
            : $user->company_id;

        $request->validate([
            'name'       => ['required', 'string', 'max:100',
                             \Illuminate\Validation\Rule::unique('ticket_categories')
                                ->where('company_id', $companyId)
                                ->ignore($category->id)],
            'company_id' => ['nullable', 'exists:companies,id'],
        ]);

        $category->update([
            'name'       => $request->string('name')->trim()->toString(),
            'company_id' => $companyId,
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(TicketCategory $category): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless(in_array($user->role, ['super_admin', 'admin']), 403);
        if (! $user->isSuperAdmin()) {
            abort_if($category->company_id !== $user->company_id, 403);
        }

        // No eliminar si tiene tickets asociados
        if ($category->tickets()->exists()) {
            return back()->with('error', 'No se puede eliminar: la categoría tiene tickets asociados.');
        }

        $category->delete();

        return back()->with('success', 'Categoría eliminada correctamente.');
    }

    private function permissions(User $user): array
    {
        $isManager = in_array($user->role, ['super_admin', 'admin']);
        return [
            'create' => $isManager,
            'update' => $isManager,
            'delete' => $isManager,
        ];
    }
}

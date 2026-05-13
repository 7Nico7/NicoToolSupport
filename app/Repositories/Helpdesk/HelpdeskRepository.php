<?php
// app/Repositories/Helpdesk/HelpdeskRepository.php
//
// Solo acceso a datos. Sin lógica de negocio.
// Recibe parámetros simples, retorna colecciones o modelos.

namespace App\Repositories\Helpdesk;

use App\Models\Helpdesk;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class HelpdeskRepository
{
    // ── Consultas ─────────────────────────────────────────────────────────────

    /**
     * Lista paginada de helpdesks con filtros opcionales.
     * Scope por compañía siempre aplicado.
     */
    public function paginate(
        ?int   $companyId = null,  // nullable: null = todas las compañías (super_admin)
        string $search    = '',
        int    $perPage   = 15,
    ): LengthAwarePaginator {
        return Helpdesk::query()
            ->when($companyId, fn($q) => $q->where('company_id', $companyId))
            ->with(['users', 'company'])   // company para mostrarlo en la tabla cuando es super_admin
            ->withCount('tickets')
            ->when(
                $search,
                fn($q) =>
                $q->where(
                    fn($inner) =>
                    $inner->where('helpdesks.name', 'like', "%{$search}%")
                        ->orWhereHas(
                            'users',
                            fn($u) =>
                            $u->where('users.name', 'like', "%{$search}%")
                        )
                )
            )
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }


    /**
     * Un helpdesk por ID, con sus agentes cargados.
     */
    public function findWithAgents(int $id): ?Helpdesk
    {
        return Helpdesk::with('users')->find($id);
    }

    /**
     * Todos los helpdesks de una compañía (para selects).
     */
    public function allByCompany(int $companyId): Collection
    {
        return Helpdesk::where('company_id', $companyId)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    // ── Escritura ─────────────────────────────────────────────────────────────

    public function create(array $data): Helpdesk
    {
        return Helpdesk::create($data);
    }

    public function update(Helpdesk $helpdesk, array $data): Helpdesk
    {
        $helpdesk->update($data);
        return $helpdesk->fresh();
    }

    public function delete(Helpdesk $helpdesk): void
    {
        $helpdesk->delete();
    }

    /**
     * Sincroniza la lista de agentes asignados al helpdesk.
     * sync() añade los nuevos y elimina los que ya no están.
     */
    public function syncAgents(Helpdesk $helpdesk, array $userIds): void
    {
        $helpdesk->users()->sync($userIds);
    }
}

<?php
// app/Services/Helpdesk/HelpdeskService.php
//
// Lógica de negocio del módulo Helpdesk.
// No maneja Request ni Response — solo trabaja con datos validados.
// Coordina el repositorio y aplica reglas de negocio.

namespace App\Services\Helpdesk;


use App\Models\Helpdesk;
use App\Models\User;
use App\Repositories\Helpdesk\HelpdeskRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class HelpdeskService
{
    public function __construct(
        private readonly HelpdeskRepository $repository
    ) {}

    // ── Consultas ─────────────────────────────────────────────────────────────

    public function paginate(
        User   $user,
        string $search    = '',
        int    $perPage   = 15,
        ?int   $companyId = null,  // null = ver todos (super_admin sin filtro)
    ): LengthAwarePaginator {
        return $this->repository->paginate(
            companyId: $companyId ?? ($user->isSuperAdmin() ? null : $user->company_id),
            search: $search,
            perPage: $perPage,
        );
    }


    public function findWithAgents(int $id): ?Helpdesk
    {
        return $this->repository->findWithAgents($id);
    }

    /**
     * Agentes disponibles para asignar — solo agents y admins de la misma compañía.
     */
    public function availableAgents(int $companyId): Collection
    {
        return User::where('company_id', $companyId)
            ->whereIn('role', ['agent', 'admin'])
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role']);
    }

    // ── Creación ──────────────────────────────────────────────────────────────

    public function create(array $validated, User $user): Helpdesk
    {
        $helpdesk = $this->repository->create([
            'company_id' => $validated['company_id'] ?? $user->company_id,
            'name'       => $validated['name'],
        ]);

        // Asignar agentes si vienen en el payload
        if (! empty($validated['agent_ids'])) {
            $this->repository->syncAgents($helpdesk, $validated['agent_ids']);
        }

        return $this->repository->findWithAgents($helpdesk->id);
    }

    // ── Actualización ─────────────────────────────────────────────────────────

    public function update(Helpdesk $helpdesk, array $validated): Helpdesk
    {
        $helpdesk = $this->repository->update($helpdesk, [
            'company_id' => $validated['company_id'] ?? $helpdesk->company_id,
            'name'       => $validated['name'],
        ]);

        $this->repository->syncAgents($helpdesk, $validated['agent_ids'] ?? []);

        return $this->repository->findWithAgents($helpdesk->id);
    }

    // ── Eliminación ───────────────────────────────────────────────────────────

    /**
     * Verifica que el helpdesk no tenga tickets activos antes de eliminar.
     * Si tiene tickets, lanza una excepción con mensaje legible.
     */
    public function delete(Helpdesk $helpdesk): void
    {
        if ($helpdesk->tickets()->exists()) {
            throw new \RuntimeException(
                'No se puede eliminar el helpdesk porque tiene tickets asociados.'
            );
        }

        $this->repository->delete($helpdesk);
    }
}

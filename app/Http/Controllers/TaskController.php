<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Status;
use App\Models\User;
use App\Models\Check;
use Illuminate\Http\Request;

use App\Exports\TasksExport;
use Maatwebsite\Excel\Facades\Excel;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        set_time_limit(60); // 60 segundos
        $query = Task::with(['status', 'assignee', 'creator', 'checks']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('created_by')) {
            $query->where('user_id', $request->created_by);
        }

        if ($request->filled('urgent')) {
            $query->where('urgent', $request->boolean('urgent'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $perPage = $request->input('per_page', 5);
        $tasks = $query->paginate($perPage)->withQueryString();

        $statuses = Status::orderBy('order')->get();
        $users = User::orderBy('name')->get(['id', 'name']);

        // Definir columnas
        $columns = [
            ['field' => 'id', 'label' => 'ID', 'width' => '60'],
            ['field' => 'title', 'label' => 'Título'],
            ['field' => 'description', 'label' => 'Descripción', 'format' => 'text_limit', 'limit' => 50],
            [
                'field' => 'status',
                'label' => 'Estado',
                'callback' => function ($task) {
                    $color = $task->status->color ?? '#6c757d';
                    $name = $task->status->name ?? 'Sin estado';
                    return "<span class='badge' style='background-color: {$color}'>{$name}</span>";
                }
            ],
            [
                'field' => 'assignee_name',
                'label' => 'Asignado a',
                'format' => 'text_muted'
            ],
            [
                'field' => 'creator_name',
                'label' => 'Creado por',
                'format' => 'text_muted'
            ],
            [
                'field' => 'created_at',
                'label' => 'Fecha creación',
                'format' => 'text_muted'
            ],
            [
                'field' => 'urgent',
                'label' => 'Urgente',
                'format' => 'badge_boolean',
                'align' => 'center'
            ],
            [
                'field' => 'progress',
                'label' => 'Progreso',
                'callback' => function ($task) {
                    $total = $task->checks->count();
                    $completed = $task->checks->where('completed', true)->count();
                    $percent = $total > 0 ? round(($completed / $total) * 100) : 0;
                    return "<div class='progress' style='height: 10px;'>
                                <div class='progress-bar bg-success' style='width: {$percent}%'></div>
                            </div>
                            <small class='text-muted'>{$completed}/{$total}</small>";
                }
            ],
        ];

        $actions = [
            [
                'label' => 'Ver',
                'route' => 'tasks.show',
                'parameter' => 'task',
                'icon'  => 'eye'
            ],
            [
                'label' => 'Editar',
                'route' => 'tasks.edit',
                'parameter' => 'task',
                'icon'  => 'pencil'
            ],
            [
                'label' => 'Eliminar',
                'route' => 'tasks.destroy',
                'parameter' => 'task',
                'method' => 'DELETE',
                'icon'  => 'trash',
                'class' => 'text-danger'
            ],
        ];

        // Filtros avanzados
        $filters = [
            [
                'name' => 'status_id',
                'label' => 'Estado',
                'type' => 'select',
                'options' => $statuses->pluck('name', 'id')->toArray(),
                'placeholder' => 'Todos los estados'
            ],
            [
                'name' => 'assigned_to',
                'label' => 'Asignado a',
                'type' => 'select',
                'options' => $users->pluck('name', 'id')->toArray(),
                'placeholder' => 'Todos'
            ],
            [
                'name' => 'created_by',
                'label' => 'Creado por',
                'type' => 'select',
                'options' => $users->pluck('name', 'id')->toArray(),
                'placeholder' => 'Todos'
            ],
            [
                'name' => 'urgent',
                'label' => 'Urgente',
                'type' => 'select',
                'options' => ['1' => 'Sí', '0' => 'No'],
                'placeholder' => 'Todos'
            ],
            [
                'name' => 'date_from',
                'label' => 'Fecha desde',
                'type' => 'date'
            ],
            [
                'name' => 'date_to',
                'label' => 'Fecha hasta',
                'type' => 'date'
            ],
        ];

        return view('tasks.index', compact('tasks', 'columns', 'actions', 'filters', 'statuses', 'users'));
    }

    public function create()
    {
        $statuses = Status::orderBy('order')->get();
        $users = User::orderBy('name')->get(['id', 'name']);
        return view('tasks.create', compact('statuses', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status_id' => 'required|exists:statuses,id',
            'assigned_to' => 'nullable|exists:users,id',
            'urgent' => 'boolean',
            'checks' => 'nullable|array',
            'checks.*.description' => 'required|string|max:255',
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['urgent'] = $request->boolean('urgent');

        $task = Task::create($validated);

        // Crear los checks asociados
        if ($request->has('checks')) {
            foreach ($request->checks as $index => $checkData) {
                $task->checks()->create([
                    'description' => $checkData['description'],
                    'completed' => false,
                    'order' => $index + 1,
                ]);
            }
        }

        return redirect()->route('tasks.index')->with('success', 'Tarea creada correctamente.');
    }

    public function show(Task $task)
    {
        $task->load(['status', 'assignee', 'creator', 'checks']);
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $task->load('checks'); // o ya viene cargada si usas route model binding con with?
        $statuses = Status::orderBy('order')->get();
        $users = User::orderBy('name')->get(['id', 'name']);
        return view('tasks.edit', compact('task', 'statuses', 'users'));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status_id' => 'required|exists:statuses,id',
            'assigned_to' => 'nullable|exists:users,id',
            'urgent' => 'boolean',
            'checks' => 'nullable|array',
            'checks.*.description' => 'required_without:checks.*.delete|string|max:255', // solo requerido si no se elimina
            'checks.*.completed' => 'nullable|boolean',
            'checks.*.id' => 'nullable|exists:checks,id',
            'checks.*.delete' => 'nullable|boolean',
        ]);

        $validated['urgent'] = $request->boolean('urgent');
        $task->update($validated);

        $checksData = $request->input('checks', []);

        foreach ($checksData as $checkData) {
            // 1. Si el check está marcado para eliminar
            if (!empty($checkData['delete'])) {
                if (!empty($checkData['id'])) {
                    $task->checks()->where('id', $checkData['id'])->delete();
                }
                continue;
            }

            // 2. Si tiene ID → actualizar check existente
            if (!empty($checkData['id'])) {
                $check = $task->checks()->find($checkData['id']);
                if ($check) {
                    $check->update([
                        'description' => $checkData['description'],
                        'completed' => !empty($checkData['completed']),
                    ]);
                }
            }
            // 3. Si no tiene ID → crear nuevo check
            else {
                $maxOrder = $task->checks()->max('order') ?? 0;
                $task->checks()->create([
                    'description' => $checkData['description'],
                    'completed' => !empty($checkData['completed']),
                    'order' => $maxOrder + 1,
                ]);
            }
        }

        return redirect()->route('tasks.index')->with('success', 'Tarea actualizada correctamente.');
    }


    public function destroy(Task $task)
    {
        // Opcional: eliminar relaciones como checks
        $task->checks()->delete(); // si quieres eliminar también los checks asociados
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Tarea eliminada correctamente.');
    }

    public function export(Request $request)
    {
        $filters = $request->all();
        $columnConfig = json_decode($request->input('column_config', '{}'), true);

        return Excel::download(new TasksExport($filters, $columnConfig), 'tareas.xlsx');
    }
}

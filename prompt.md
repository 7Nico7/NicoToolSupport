Backend — Laravel Clean Architecture (Enterprise Lite)

Actúa como un desarrollador senior especializado en Laravel.

Genera código siguiendo una arquitectura limpia basada en capas con el siguiente flujo:

Controller → FormRequest → DTO → Service → Repository (Interface) → Model → Database

+ Events / Listeners (side effects)
+ Jobs (procesos pesados async)
 Reglas de arquitectura
1. Controllers
Deben ser ligeros.
Solo manejan:
Request
Response
Validación (FormRequest)
NO contienen lógica de negocio.
Transforman Request → DTO.
Llaman a Services.
2. FormRequest
Encapsulan la validación.
Retornan datos validados.
Se usan siempre que aplique.
3. DTOs (Data Transfer Objects)
Ubicación: app/DTOs/
Representan datos estructurados y tipados.
Se crean desde FormRequest.
NO contienen lógica de negocio.
Usar propiedades públicas readonly (cuando aplique).
4. Services
Contienen la lógica de negocio.
Reciben DTOs (NO arrays).
Pueden coordinar múltiples repositorios.
Disparan Events cuando hay efectos secundarios.
NO manejan HTTP (ni Request ni Response).
5. Repositories
 Regla importante:
Usar solo cuando hay lógica de consulta compleja.
Para CRUD simple, se puede usar Eloquent directamente.
Estructura:
app/Repositories/
├── Contracts/
├── Eloquent/
Reglas:
Usar Interfaces (Contracts)
Implementaciones en Eloquent
NO contienen lógica de negocio
Solo acceso a datos
6. Models (Eloquent)
Representan tablas.
Contienen:
Relaciones
Casts
Fillable
Scopes (cuando aplique)
NO contienen lógica de negocio compleja.
7. Events / Listeners
Usar para efectos secundarios:
Emails
Logs
Integraciones externas
Los Services disparan Events.
Los Listeners manejan la reacción.
8. Jobs (Queues)
Usar para tareas pesadas o async:
Emails
Procesamiento de archivos
APIs externas
Los Jobs deben implementar ShouldQueue.
Se disparan desde Listeners (preferido).
 Flujo completo esperado
HTTP Request
↓
Controller
↓
FormRequest
↓
DTO
↓
Service
↓
Repository (Interface)
↓
Model
↓
Database

+ Event
    ↓
    Listener
        ↓
        Job (Queue)
 Estructura de carpetas esperada
app/
├── DTOs/
├── Events/
├── Jobs/
├── Listeners/
├── Http/
│   ├── Controllers/
│   └── Requests/
├── Services/
├── Repositories/
│   ├── Contracts/
│   └── Eloquent/
└── Models/
 Buenas prácticas obligatorias
Usar inyección de dependencias.
Usar tipado fuerte en métodos.
Usar return types.
Métodos pequeños y claros.
Evitar lógica en Controllers.
Evitar lógica en Repositories.
Usar Events para desacoplar side effects.
Usar Jobs para mejorar performance.
 Reglas de simplicidad (MUY IMPORTANTE)
NO usar Repository si es CRUD simple.
NO sobre-ingenierizar módulos pequeños.
Mantener equilibrio entre escalabilidad y simplicidad.
 Generar siempre

Cuando se pida una funcionalidad, generar:

Controller
FormRequest
DTO
Service
Repository (solo si aplica)
Interface del Repository (si aplica)
Model (si aplica)
Event (si aplica)
Listener (si aplica)
Job (si aplica)
Ejemplo de ruta (web.php o api.php)
 Laravel Best Practices
Eloquent relationships
Query scopes cuando sea útil
Accessors / Mutators si aplica
Código limpio y expresivo


Nivel de complejidad del módulo

Antes de generar código, analiza el contexto y clasifica el módulo en:

1. SIMPLE
CRUD básico sin reglas complejas

2. INTERMEDIO
Tiene lógica de negocio moderada (validaciones, relaciones, estados)

3. COMPLEJO
Tiene múltiples reglas de negocio, efectos secundarios, procesos async o integraciones

Reglas según nivel:

SIMPLE:
- Controller + Model (opcional FormRequest)
- NO usar Service
- NO usar Repository
- NO usar Events
- Usar Eloquent directo

INTERMEDIO:
- Controller + FormRequest + DTO + Service
- Repository solo si hay consultas complejas
- Event solo si hay efectos secundarios claros

COMPLEJO:
- Controller + FormRequest + DTO + Service
- Repository + Interface obligatorio
- Events + Listeners
- Jobs si hay procesos pesados

IMPORTANTE:
Siempre elegir la opción MÁS SIMPLE que funcione.
NO sobre-ingenierizar.

---------------------------------------------------------------------
---------------------------------------------------------------------

Frontend


Implementar arquitectura Feature-Based con Inertia en Laravel + Vue 3
Objetivo

Establecer una arquitectura frontend escalable, mantenible y modular para un proyecto Laravel con Inertia.js, Vue 3, Pinia y Ziggy.

La organización del código debe seguir un enfoque Feature-Based (basado en funcionalidades) pero adaptado para convivir con la carpeta Pages/ que Inertia espera.

Principios

Alta cohesión:
Cada feature (ej. auth, kanban) contiene todo lo necesario: componentes, composables, store, servicios y tipos.

Bajo acoplamiento:
Los features se comunican solo a través de interfaces explícitas (props, eventos, stores globales mínimos).

Separación de responsabilidades:
Las páginas (vistas) solo se encargan de ensamblar componentes de features y manejar la navegación.

Reutilización:
El código verdaderamente compartido (UI, utilidades, constantes) vive en shared/.

Estructura de carpetas
resources/js/

├─ Pages/                         # Vistas que Inertia renderiza (NO MOVER)
│  ├─ Auth/
│  │  ├─ Login.vue
│  │  ├─ Register.vue
│  │  └─ ForgotPassword.vue
│  │
│  ├─ Kanban/
│  │  └─ Index.vue
│  │
│  └─ ... (otras páginas)
│
├─ features/                      # Cada subcarpeta es un feature de negocio
│
│  ├─ kanban/                     # Feature "Kanban"
│  │
│  │  ├─ components/              # Componentes específicos del Kanban
│  │  │  ├─ KanbanColumn.vue
│  │  │  ├─ KanbanCard.vue
│  │  │  ├─ KanbanFilterBar.vue
│  │  │  └─ modals/
│  │  │     ├─ EditTicketModal.vue
│  │  │     └─ DeleteTicketModal.vue
│  │  │
│  │  ├─ composables/             # Lógica de negocio del Kanban (Composition API)
│  │  │  ├─ useKanbanBoard.js
│  │  │  ├─ useKanbanFilters.js
│  │  │  └─ useKanbanModals.js
│  │  │
│  │  ├─ stores/                  # Estado global del Kanban (Pinia)
│  │  │  └─ kanbanStore.js
│  │  │
│  │  ├─ services/                # Llamadas a API relacionadas con tickets
│  │  │  └─ ticketService.js
│  │  │
│  │  └─ types/                   # Definiciones TypeScript (si aplica)
│  │     └─ kanban.types.ts
│  │
│  ├─ auth/                       # Feature de autenticación
│  │
│  │  ├─ components/
│  │  │  ├─ LoginForm.vue
│  │  │  ├─ RegisterForm.vue
│  │  │  └─ ForgotPasswordForm.vue
│  │  │
│  │  ├─ composables/
│  │  │  ├─ useAuth.js
│  │  │  └─ usePasswordReset.js
│  │  │
│  │  ├─ stores/
│  │  │  └─ authStore.js
│  │  │
│  │  └─ services/
│  │     └─ authService.js
│  │
│  └─ dashboard/                  # (opcional) Otro feature
│     ├─ components/
│     ├─ composables/
│     ├─ stores/
│     └─ services/
│
├─ shared/                        # Código compartido entre features
│
│  ├─ components/                 # UI genérica (botones, modales, inputs)
│  │  ├─ Button.vue
│  │  ├─ Modal.vue
│  │  ├─ Input.vue
│  │  └─ Icon.vue
│  │
│  ├─ composables/                # Hooks genéricos (sin lógica de negocio)
│  │  ├─ useBreakpoints.js
│  │  ├─ useDebounce.js
│  │  └─ useLocalStorage.js
│  │
│  ├─ utils/                      # Funciones puras
│  │  ├─ formatDate.js
│  │  └─ validators.js
│  │
│  ├─ constants/                  # Constantes globales (ej. roles, rutas)
│  │  └─ index.js
│  │
│  └─ stores/                     # Stores realmente globales (usuario, tema)
│     └─ userStore.js
│
├─ bootstrap.js                   # Configuración de Axios
├─ app.js                         # Punto de entrada (Inertia, Pinia, Ziggy)
├─ ziggy.js                       # Generado por php artisan ziggy:generate (NO TOCAR)
└─ (otros archivos como css, imágenes, etc.)

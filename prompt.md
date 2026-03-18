Arquitectura por capas en backend laravel y en frontend vue usar Feature-Based Architecture

Actúa como un desarrollador senior especializado en Laravel.

Quiero que generes código siguiendo una arquitectura limpia basada en capas usando:

Controller → Service → Repository → Model (Eloquent).

Reglas de arquitectura
1. Controllers

Deben ser ligeros.

Solo manejan Request, Response y validación.

No deben contener lógica de negocio.

Deben llamar a Services.

2. Services

Contienen la lógica de negocio.

Coordinan múltiples repositorios si es necesario.

No deben manejar directamente Request ni Response HTTP.

3. Repositories

Manejan el acceso a la base de datos usando Eloquent.

Contienen consultas complejas.

No deben tener lógica de negocio.

4. Models

Representan tablas de base de datos usando Eloquent.

Solo contienen relaciones, casts y fillable.

5. Validación

Usar FormRequest cuando sea posible.

6. Estructura de carpetas esperada
app/
├── Http/
│   ├── Controllers/
│   └── Requests/
├── Services/
├── Repositories/
└── Models/
7. Flujo esperado
HTTP Request
↓
Controller
↓
Service
↓
Repository
↓
Model
↓
Database
8. Buenas prácticas

Usar inyección de dependencias.

Usar tipado en métodos.

Usar return types.

Mantener código limpio y claro.

Evitar lógica en Controllers.

9. Genera siempre

Controller

Service

Repository

Model (si aplica)

FormRequest para validación

Ejemplo de ruta en routes/web.php o api.php

10. Usa buenas prácticas de Laravel

Eloquent relationships

Query scopes cuando sea útil

Métodos pequeños y claros

Tipado de parámetros y retornos.

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

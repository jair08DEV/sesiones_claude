# sesiones_claude — Gestión de Proyectos y Tareas

Aplicación web desarrollada con **Laravel 8** y **Bootstrap 5.3** para gestionar proyectos y sus tareas internas, con métricas visuales, historial de actividad automático y exportación de datos.

---

## Tecnologías

| Tecnología | Versión | Uso |
|------------|---------|-----|
| Laravel | 8.x | Framework PHP backend |
| PHP | 7.4+ | Lenguaje del servidor |
| Bootstrap | 5.3.3 (CDN) | UI y componentes visuales |
| Chart.js | 4.4.3 (CDN) | Gráficos del dashboard |
| Laragon | — | Entorno de desarrollo local (Windows 11) |
| MySQL | — | Base de datos |

> No se utiliza `npm run dev`. Todos los assets frontend se sirven desde CDN.

---

## Instalación

```bash
# 1. Clonar el repositorio
git clone <url-del-repo>
cd sesiones_claude

# 2. Instalar dependencias PHP
composer install

# 3. Copiar el archivo de entorno
cp .env.example .env

# 4. Generar la clave de la aplicación
php artisan key:generate

# 5. Configurar la base de datos en .env
DB_DATABASE=sesiones_claude
DB_USERNAME=root
DB_PASSWORD=

# 6. Ejecutar las migraciones
php artisan migrate
```

---

## Estructura de la base de datos

### Tabla `projects`
| Columna | Tipo | Descripción |
|---------|------|-------------|
| `id` | bigint PK | Identificador |
| `name` | varchar | Nombre del proyecto |
| `description` | text (nullable) | Descripción opcional |
| `deadline` | date | Fecha límite |
| `created_at / updated_at` | timestamp | Marcas de tiempo |

### Tabla `tasks`
| Columna | Tipo | Descripción |
|---------|------|-------------|
| `id` | bigint PK | Identificador |
| `project_id` | FK → projects | Proyecto al que pertenece |
| `title` | varchar | Título de la tarea |
| `description` | text (nullable) | Descripción opcional |
| `priority` | enum | `alta` / `media` / `baja` |
| `status` | enum | `backlog` / `en_progreso` / `testing` / `terminada` |
| `estimated_time` | smallint (nullable) | Cantidad de tiempo estimado |
| `estimated_unit` | enum (nullable) | `minutos` / `horas` / `dias` |
| `created_at / updated_at` | timestamp | Marcas de tiempo |

### Tabla `activity_logs`
| Columna | Tipo | Descripción |
|---------|------|-------------|
| `id` | bigint PK | Identificador |
| `project_id` | FK → projects | Proyecto relacionado |
| `task_id` | FK → tasks (nullable) | Tarea relacionada (null si fue eliminada) |
| `user_id` | FK → users (nullable) | Usuario que realizó la acción |
| `action` | enum | `created` / `updated` / `deleted` |
| `field` | varchar (nullable) | Campo que fue modificado |
| `old_value` | text (nullable) | Valor anterior al cambio |
| `new_value` | text (nullable) | Valor nuevo tras el cambio |
| `created_at / updated_at` | timestamp | Marcas de tiempo |

---

## Rutas disponibles

Todas las rutas requieren autenticación excepto `/` y las de auth.

### Proyectos
| Método | URI | Nombre | Descripción |
|--------|-----|--------|-------------|
| GET | `/home` | `home` | Dashboard con métricas y listado de proyectos |
| GET | `/projects/create` | `projects.create` | Formulario para crear un proyecto |
| POST | `/projects` | `projects.store` | Guardar nuevo proyecto |
| GET | `/projects/{project}` | `projects.show` | Detalle del proyecto con sus tareas |
| GET | `/projects/{project}/edit` | `projects.edit` | Formulario para editar el proyecto |
| PUT | `/projects/{project}` | `projects.update` | Guardar cambios del proyecto |
| GET | `/projects/{project}/activity` | `projects.activity` | Historial de actividad del proyecto |
| GET | `/projects/{project}/export?format=excel\|csv` | `projects.export` | Descargar exportación del proyecto |

### Tareas
| Método | URI | Nombre | Descripción |
|--------|-----|--------|-------------|
| POST | `/projects/{project}/tasks` | `tasks.store` | Crear nueva tarea en un proyecto |
| GET | `/projects/{project}/tasks/{task}/edit` | `tasks.edit` | Formulario para editar una tarea |
| PUT | `/projects/{project}/tasks/{task}` | `tasks.update` | Guardar cambios de la tarea |
| PATCH | `/projects/{project}/tasks/{task}/advance` | `tasks.advance` | Avanzar al siguiente estado |
| DELETE | `/projects/{project}/tasks/{task}` | `tasks.destroy` | Eliminar tarea |

---

## Funcionalidades implementadas

### Dashboard con métricas (`/home`)

Vista principal del sistema. Incluye:

- **4 tarjetas de resumen:** total de proyectos, terminados, en progreso y vencidos.
- **Gráfico de dona** (Chart.js): distribución global de todas las tareas por estado.
- **Gráfico de barras agrupadas** (Chart.js): por proyecto muestra total de tareas, tareas con estimado y tareas terminadas. El tooltip incluye el porcentaje de avance.
- **Tabla de proyectos:** nombre, descripción, fecha límite, conteo de tareas, barra de progreso porcentual, badge de estado y menú de acciones.
- Los gráficos se cargan (CDN Chart.js) únicamente cuando existe al menos una tarea registrada.

---

### Gestión de proyectos (CRUD)

**Crear proyecto** (`/projects/create`)
Formulario con campos: nombre (requerido), descripción (opcional) y fecha límite (requerido).

**Ver proyecto** (`/projects/{project}`)
- Encabezado con nombre, descripción, fecha límite, conteo de tareas y botón de edición.
- Formulario lateral para añadir nuevas tareas.
- Listado de tareas con acciones: avanzar estado, editar y eliminar.

**Editar proyecto** (`/projects/{project}/edit`)
Permite modificar nombre, descripción y fecha límite. Los campos se pre-cargan con los valores actuales.

---

### Gestión de tareas (CRUD)

Cada tarea pertenece a un proyecto y tiene los siguientes campos:

| Campo | Tipo | Valores |
|-------|------|---------|
| Título | texto | Requerido |
| Descripción | texto | Opcional |
| Prioridad | enum | `alta` / `media` / `baja` |
| Estado | enum | `backlog` / `en_progreso` / `testing` / `terminada` |
| Estimación | número + unidad | minutos / horas / días (opcional) |

**Flujo de estados:** las tareas avanzan en orden secuencial (`backlog` → `en_progreso` → `testing` → `terminada`) mediante el botón de avance. Al llegar a `terminada` el botón cambia a "✓ Terminar" y la tarjeta aparece atenuada.

**Editar tarea** (`/projects/{project}/tasks/{task}/edit`)
Permite modificar todos los campos. Los valores actuales se pre-cargan, incluyendo la unidad de estimación seleccionada.

---

### Historial de actividad (`/projects/{project}/activity`)

Log cronológico **completamente automático** de todos los cambios ocurridos en las tareas de un proyecto. Implementado mediante un **Laravel Observer** (`TaskObserver`) registrado en `AppServiceProvider`, sin necesidad de código manual en los controladores.

**Eventos que se registran automáticamente:**

| Evento | Información guardada |
|--------|---------------------|
| Tarea creada | Nombre de la tarea |
| Tarea eliminada | Nombre de la tarea (preservado aunque se borre el registro) |
| Campo actualizado | Nombre del campo + valor anterior + valor nuevo |

**Campos trackeados en actualizaciones:** `title`, `description`, `priority`, `status`, `estimated_time`, `estimated_unit`.

**Vista del historial:**
- Línea de tiempo vertical con punto de color por tipo de acción (verde = creada, rojo = eliminada, azul = actualizada).
- Nombre de la tarea afectada y campo modificado.
- Badges con valor anterior → valor nuevo (con etiquetas legibles para estados y prioridades).
- Tiempo relativo ("hace 5 minutos") y fecha/hora exacta.

---

### Exportar proyecto (`/projects/{project}/export`)

Desde el dashboard, cada proyecto tiene un menú dropdown con la opción **Exportar**. Al hacer clic se abre una **modal Bootstrap** donde el usuario elige el formato de descarga.

**Formato Excel (`.xls`)**
- Generado con **SpreadsheetML XML** — sin paquetes Composer externos.
- **Hoja 1 — Proyecto:** nombre, descripción, fecha límite, fecha de creación y conteos por estado (total, terminadas, en progreso, testing, backlog, con estimación).
- **Hoja 2 — Tareas:** una fila por tarea con todos sus campos (título, descripción, estado, prioridad, estimación, unidad, fecha de creación).
- Encabezados con fondo azul (`#0D6EFD`) y texto blanco en negrita.

**Formato CSV (`.csv`)**
- Generado con `fputcsv` nativo de PHP, sin paquetes externos.
- Sección 1: información general del proyecto.
- Fila vacía de separación.
- Sección 2: listado completo de tareas con cabeceras.

El nombre del archivo sigue el patrón: `{slug-del-proyecto}_{AAAAMMDD}.xls|csv`.

---

### Navegación y layout

**Top navbar**
Siempre visible. Muestra el nombre de la aplicación a la izquierda y el botón de cerrar sesión a la derecha.

**Sidebar izquierdo** (sticky, visible desde `md` en adelante)

| Sección | Links |
|---------|-------|
| Menú | Dashboard · Nuevo proyecto |
| Proyecto actual | Tareas · Historial (visibles solo dentro de un proyecto) |

- Los links se marcan como activos (`active`) según la ruta actual usando `request()->routeIs()`.
- La sección "Proyecto actual" aparece solo cuando hay un proyecto en contexto (`projects.show`, `projects.edit`, `projects.activity`, `tasks.edit`).
- En pantallas menores a 768 px el sidebar se oculta. El logout sigue accesible en el top navbar.

---

## Archivos principales del proyecto

```
app/
├── Http/Controllers/
│   ├── HomeController.php        # Dashboard y estadísticas globales
│   ├── ProjectController.php     # CRUD proyectos, actividad y exportación
│   └── TaskController.php        # CRUD tareas y avance de estado
├── Models/
│   ├── Project.php               # Relaciones: tasks(), activityLogs()
│   ├── Task.php                  # Flujo de estados con nextStatus()
│   └── ActivityLog.php           # Modelo del historial de actividad
├── Observers/
│   └── TaskObserver.php          # Registro automático de cambios en tareas
└── Providers/
    └── AppServiceProvider.php    # Registro del observer en boot()

database/migrations/
├── ..._create_projects_table.php
├── ..._create_tasks_table.php
├── ..._add_estimation_to_tasks_table.php
└── ..._create_activity_logs_table.php

resources/views/
├── layouts/
│   └── app.blade.php             # Layout principal (top navbar + sidebar)
├── home.blade.php                # Dashboard con métricas, gráficos y tabla
├── projects/
│   ├── create.blade.php          # Formulario nuevo proyecto
│   ├── edit.blade.php            # Formulario editar proyecto
│   ├── show.blade.php            # Detalle del proyecto + gestión de tareas
│   └── activity.blade.php        # Historial de actividad (timeline)
└── tasks/
    └── edit.blade.php            # Formulario editar tarea
```

---

## Decisiones técnicas destacadas

- **Sin npm:** todos los assets (Bootstrap, Chart.js) se sirven desde CDN para simplificar el entorno de desarrollo.
- **Chart.js bajo demanda:** el CDN solo se incluye en las vistas que lo necesitan mediante `@push('scripts')` / `@stack('scripts')`, evitando carga innecesaria.
- **Observer vs logs manuales:** el historial usa un Observer de Laravel en lugar de escribir logs en cada controlador, manteniendo el código desacoplado y sin riesgo de olvidar registrar un cambio.
- **SpreadsheetML para Excel:** permite generar archivos `.xls` con múltiples hojas reales sin instalar ningún paquete Composer (solo XML puro).
- **Modal por proyecto:** el modal de exportación se genera dentro del `@foreach` con IDs únicos (`#exportModal-{id}`) y URLs hardcodeadas directamente en el `href`, eliminando la dependencia de JavaScript para inyectar rutas dinámicamente.

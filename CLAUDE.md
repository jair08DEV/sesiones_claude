# CLAUDE.md — Proyecto: sesiones_claude

## Contexto del proyecto
- **Framework:** Laravel 8 (PHP)
- **Entorno:** Laragon (Windows 11)
- **Ruta:** `C:\laragon\www\sesiones_claude`
- **Bootstrap:** 5.3.3 vía CDN (en `layouts/app.blade.php`)
- **Auth:** Scaffolding de Laravel UI ya configurado

## Arquitectura actual

### Modelos
| Modelo | Tabla | Relaciones |
|--------|-------|-----------|
| `Project` | `projects` | `hasMany(Task)` |
| `Task` | `tasks` | `belongsTo(Project)` |

### Tablas
| Tabla | Columnas |
|-------|---------|
| `projects` | `id`, `name`, `description` (nullable), `deadline` (date), `timestamps` |
| `tasks` | `id`, `project_id` (FK), `title`, `description` (nullable), `priority` (enum: alta/media/baja), `status` (enum: backlog/en_progreso/testing/terminada), `timestamps` |

### Rutas protegidas (`auth`)
| Método | URI | Controlador | Nombre |
|--------|-----|-------------|--------|
| GET | `/home` | `HomeController@index` | `home` |
| GET | `/projects/create` | `ProjectController@create` | `projects.create` |
| POST | `/projects` | `ProjectController@store` | `projects.store` |
| GET | `/projects/{project}` | `ProjectController@show` | `projects.show` |
| POST | `/projects/{project}/tasks` | `TaskController@store` | `tasks.store` |
| DELETE | `/projects/{project}/tasks/{task}` | `TaskController@destroy` | `tasks.destroy` |

### Vistas
| Vista | Descripción |
|-------|-------------|
| `home.blade.php` | Dashboard con stats reales, tabla de proyectos y empty state |
| `projects/create.blade.php` | Formulario para crear proyecto |
| `projects/show.blade.php` | Detalle de proyecto + formulario y listado de tareas |
| `layouts/app.blade.php` | Layout principal con navbar y Bootstrap CDN |

---

## Backlog de peticiones

> Estado: `[ ]` Pendiente · `[~]` En progreso · `[x]` Completado
> **Prioridad:** Alta = bloqueante · Media = importante · Baja = nice-to-have
> **Dificultad:** Fácil = < 30 min · Media = horas · Difícil = días

### Prioridad Alta

| # | Petición | Dificultad | Estado | Fecha |
|---|----------|-----------|--------|-------|
| 6 | Dashboard de métricas del proyecto (gráficos por estado, horas, avance) | Media | `[ ]` | 2026-03-25 |
| 7 | Historial de actividad (log de cambios en tareas y proyectos) | Difícil | `[ ]` | 2026-03-25 |
| 8 | Exportar proyecto a JSON/CSV | Fácil | `[ ]` | 2026-03-25 |

### Prioridad Media

| # | Petición | Dificultad | Estado | Fecha |
|---|----------|-----------|--------|-------|
| 1 | Crear CLAUDE.md con backlog organizado | Fácil | `[x]` | 2026-02-17 |
| 2 | Dashboard con botón a formulario de proyecto | Fácil | `[x]` | 2026-02-17 |
| 3 | Implementar Bootstrap vía CDN | Fácil | `[x]` | 2026-02-17 |
| 4 | Backend + migraciones para proyectos y tareas | Media | `[x]` | 2026-02-17 |
| 5 | Listar proyectos en /home con empty state | Fácil | `[x]` | 2026-02-17 |

### Prioridad Baja

| # | Petición | Dificultad | Estado | Fecha |
|---|----------|-----------|--------|-------|
| — | *Sin peticiones de baja prioridad aún* | — | — | — |

---

## Historial de peticiones completadas

| # | Petición | Dificultad | Fecha completado |
|---|----------|-----------|-----------------|
| 1 | Crear CLAUDE.md con backlog organizado | Fácil | 2026-02-17 |
| 2 | Dashboard básico con botón a formulario de proyecto | Fácil | 2026-02-17 |
| 3 | Rediseño del dashboard y formulario con Bootstrap 5 | Fácil | 2026-02-17 |
| 4 | Bootstrap vía CDN en layout principal | Fácil | 2026-02-17 |
| 5 | Migraciones `projects` y `tasks`, modelos con relaciones, controladores CRUD, rutas y vista show con formulario de tareas | Media | 2026-02-17 |
| 6 | Backend HomeController para obtener proyectos reales, listado con tabla y empty state en /home | Fácil | 2026-02-17 |

---

---

## Plan Spec-Driven con Brainstorm

> Metodología: cada funcionalidad se define con su **spec** (qué debe hacer, qué datos necesita, qué cambios implica) y un **brainstorm** de enfoques posibles antes de escribir código.

---

### Feature 6 — Dashboard de métricas del proyecto

#### Spec
- Vista accesible desde `projects/show` (ej. tab o sección inferior).
- Muestra:
  - Total de tareas agrupadas por estado (`backlog`, `en_progreso`, `testing`, `terminada`).
  - Horas estimadas vs. horas completadas por proyecto.
  - Porcentaje de avance general (tareas `terminada` / total tareas × 100).
- Usa gráficos visuales (barras o donas).
- Sin `npm run dev`; librerías de gráficos vía CDN.

#### Cambios en BD necesarios
- Agregar columnas a `tasks`: `estimated_hours` (decimal, nullable) y `actual_hours` (decimal, nullable).
- Nueva migración: `add_hours_to_tasks_table`.

#### Brainstorm de enfoques

| Enfoque | Pros | Contras | Decisión |
|---------|------|---------|----------|
| **Chart.js** vía CDN | Popular, fácil, CDN disponible, donas y barras nativas | Requiere JS config | **Elegido** |
| ApexCharts vía CDN | Más visual, animaciones | CDN más pesado | Descartado |
| CSS puro (barras con `width %`) | Sin dependencias JS | No es un gráfico real | Solo como fallback |

#### Ruta propuesta
```
GET /projects/{project}/metrics  → ProjectController@metrics
```
O renderizar directamente en `projects/show` pasando stats desde el controlador.

#### Archivos a tocar
- `database/migrations/` — nueva migración `add_hours_to_tasks_table`
- `app/Models/Task.php` — agregar `estimated_hours`, `actual_hours` al `$fillable`
- `app/Http/Controllers/ProjectController.php` — método `show` o `metrics` con agregados
- `resources/views/projects/show.blade.php` — sección de gráficos con Chart.js CDN
- `routes/web.php` — ruta opcional si es página separada
- `layouts/app.blade.php` — CDN de Chart.js (solo en la vista que lo necesite)

---

### Feature 7 — Historial de actividad

#### Spec
- Log cronológico visible en la vista del proyecto.
- Registra automáticamente:
  - Creación de tarea.
  - Cambio de estado (`status`).
  - Edición de título, descripción, prioridad, horas.
  - Eliminación de tarea.
- Cada entrada muestra: qué cambió, valor anterior → nuevo valor, fecha/hora.
- No requiere acción manual del usuario.

#### Cambios en BD necesarios
- Nueva tabla `activity_logs`:
  ```
  id, project_id (FK), task_id (FK nullable), user_id (FK nullable),
  action (enum: created/updated/deleted),
  field (varchar nullable), old_value (text nullable), new_value (text nullable),
  timestamps
  ```
- Nueva migración: `create_activity_logs_table`.

#### Brainstorm de enfoques

| Enfoque | Pros | Contras | Decisión |
|---------|------|---------|----------|
| **Laravel Observer** en `Task` | Automático, desacoplado, limpio | Hay que configurar el Observer | **Elegido** |
| Logs manuales en controladores | Simple de entender | Repetitivo, acoplado, propenso a olvidos | Descartado |
| Spatie Laravel Activity Log (paquete) | Full-featured | Dependencia externa, overkill | Descartado |

#### Archivos a tocar
- `database/migrations/` — `create_activity_logs_table`
- `app/Models/ActivityLog.php` — nuevo modelo
- `app/Observers/TaskObserver.php` — nuevo Observer (created, updated, deleted)
- `app/Providers/AppServiceProvider.php` — registrar el Observer
- `app/Http/Controllers/ProjectController.php` — pasar logs a la vista `show`
- `resources/views/projects/show.blade.php` — sección timeline de actividad

---

### Feature 8 — Exportar proyecto a JSON/CSV

#### Spec
- Botón "Exportar" en `projects/show`.
- El usuario elige formato (JSON o CSV) mediante dos botones o un `select`.
- El archivo descargado incluye:
  - Datos del proyecto (`name`, `description`, `deadline`).
  - Todas las tareas con sus campos (`title`, `description`, `priority`, `status`, `estimated_hours`, `actual_hours`).
- Sin paquetes extra; usar PHP nativo (`fputcsv` para CSV, `json_encode` para JSON).

#### Brainstorm de enfoques

| Enfoque | Pros | Contras | Decisión |
|---------|------|---------|----------|
| **Ruta GET con parámetro `?format=json\|csv`** | Un solo endpoint, limpio | Validar el param | **Elegido** |
| Dos rutas separadas (`/export/json`, `/export/csv`) | Más explícito | Duplica lógica | Descartado |
| Paquete Laravel Excel | Potente | Dependencia externa, innecesario | Descartado |

#### Ruta propuesta
```
GET /projects/{project}/export?format=json   → ProjectController@export
GET /projects/{project}/export?format=csv    → ProjectController@export
```

#### Archivos a tocar
- `app/Http/Controllers/ProjectController.php` — método `export`
- `routes/web.php` — nueva ruta `projects.export`
- `resources/views/projects/show.blade.php` — botones de exportación

---

## Notas y convenciones

- Cada vez que el usuario haga una nueva petición, se añade al backlog con prioridad y dificultad.
- Al completar una tarea, se mueve al historial.
- No usar `npm run dev`; los assets se sirven vía CDN de Bootstrap.
- Los enums de `tasks.priority`: `alta`, `media`, `baja`.
- Los enums de `tasks.status`: `backlog`, `en_progreso`, `testing`, `terminada`.

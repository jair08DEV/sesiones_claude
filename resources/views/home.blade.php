@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- Flash --}}
    @if (session('status') || session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') ?? session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Cabecera --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-0">Dashboard</h2>
            <p class="text-muted mb-0">Bienvenido, {{ Auth::user()->name }}</p>
        </div>
        <a href="{{ route('projects.create') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                 class="bi bi-plus-lg me-1" viewBox="0 0 16 16">
                <path d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
            </svg>
            Crear Proyecto
        </a>
    </div>

    {{-- Tarjetas de resumen --}}
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-primary bg-opacity-10">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#0d6efd" viewBox="0 0 16 16">
                            <path d="M2 6a6 6 0 1 1 10.174 4.31c-.203.196-.359.4-.453.619l-.762 1.769A.5.5 0 0 1 10.5 13h-5a.5.5 0 0 1-.46-.302l-.761-1.77a2 2 0 0 0-.453-.618A5.98 5.98 0 0 1 2 6m3 8.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1l-.224.447a1 1 0 0 1-.894.553H6.618a1 1 0 0 1-.894-.553L5.5 15a.5.5 0 0 1-.5-.5"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Total proyectos</p>
                        <h4 class="fw-bold mb-0">{{ $stats['total'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-success bg-opacity-10">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#198754" viewBox="0 0 16 16">
                            <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Terminados</p>
                        <h4 class="fw-bold mb-0">{{ $stats['terminados'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-warning bg-opacity-10">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#ffc107" viewBox="0 0 16 16">
                            <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/>
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">En progreso</p>
                        <h4 class="fw-bold mb-0">{{ $stats['en_progreso'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-danger bg-opacity-10">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#dc3545" viewBox="0 0 16 16">
                            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Vencidos</p>
                        <h4 class="fw-bold mb-0">{{ $stats['vencidos'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Métricas de tareas --}}
    @if ($taskStats['total'] > 0)
    <div class="row g-4 mb-4">

        {{-- Dona: distribución global de tareas por estado --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-semibold">Estado de tareas</h6>
                    <p class="text-muted small mb-0">{{ $taskStats['total'] }} tareas en total</p>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div style="max-width: 220px; width: 100%;">
                        <canvas id="chartEstados"></canvas>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 pb-3">
                    <div class="row text-center g-2">
                        <div class="col-6">
                            <span class="d-block fw-bold text-secondary">{{ $taskStats['backlog'] }}</span>
                            <span class="small text-muted">Backlog</span>
                        </div>
                        <div class="col-6">
                            <span class="d-block fw-bold text-warning">{{ $taskStats['en_progreso'] }}</span>
                            <span class="small text-muted">En progreso</span>
                        </div>
                        <div class="col-6">
                            <span class="d-block fw-bold text-info">{{ $taskStats['testing'] }}</span>
                            <span class="small text-muted">Testing</span>
                        </div>
                        <div class="col-6">
                            <span class="d-block fw-bold text-success">{{ $taskStats['terminadas'] }}</span>
                            <span class="small text-muted">Terminadas</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Barras: completas vs estimadas vs total por proyecto --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-semibold">Avance por proyecto</h6>
                    <p class="text-muted small mb-0">Tareas terminadas vs con estimado vs total</p>
                </div>
                <div class="card-body">
                    <canvas id="chartProyectos" style="max-height: 240px;"></canvas>
                </div>
            </div>
        </div>

    </div>
    @endif

    {{-- Listado de proyectos --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
            <h5 class="mb-0 fw-semibold">Mis proyectos</h5>
            @if ($projects->isNotEmpty())
                <span class="badge bg-primary rounded-pill">{{ $projects->count() }}</span>
            @endif
        </div>

        @if ($projects->isEmpty())
            {{-- Empty state --}}
            <div class="card-body text-center py-5 text-muted">
                <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" fill="currentColor"
                     class="bi bi-folder-plus mb-3 opacity-25" viewBox="0 0 16 16">
                    <path d="m.5 3 .04.87a2 2 0 0 0-.342 1.311l.637 7A2 2 0 0 0 2.826 14H9v-1H2.826a1 1 0 0 1-.995-.91l-.637-7A1 1 0 0 1 2.19 4h11.62a1 1 0 0 1 .996 1.09L14.54 8h1.005l.256-2.819A2 2 0 0 0 13.81 3H9.828a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 6.172 1H2.5a2 2 0 0 0-2 2m5.672-1a1 1 0 0 1 .707.293L7.586 3H2.19q-.362.002-.683.12L1.5 2.98a1 1 0 0 1 1-.98z"/>
                    <path d="M13.5 9a.5.5 0 0 1 .5.5V11h1.5a.5.5 0 1 1 0 1H14v1.5a.5.5 0 1 1-1 0V12h-1.5a.5.5 0 0 1 0-1H13V9.5a.5.5 0 0 1 .5-.5"/>
                </svg>
                <h6 class="fw-semibold mb-1">Sin proyectos aún</h6>
                <p class="small mb-3">Crea tu primer proyecto y empieza a organizar tus tareas.</p>
                <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm px-4">
                    Crear primer proyecto
                </a>
            </div>

        @else
            {{-- Tabla --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Proyecto</th>
                            <th>Fecha límite</th>
                            <th class="text-center">Tareas</th>
                            <th class="text-center">Progreso</th>
                            <th class="text-center">Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($projects as $project)
                            @php
                                $pct = $project->tasks_count > 0
                                    ? round(($project->completed_tasks_count / $project->tasks_count) * 100)
                                    : 0;
                                $isOverdue  = $project->deadline->isPast();
                                $isDone     = $project->tasks_count > 0 && $project->tasks_count === $project->completed_tasks_count;
                                $statusLabel = $isDone ? 'Terminado' : ($isOverdue ? 'Vencido' : 'Activo');
                                $statusClass = $isDone ? 'success' : ($isOverdue ? 'danger' : 'primary');
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <a href="{{ route('projects.show', $project) }}"
                                       class="fw-semibold text-decoration-none text-dark">
                                        {{ $project->name }}
                                    </a>
                                    @if ($project->description)
                                        <p class="text-muted small mb-0">
                                            {{ Str::limit($project->description, 60) }}
                                        </p>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    <span class="{{ $isOverdue && !$isDone ? 'text-danger fw-semibold' : 'text-muted' }}">
                                        {{ $project->deadline->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary rounded-pill">
                                        {{ $project->tasks_count }}
                                    </span>
                                </td>
                                <td style="min-width: 120px">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px">
                                            <div class="progress-bar bg-{{ $statusClass }}"
                                                 style="width: {{ $pct }}%"></div>
                                        </div>
                                        <span class="small text-muted">{{ $pct }}%</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }} border border-{{ $statusClass }} border-opacity-25">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group">
                                        <a href="{{ route('projects.show', $project) }}"
                                           class="btn btn-sm btn-outline-secondary">
                                            Ver
                                        </a>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('projects.show', $project) }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                                                         class="bi bi-eye me-2" viewBox="0 0 16 16">
                                                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                                                        <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                                                    </svg>
                                                    Ver proyecto
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('projects.edit', $project) }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                                                         class="bi bi-pencil me-2" viewBox="0 0 16 16">
                                                        <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                                                    </svg>
                                                    Editar proyecto
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item text-success fw-semibold"
                                                   href="#"
                                                   data-bs-toggle="modal"
                                                   data-bs-target="#exportModal-{{ $project->id }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                                                         class="bi bi-file-earmark-arrow-down me-2" viewBox="0 0 16 16">
                                                        <path d="M8.5 6.5a.5.5 0 0 0-1 0v3.793L6.354 9.146a.5.5 0 1 0-.708.708l2 2a.5.5 0 0 0 .708 0l2-2a.5.5 0 0 0-.708-.708L8.5 10.293z"/>
                                                        <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h3V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                                                    </svg>
                                                    Exportar
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                            {{-- Modal de exportación para este proyecto --}}
                            <div class="modal fade" id="exportModal-{{ $project->id }}"
                                 tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">

                                        <div class="modal-header border-0 pb-0">
                                            <div>
                                                <h5 class="modal-title fw-bold">Exportar proyecto</h5>
                                                <p class="text-muted small mb-0">{{ $project->name }}</p>
                                            </div>
                                            <button type="button" class="btn-close"
                                                    data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                        </div>

                                        <div class="modal-body py-4">
                                            <p class="text-muted small mb-4">
                                                Elige el formato para descargar la información del proyecto y sus tareas.
                                            </p>
                                            <div class="d-flex gap-3 justify-content-center flex-wrap">

                                                {{-- Excel --}}
                                                <a href="{{ route('projects.export', $project) }}?format=excel"
                                                   class="btn btn-success px-4 d-flex align-items-center gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                         fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h3V9H2V2a1 1 0 0 1 1-1h6.5zM2 10h4v2H2zm0 3h4v1H2zm5-3h2v2H7zm0 3h2v1H7zm3-3h4v2h-4zm0 3h4v1h-4z"/>
                                                    </svg>
                                                    <div class="text-start">
                                                        <span class="d-block fw-semibold">Excel</span>
                                                        <span class="d-block" style="font-size:.72rem;opacity:.85">2 hojas: proyecto + tareas</span>
                                                    </div>
                                                </a>

                                                {{-- CSV --}}
                                                <a href="{{ route('projects.export', $project) }}?format=csv"
                                                   class="btn btn-outline-secondary px-4 d-flex align-items-center gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                         fill="currentColor" viewBox="0 0 16 16">
                                                        <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h3V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                                                        <path d="M4.603 14.087a.8.8 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.7 7.7 0 0 1 1.482-.645 20 20 0 0 0 1.062-2.227 7.3 7.3 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27 1.134-.52 1.794a10.954 10.954 0 0 0 .98 1.686 5.753 5.753 0 0 1 1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.856.856 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.712 5.712 0 0 1-.911-.95 11.651 11.651 0 0 0-1.997.406 11.307 11.307 0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.793.793 0 0 1-.58.029"/>
                                                    </svg>
                                                    <div class="text-start">
                                                        <span class="d-block fw-semibold">CSV</span>
                                                        <span class="d-block" style="font-size:.72rem;opacity:.75">Proyecto + tareas en un archivo</span>
                                                    </div>
                                                </a>

                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')

@if ($taskStats['total'] > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function () {
    const palette = {
        backlog:     { bg: 'rgba(108,117,125,0.15)', border: 'rgba(108,117,125,1)' },
        en_progreso: { bg: 'rgba(255,193,7,0.15)',   border: 'rgba(255,193,7,1)'   },
        testing:     { bg: 'rgba(13,202,240,0.15)',  border: 'rgba(13,202,240,1)'  },
        terminada:   { bg: 'rgba(25,135,84,0.15)',   border: 'rgba(25,135,84,1)'   },
    };

    // --- Donut: estado de tareas global ---
    new Chart(document.getElementById('chartEstados'), {
        type: 'doughnut',
        data: {
            labels: ['Backlog', 'En progreso', 'Testing', 'Terminadas'],
            datasets: [{
                data: [
                    {{ $taskStats['backlog'] }},
                    {{ $taskStats['en_progreso'] }},
                    {{ $taskStats['testing'] }},
                    {{ $taskStats['terminadas'] }},
                ],
                backgroundColor: [
                    palette.backlog.border,
                    palette.en_progreso.border,
                    palette.testing.border,
                    palette.terminada.border,
                ],
                borderWidth: 0,
                hoverOffset: 6,
            }],
        },
        options: {
            cutout: '70%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.label}: ${ctx.parsed} tareas`,
                    },
                },
            },
        },
    });

    // --- Barras agrupadas: terminadas / con estimado / total por proyecto ---
    const metrics = @json($projectMetrics);

    new Chart(document.getElementById('chartProyectos'), {
        type: 'bar',
        data: {
            labels: metrics.map(p => p.name),
            datasets: [
                {
                    label: 'Total',
                    data: metrics.map(p => p.total),
                    backgroundColor: 'rgba(108,117,125,0.25)',
                    borderColor:     'rgba(108,117,125,0.8)',
                    borderWidth: 1,
                    borderRadius: 4,
                },
                {
                    label: 'Con estimado',
                    data: metrics.map(p => p.estimadas),
                    backgroundColor: 'rgba(13,110,253,0.25)',
                    borderColor:     'rgba(13,110,253,0.8)',
                    borderWidth: 1,
                    borderRadius: 4,
                },
                {
                    label: 'Terminadas',
                    data: metrics.map(p => p.terminadas),
                    backgroundColor: 'rgba(25,135,84,0.55)',
                    borderColor:     'rgba(25,135,84,1)',
                    borderWidth: 1,
                    borderRadius: 4,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                x: { grid: { display: false } },
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, precision: 0 },
                    grid: { color: 'rgba(0,0,0,0.05)' },
                },
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, padding: 16 },
                },
                tooltip: {
                    callbacks: {
                        afterBody: (items) => {
                            const p = metrics[items[0].dataIndex];
                            return [`Avance: ${p.pct}%`];
                        },
                    },
                },
            },
        },
    });
})();
</script>
@endif
@endpush

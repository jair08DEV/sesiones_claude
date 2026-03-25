@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('projects.show', $project) }}" class="text-decoration-none">{{ $project->name }}</a></li>
            <li class="breadcrumb-item active">Historial</li>
        </ol>
    </nav>

    {{-- Cabecera --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-0">Historial de actividad</h4>
            <p class="text-muted small mb-0">{{ $project->name }}</p>
        </div>
        <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                 class="bi bi-arrow-left me-1" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
            </svg>
            Volver a tareas
        </a>
    </div>

    {{-- Timeline --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-semibold">Eventos registrados</h6>
            <span class="badge bg-secondary rounded-pill">{{ $activityLogs->count() }}</span>
        </div>

        @if ($activityLogs->isEmpty())
            <div class="card-body text-center text-muted py-5">
                <svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" fill="currentColor"
                     class="bi bi-clock-history mb-3 opacity-25" viewBox="0 0 16 16">
                    <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a6.9 6.9 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z"/>
                    <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z"/>
                    <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5"/>
                </svg>
                <h6 class="fw-semibold mb-1">Sin actividad aún</h6>
                <p class="small mb-0">Los cambios en tareas (crear, editar, eliminar) aparecerán aquí.</p>
            </div>
        @else
            <div class="card-body px-4 py-4">
                @foreach ($activityLogs as $log)
                    @php
                        $icon = match($log->action) {
                            'created' => ['bg' => 'bg-success', 'border' => 'border-success', 'symbol' => '+'],
                            'deleted' => ['bg' => 'bg-danger',  'border' => 'border-danger',  'symbol' => '×'],
                            default   => ['bg' => 'bg-primary', 'border' => 'border-primary', 'symbol' => '↻'],
                        };

                        $fieldLabels = [
                            'title'          => 'Título',
                            'description'    => 'Descripción',
                            'priority'       => 'Prioridad',
                            'status'         => 'Estado',
                            'estimated_time' => 'Tiempo estimado',
                            'estimated_unit' => 'Unidad estimada',
                        ];

                        $statusLabels = [
                            'backlog'     => 'Backlog',
                            'en_progreso' => 'En progreso',
                            'testing'     => 'Testing',
                            'terminada'   => 'Terminada',
                        ];

                        $priorityLabels = [
                            'alta'  => 'Alta',
                            'media' => 'Media',
                            'baja'  => 'Baja',
                        ];

                        $taskName = $log->task?->title ?? $log->old_value ?? '(tarea eliminada)';

                        $displayOld = $statusLabels[$log->old_value] ?? $priorityLabels[$log->old_value] ?? $log->old_value;
                        $displayNew = $statusLabels[$log->new_value] ?? $priorityLabels[$log->new_value] ?? $log->new_value;
                    @endphp

                    <div class="d-flex gap-3 {{ $loop->last ? '' : 'mb-4' }}">

                        {{-- Línea vertical + dot --}}
                        <div class="flex-shrink-0 d-flex flex-column align-items-center" style="width:32px">
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold {{ $icon['bg'] }}"
                                 style="width:32px;height:32px;font-size:15px;line-height:1;flex-shrink:0">
                                {{ $icon['symbol'] }}
                            </div>
                            @if (! $loop->last)
                                <div class="flex-grow-1 mt-1" style="width:2px;background:#e9ecef;min-height:24px"></div>
                            @endif
                        </div>

                        {{-- Contenido del evento --}}
                        <div class="flex-grow-1 pt-1 pb-2">
                            <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap">
                                <div>
                                    @if ($log->action === 'created')
                                        <span class="fw-semibold">Tarea creada</span>
                                        <span class="text-muted mx-1">·</span>
                                        <span class="text-success fw-semibold">{{ $taskName }}</span>
                                    @elseif ($log->action === 'deleted')
                                        <span class="fw-semibold">Tarea eliminada</span>
                                        <span class="text-muted mx-1">·</span>
                                        <span class="text-danger fw-semibold">{{ $taskName }}</span>
                                    @else
                                        <span class="fw-semibold">{{ $fieldLabels[$log->field] ?? $log->field }}</span>
                                        <span class="text-muted small"> actualizado en </span>
                                        <span class="fw-semibold">{{ $taskName }}</span>
                                    @endif
                                </div>
                                <div class="text-end flex-shrink-0">
                                    <span class="text-muted small d-block">{{ $log->created_at->diffForHumans() }}</span>
                                    <span class="text-muted" style="font-size:.7rem">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>

                            {{-- Cambio de valor --}}
                            @if ($log->action === 'updated' && ($log->old_value !== null || $log->new_value !== null))
                                <div class="d-flex align-items-center gap-2 mt-2 flex-wrap">
                                    <span class="badge bg-light text-secondary border" style="font-size:.75rem">
                                        {{ $displayOld ?? '—' }}
                                    </span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="#adb5bd" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
                                    </svg>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size:.75rem">
                                        {{ $displayNew ?? '—' }}
                                    </span>
                                </div>
                            @endif
                        </div>

                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection

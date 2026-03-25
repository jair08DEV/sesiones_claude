@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('projects.show', $project) }}" class="text-decoration-none">{{ $project->name }}</a></li>
            <li class="breadcrumb-item active">Editar tarea</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-semibold">Editar tarea</h5>
                    <span class="text-muted small">{{ $project->name }}</span>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('tasks.update', [$project, $task]) }}">
                        @csrf
                        @method('PUT')

                        {{-- Título --}}
                        <div class="mb-4">
                            <label for="title" class="form-label fw-semibold">
                                Título <span class="text-danger">*</span>
                            </label>
                            <input type="text" id="title" name="title"
                                   value="{{ old('title', $task->title) }}"
                                   class="form-control form-control-lg @error('title') is-invalid @enderror"
                                   placeholder="Ej. Diseñar landing page" autofocus>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Descripción --}}
                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold">
                                Descripción <span class="text-muted fw-normal small">(opcional)</span>
                            </label>
                            <textarea id="description" name="description" rows="3"
                                      class="form-control @error('description') is-invalid @enderror"
                                      placeholder="Detalla la tarea…">{{ old('description', $task->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Estimación --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Estimación <span class="text-muted fw-normal small">(opcional)</span>
                            </label>
                            <div class="input-group">
                                <input type="number" name="estimated_time" id="estimated_time"
                                       value="{{ old('estimated_time', $task->estimated_time) }}"
                                       class="form-control @error('estimated_time') is-invalid @enderror"
                                       placeholder="0" min="1" max="9999">
                                <select name="estimated_unit" id="estimated_unit"
                                        class="form-select @error('estimated_unit') is-invalid @enderror"
                                        style="max-width: 110px">
                                    <option value="minutos" {{ old('estimated_unit', $task->estimated_unit) === 'minutos' ? 'selected' : '' }}>min</option>
                                    <option value="horas"   {{ old('estimated_unit', $task->estimated_unit) === 'horas'   ? 'selected' : '' }}>horas</option>
                                    <option value="dias"    {{ old('estimated_unit', $task->estimated_unit) === 'dias'    ? 'selected' : '' }}>días</option>
                                </select>
                            </div>
                            @error('estimated_time')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            @error('estimated_unit')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Prioridad --}}
                        <div class="mb-4">
                            <label for="priority" class="form-label fw-semibold">
                                Prioridad <span class="text-danger">*</span>
                            </label>
                            <select id="priority" name="priority"
                                    class="form-select @error('priority') is-invalid @enderror">
                                <option value="alta"  {{ old('priority', $task->priority) === 'alta'  ? 'selected' : '' }}>Alta</option>
                                <option value="media" {{ old('priority', $task->priority) === 'media' ? 'selected' : '' }}>Media</option>
                                <option value="baja"  {{ old('priority', $task->priority) === 'baja'  ? 'selected' : '' }}>Baja</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Estado --}}
                        <div class="mb-4">
                            <label for="status" class="form-label fw-semibold">
                                Estado <span class="text-danger">*</span>
                            </label>
                            <select id="status" name="status"
                                    class="form-select @error('status') is-invalid @enderror">
                                <option value="backlog"     {{ old('status', $task->status) === 'backlog'     ? 'selected' : '' }}>Backlog</option>
                                <option value="en_progreso" {{ old('status', $task->status) === 'en_progreso' ? 'selected' : '' }}>En progreso</option>
                                <option value="testing"     {{ old('status', $task->status) === 'testing'     ? 'selected' : '' }}>Testing</option>
                                <option value="terminada"   {{ old('status', $task->status) === 'terminada'   ? 'selected' : '' }}>Terminada</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary px-4">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                Guardar cambios
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

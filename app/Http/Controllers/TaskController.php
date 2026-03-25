<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Project $project)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'priority'       => 'required|in:alta,media,baja',
            'status'         => 'required|in:backlog,en_progreso,testing,terminada',
            'estimated_time' => 'nullable|integer|min:1|max:9999',
            'estimated_unit' => 'nullable|required_with:estimated_time|in:minutos,horas,dias',
        ]);

        $project->tasks()->create($data);

        return redirect()->route('projects.show', $project)
                         ->with('success', 'Tarea añadida correctamente.');
    }

    public function edit(Project $project, Task $task)
    {
        return view('tasks.edit', compact('project', 'task'));
    }

    public function update(Request $request, Project $project, Task $task)
    {
        $data = $request->validate([
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'priority'       => 'required|in:alta,media,baja',
            'status'         => 'required|in:backlog,en_progreso,testing,terminada',
            'estimated_time' => 'nullable|integer|min:1|max:9999',
            'estimated_unit' => 'nullable|required_with:estimated_time|in:minutos,horas,dias',
        ]);

        $task->update($data);

        return redirect()->route('projects.show', $project)
                         ->with('success', 'Tarea "' . $task->title . '" actualizada.');
    }

    public function advanceStatus(Project $project, Task $task)
    {
        $next = $task->nextStatus();

        if ($next) {
            $task->update(['status' => $next]);
            $msg = $next === 'terminada'
                ? '¡Tarea marcada como terminada!'
                : 'Estado actualizado a "' . $next . '".';
        } else {
            $msg = 'La tarea ya está terminada.';
        }

        return redirect()->route('projects.show', $project)->with('success', $msg);
    }

    public function destroy(Project $project, Task $task)
    {
        $task->delete();

        return redirect()->route('projects.show', $project)
                         ->with('success', 'Tarea eliminada.');
    }
}

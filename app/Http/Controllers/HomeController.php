<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $projects = Project::withCount([
            'tasks',
            'tasks as completed_tasks_count'  => fn($q) => $q->where('status', 'terminada'),
            'tasks as active_tasks_count'     => fn($q) => $q->where('status', 'en_progreso'),
            'tasks as backlog_tasks_count'    => fn($q) => $q->where('status', 'backlog'),
            'tasks as testing_tasks_count'    => fn($q) => $q->where('status', 'testing'),
            'tasks as estimated_tasks_count'  => fn($q) => $q->whereNotNull('estimated_time'),
        ])->withSum('tasks', 'estimated_time')->latest()->get();

        // Estadísticas globales de proyectos
        $stats = [
            'total'       => $projects->count(),
            'terminados'  => $projects->filter(fn($p) => $p->tasks_count > 0 && $p->tasks_count === $p->completed_tasks_count)->count(),
            'en_progreso' => $projects->filter(fn($p) => $p->active_tasks_count > 0)->count(),
            'vencidos'    => $projects->filter(fn($p) => $p->deadline->isPast())->count(),
        ];

        // Estadísticas globales de tareas (para métricas del dashboard)
        $taskStats = [
            'total'        => Task::count(),
            'terminadas'   => Task::where('status', 'terminada')->count(),
            'en_progreso'  => Task::where('status', 'en_progreso')->count(),
            'testing'      => Task::where('status', 'testing')->count(),
            'backlog'      => Task::where('status', 'backlog')->count(),
            'con_estimado' => Task::whereNotNull('estimated_time')->count(),
        ];

        // Datos para gráficos por proyecto (tareas completas vs estimadas)
        $projectMetrics = $projects->map(fn($p) => [
            'name'       => $p->name,
            'total'      => $p->tasks_count,
            'terminadas' => $p->completed_tasks_count,
            'estimadas'  => $p->estimated_tasks_count,
            'pct'        => $p->tasks_count > 0
                                ? round(($p->completed_tasks_count / $p->tasks_count) * 100)
                                : 0,
        ])->values();

        return view('home', compact('projects', 'stats', 'taskStats', 'projectMetrics'));
    }
}

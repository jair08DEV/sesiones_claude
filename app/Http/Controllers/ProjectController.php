<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline'    => 'required|date',
        ]);

        $project = Project::create($data);

        return redirect()->route('projects.show', $project)
                         ->with('success', '¡Proyecto creado! Ahora añade las tareas.');
    }

    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline'    => 'required|date',
        ]);

        $project->update($data);

        return redirect()->route('projects.show', $project)
                         ->with('success', 'Proyecto actualizado correctamente.');
    }

    public function show(Project $project)
    {
        $tasks = $project->tasks()->latest()->get();

        return view('projects.show', compact('project', 'tasks'));
    }

    public function export(Request $request, Project $project)
    {
        $tasks  = $project->tasks()->orderBy('status')->get();
        $format = $request->query('format', 'excel');
        $slug   = Str::slug($project->name) . '_' . now()->format('Ymd');

        if ($format === 'csv') {
            $csv      = $this->buildCsv($project, $tasks);
            $filename = $slug . '.csv';

            return response($csv, 200, [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        }

        $xml      = $this->buildExcelXml($project, $tasks);
        $filename = $slug . '.xls';

        return response($xml, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    private function buildCsv(Project $project, $tasks): string
    {
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

        $handle = fopen('php://temp', 'r+');

        // ── Sección 1: Proyecto ───────────────────────────────────────────
        fputcsv($handle, ['INFORMACIÓN DEL PROYECTO']);
        fputcsv($handle, ['Campo', 'Valor']);
        fputcsv($handle, ['Nombre',         $project->name]);
        fputcsv($handle, ['Descripción',    $project->description ?? '']);
        fputcsv($handle, ['Fecha límite',   $project->deadline->format('d/m/Y')]);
        fputcsv($handle, ['Creado el',      $project->created_at->format('d/m/Y')]);
        fputcsv($handle, ['Total tareas',   $tasks->count()]);
        fputcsv($handle, ['Terminadas',     $tasks->where('status', 'terminada')->count()]);
        fputcsv($handle, ['En progreso',    $tasks->where('status', 'en_progreso')->count()]);
        fputcsv($handle, ['Testing',        $tasks->where('status', 'testing')->count()]);
        fputcsv($handle, ['Backlog',        $tasks->where('status', 'backlog')->count()]);

        // ── Fila vacía de separación ──────────────────────────────────────
        fputcsv($handle, []);

        // ── Sección 2: Tareas ─────────────────────────────────────────────
        fputcsv($handle, ['TAREAS']);
        fputcsv($handle, ['#', 'Título', 'Descripción', 'Estado', 'Prioridad',
                           'Estimación', 'Unidad', 'Creada el']);

        foreach ($tasks as $i => $task) {
            fputcsv($handle, [
                $i + 1,
                $task->title,
                $task->description ?? '',
                $statusLabels[$task->status]     ?? $task->status,
                $priorityLabels[$task->priority] ?? $task->priority,
                $task->estimated_time ?? '',
                $task->estimated_unit ?? '',
                $task->created_at->format('d/m/Y H:i'),
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }

    private function buildExcelXml(Project $project, $tasks): string
    {
        $e = fn($v) => htmlspecialchars((string) ($v ?? ''), ENT_XML1 | ENT_QUOTES, 'UTF-8');

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

        // ── Hoja 1: Información del proyecto ──────────────────────────────
        $projectRows = $this->xlRow(['Campo', 'Valor'], 'header');

        $infoFields = [
            ['Nombre',           $project->name],
            ['Descripción',      $project->description ?? '—'],
            ['Fecha límite',     $project->deadline->format('d/m/Y')],
            ['Creado el',        $project->created_at->format('d/m/Y')],
            ['Total tareas',     $tasks->count()],
            ['Terminadas',       $tasks->where('status', 'terminada')->count()],
            ['En progreso',      $tasks->where('status', 'en_progreso')->count()],
            ['Testing',          $tasks->where('status', 'testing')->count()],
            ['Backlog',          $tasks->where('status', 'backlog')->count()],
            ['Con estimación',   $tasks->whereNotNull('estimated_time')->count()],
        ];

        foreach ($infoFields as [$label, $value]) {
            $projectRows .= $this->xlRow([$label, $value], null, 'label');
        }

        // ── Hoja 2: Tareas ─────────────────────────────────────────────────
        $taskHeaders = ['#', 'Título', 'Descripción', 'Estado', 'Prioridad',
                        'Estimación', 'Unidad', 'Creada el'];
        $taskRows = $this->xlRow($taskHeaders, 'header');

        foreach ($tasks as $i => $task) {
            $taskRows .= $this->xlRow([
                $i + 1,
                $task->title,
                $task->description ?? '',
                $statusLabels[$task->status]   ?? $task->status,
                $priorityLabels[$task->priority] ?? $task->priority,
                $task->estimated_time ?? '',
                $task->estimated_unit ?? '',
                $task->created_at->format('d/m/Y H:i'),
            ]);
        }

        // ── XML completo ───────────────────────────────────────────────────
        return implode("\n", [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<?mso-application progid="Excel.Sheet"?>',
            '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"',
            '  xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">',
            '<Styles>',
            '  <Style ss:ID="header">',
            '    <Font ss:Bold="1" ss:Color="#FFFFFF" ss:Size="11"/>',
            '    <Interior ss:Color="#0D6EFD" ss:Pattern="Solid"/>',
            '    <Alignment ss:Horizontal="Center"/>',
            '  </Style>',
            '  <Style ss:ID="label">',
            '    <Font ss:Bold="1"/>',
            '  </Style>',
            '</Styles>',
            '<Worksheet ss:Name="Proyecto">',
            '  <Table ss:DefaultColumnWidth="160">' . $projectRows . '</Table>',
            '</Worksheet>',
            '<Worksheet ss:Name="Tareas">',
            '  <Table ss:DefaultColumnWidth="140">' . $taskRows . '</Table>',
            '</Worksheet>',
            '</Workbook>',
        ]);
    }

    private function xlRow(array $cells, ?string $rowStyle = null, ?string $firstCellStyle = null): string
    {
        $e    = fn($v) => htmlspecialchars((string) ($v ?? ''), ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $cols = '';

        foreach ($cells as $idx => $cell) {
            $style = $rowStyle ?? ($idx === 0 ? $firstCellStyle : null);
            $styleAttr = $style ? " ss:StyleID=\"{$style}\"" : '';
            $type  = is_numeric($cell) && $cell !== '' ? 'Number' : 'String';
            $cols .= "<Cell{$styleAttr}><Data ss:Type=\"{$type}\">" . $e($cell) . '</Data></Cell>';
        }

        return "<Row>{$cols}</Row>\n";
    }

    public function activity(Project $project)
    {
        $activityLogs = $project->activityLogs()
            ->with('task')
            ->latest()
            ->take(100)
            ->get();

        return view('projects.activity', compact('project', 'activityLogs'));
    }
}

<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Task;

class TaskObserver
{
    private const TRACKED_FIELDS = [
        'title', 'description', 'priority',
        'status', 'estimated_time', 'estimated_unit',
    ];

    public function created(Task $task): void
    {
        ActivityLog::create([
            'project_id' => $task->project_id,
            'task_id'    => $task->id,
            'user_id'    => auth()->id(),
            'action'     => 'created',
            'new_value'  => $task->title,
        ]);
    }

    public function updated(Task $task): void
    {
        $changes  = $task->getChanges();
        $original = $task->getOriginal();

        foreach (self::TRACKED_FIELDS as $field) {
            if (! array_key_exists($field, $changes)) {
                continue;
            }

            ActivityLog::create([
                'project_id' => $task->project_id,
                'task_id'    => $task->id,
                'user_id'    => auth()->id(),
                'action'     => 'updated',
                'field'      => $field,
                'old_value'  => $original[$field] ?? null,
                'new_value'  => $changes[$field],
            ]);
        }
    }

    public function deleted(Task $task): void
    {
        ActivityLog::create([
            'project_id' => $task->project_id,
            'task_id'    => $task->id,
            'user_id'    => auth()->id(),
            'action'     => 'deleted',
            'old_value'  => $task->title,
        ]);
    }
}

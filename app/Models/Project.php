<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ActivityLog;

class Project extends Model
{
    protected $fillable = ['name', 'description', 'deadline'];

    protected $casts = [
        'deadline' => 'date',
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}

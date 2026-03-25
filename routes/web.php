<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/projects/create',        [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects',              [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}',          [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/projects/{project}/edit',     [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}',          [ProjectController::class, 'update'])->name('projects.update');
    Route::get('/projects/{project}/activity', [ProjectController::class, 'activity'])->name('projects.activity');
    Route::get('/projects/{project}/export',   [ProjectController::class, 'export'])->name('projects.export');

    Route::post('/projects/{project}/tasks',                      [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/projects/{project}/tasks/{task}/edit',           [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/projects/{project}/tasks/{task}',                [TaskController::class, 'update'])->name('tasks.update');
    Route::patch('/projects/{project}/tasks/{task}/advance',      [TaskController::class, 'advanceStatus'])->name('tasks.advance');
    Route::delete('/projects/{project}/tasks/{task}',             [TaskController::class, 'destroy'])->name('tasks.destroy');
});

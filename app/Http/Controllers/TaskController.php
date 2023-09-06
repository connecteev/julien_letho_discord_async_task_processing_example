<?php

namespace App\Http\Controllers;

use App\Jobs\DummyJobSlowCountToOneHundred;
use App\Jobs\ProcessVideo;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use function dd;
use function dispatch;

class TaskController extends Controller
{
    public function show(Task $task): Response
    {
        return Inertia::render('TaskShow', [
            'task' => $task
        ]);
    }

    public function start(Task $task): RedirectResponse
    {
        if ($task->job_completed) {
            return back()->withErrors('job already completed', 'job.completed');
        }
        if ($task->job_started) {
            return back()->withErrors('job already started', 'job.started');
        }

        if ($task->id === 1) {
            dispatch(new DummyJobSlowCountToOneHundred($task));
        } else if ($task->id === 2) {
            dispatch(new ProcessVideo($task));
        }
        return back();
    }

    public function status(Task $task): JsonResponse
    {
        return response()->json($task);
    }

}

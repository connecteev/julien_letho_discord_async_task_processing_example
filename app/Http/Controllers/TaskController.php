<?php

namespace App\Http\Controllers;

use App\Jobs\DummyJobSlowCountToOneHundred;
use App\Jobs\DummyTaskWithOutput;
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

        switch ($task->id) {
            case 1:
                dispatch(new DummyJobSlowCountToOneHundred($task));
                break;
            case 2:
                dispatch(new DummyTaskWithOutput($task));
                break;
            default:
        }
        return back();
    }

    public function status(Task $task): JsonResponse
    {
        return response()->json($task);
    }

}

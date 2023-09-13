<?php

namespace App\Observers;

use App\Events\UpdateProgress;
use App\Models\Task;
use function event;

class TaskObserver
{
    public function updated(Task $task): void
    {
        event(new UpdateProgress($task));
    }
}

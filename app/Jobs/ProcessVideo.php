<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use function random_int;

class ProcessVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Task $task)
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        info('Job running');
        $progress = 0;
        $this->task->job_started = true;
        $this->task->save();

        while ($progress < 100) {
            $progress += random_int(10, 20);
            $this->task->progress = $progress;
            $this->task->save();

            sleep(3);
        }

        $this->task->job_completed = true;
        $this->task->save();
    }
}

<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use function array_push;
use function fake;
use function now;
use function random_int;
use function sleep;

class DummyProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Task $task)
    {
        $this->queue = 'tasks';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        /*       while ($this->task->progress < 100) {
                   $this->task->progress += random_int(20, 50);
                   $this->task->save();
               }*/

        $count = 0;

        while ($count++ < 15) {
            usleep(random_int(1000, 3000) * 1000);
            //sleep(5);

            $tempOutput = $this->task->output;
            $tempOutput[] = ['message' => fake()->sentence(), 'created' => now()];
            $this->task->output = $tempOutput;

            $this->task->save();
        }

    }
}

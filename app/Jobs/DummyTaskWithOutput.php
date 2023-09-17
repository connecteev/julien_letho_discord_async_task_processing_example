<?php

namespace App\Jobs;

use App\Events\UpdateProgress;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;
use function broadcast;
use function event;
use function now;
use function sleep;

class DummyTaskWithOutput implements ShouldQueue
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
        $progress = 0;
        $this->task->job_started = true;
        $this->task->save();

        //Artisan::call('dummy:task-with-output');
        //$output = Artisan::output() // Get the output of the command
        $process = Process::timeout(1200)->start('php artisan dummy:task-with-output');

        $output = "";
        $latestOutput = "";
        $outputArray = [];
        $max_entries = 5;
        /*while ($process->running()) {
            $latestOutput = $process->latestOutput();

            if (strlen($latestOutput) > 0) {
                array_unshift($outputArray, $latestOutput);
                while (count($outputArray) > $max_entries) {
                    array_pop($outputArray);
                }
                $output = "";
                foreach (array_reverse($outputArray) as $v) {
                    $output .= $v . "<br/>";
                }

                event(new UpdateProgress('Status updated: ' . now(), $this->task));

                $this->task->output = $output;
                $this->task->save();
            }
        }*/

    }
}

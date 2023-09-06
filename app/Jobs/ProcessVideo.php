<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;

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

        //Artisan::call('video:process-mp4-video-for-hls-streaming');
        //$output = Artisan::output() // Get the output of the command

        $process = Process::timeout(1200)->start('php artisan video:process-mp4-video-for-hls-streaming');

        $output = "";
        $latestOutput = "";
        $outputArray = [];
        $max_entries = 5;
        while ($process->running()) {
            $latestOutput = $process->latestOutput();

//array_unshift($outputArray, $latestOutput);
//while (count($outputArray) > $max_entries) {
//    array_pop($outputArray);
//}

            if (strlen($latestOutput) > 0) {
                $output .= $latestOutput . "<br/>";

                $this->task->output = $output;
                $this->task->save();
            }

        }

        $this->task->job_completed = true;
        $this->task->save();
    }
}

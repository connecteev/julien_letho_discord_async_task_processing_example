<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;

class DummyTaskWithOutput extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dummy:task-with-output';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dummy Task with Output';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        echo "Starting Dummy Task...\n";

        $total_steps = 10;
        for ($i=1; $i <= $total_steps; $i++) {
            echo "Executing Step $i of $total_steps\n";
            sleep(1);
        }
        echo "Task completed.\n";
    }
}

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

        echo "Executing Step 1 of 5\n";
        sleep(2);
        echo "Executing Step 2 of 5\n";
        sleep(2);
        echo "Executing Step 3 of 5\n";
        sleep(2);
        echo "Executing Step 4 of 5\n";
        sleep(2);
        echo "Executing Step 5 of 5\n";
        sleep(2);
        echo "Task completed.\n";
    }
}

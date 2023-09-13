<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use function bcrypt;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Task::create(['id' => 1, 'name' => 'Dummy Task (Slow Count to One Hundred)']);
        Task::create(['id' => 2, 'name' => 'Dummy Task with Output (Polling)']);
        Task::create(['id' => 3, 'name' => 'Dummy Task with Output (Websockets)']);

        User::create([
            'name'     => 'Julien',
            'email'    => 'jj@gmail.com',
            'password' => bcrypt(12345)
        ]);

        User::create([
            'name'     => 'Someone Else',
            'email'    => 'se@gmail.com',
            'password' => bcrypt(12345)
        ]);

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}

<?php

use App\Events\UpdateProfileInformation;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin'       => Route::has('login'),
        'canRegister'    => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion'     => PHP_VERSION,
    ]);
});

Route::controller(TaskController::class)->group(function () {
    Route::get('/show/{task}', 'show');
    Route::post('/show/{task}/start', 'start')
        ->name('task.start');

    Route::get('/stats/{task}', 'status')
        ->name('task.status');
});


Route::get('/login', [AuthenticatedSessionController::class, 'create']);
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');

Route::get('/test', function () {
    $task = Task::find(3);

    $tempOutput = $task->output;
    $tempOutput[] = ['message' => fake()->sentence(), now()];
    $task->output = $tempOutput;

    dd($task->output);
});

Route::get('/test2', function () {
    $users = User::where('id', 1)->get();

    foreach ($users as $user) {
        event(new UpdateProfileInformation($user));
    }
});
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', [
    ]);
})->name('dashboard');

<?php

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
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function(){
    Route::prefix('admin')->group(function(){
        Route::resource('licenses', \App\Http\Controllers\LicenseController::class)
            ->except(['edit','update']);
    });

    Route::resource('licenses.losts', \App\Http\Controllers\LostController::class);

    Route::get('losts', [\App\Http\Controllers\LostController::class, 'indexLicenses'])
        ->name('licenses.losts.index_licenses');

    Route::get('licenses/{license}/founds/create', [\App\Http\Controllers\FoundController::class, 'create'])
        ->name('licenses.founds.create');

    Route::post('licenses/{license}/founds', [\App\Http\Controllers\FoundController::class, 'store'])
        ->name('licenses.founds.store');

    Route::get('licenses/{license}/founds/{found}/edit', [\App\Http\Controllers\FoundController::class, 'edit'])
        ->name('licenses.founds.edit');

    Route::put('licenses/{license}/founds/{found}', [\App\Http\Controllers\FoundController::class, 'update'])
        ->name('licenses.founds.update');

    Route::get('licenses/{license}/founds/{found}', [\App\Http\Controllers\FoundController::class, 'show'])
        ->name('licenses.founds.show');

    Route::get('licenses/{license}/founds', [\App\Http\Controllers\FoundController::class, 'index'])
        ->name('licenses.founds.index');

    Route::delete('licenses/{license}/founds/{found}', [\App\Http\Controllers\FoundController::class, 'destroy'])
        ->name('licenses.founds.destroy');
});

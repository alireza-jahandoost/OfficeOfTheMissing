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

    Route::post('matchLosts/{lost}/founds/{found}', [\App\Http\Controllers\LostController::class, 'match'])
        ->name('licenses.losts.match');

    Route::resource('licenses.founds', \App\Http\Controllers\FoundController::class);

    Route::get('founds', [\App\Http\Controllers\FoundController::class, 'indexLicenses'])
        ->name('licenses.founds.index_licenses');
});

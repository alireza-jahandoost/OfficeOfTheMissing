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

Route::prefix('admin')->middleware('auth')->group(function(){
    Route::resource('licenses', \App\Http\Controllers\LicenseController::class)
        ->except(['edit','update']);
});

Route::prefix('licenses/{license}')->middleware('auth')->group(function(){
    Route::get('losts/create', [\App\Http\Controllers\LostController::class, 'create'])->name('losts.create');
    Route::post('losts', [\App\Http\Controllers\LostController::class, 'store'])->name('losts.store');
    Route::get('losts/{lost}', [\App\Http\Controllers\LostController::class, 'show'])->name('losts.show');
    Route::get('losts/{lost}/edit', [\App\Http\Controllers\LostController::class, 'edit'])->name('losts.edit');
});


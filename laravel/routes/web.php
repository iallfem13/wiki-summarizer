<?php

use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/summarize', function () {
    return Inertia::render('Summarize');
})->name('summarize');

// Route for the root URL
Route::get('/', function () {
    return Inertia::render('Summarize');
})->name('home');

Route::get('/documents', function () {
    return Inertia::render('Documents');
});

Route::get('/document/{slug}', [DocumentController::class, 'view']);

/*
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
*/

require __DIR__ . '/auth.php';

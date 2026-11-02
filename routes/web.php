<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\LibraryController::class, 'landing'])->name('landing');

Route::get('/dashboard', function () {
    return redirect()->route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'home');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/home', [\App\Http\Controllers\LibraryController::class, 'index'])->name('home');
    Route::get('/question-sets/{questionSet}/read', [\App\Http\Controllers\LibraryController::class, 'read'])->name('question-sets.read');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', \App\Http\Controllers\Admin\DashboardController::class)->name('dashboard');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin-articles.php';

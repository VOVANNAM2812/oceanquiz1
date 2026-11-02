<?php

use App\Http\Controllers\Admin\ArticleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('articles', ArticleController::class)->except('show');
    Route::get('wikipedia', [\App\Http\Controllers\Admin\WikipediaController::class, 'index'])->name('wikipedia.index');
    Route::post('wikipedia', [\App\Http\Controllers\Admin\WikipediaController::class, 'store'])->middleware('throttle:10,1')->name('wikipedia.store');
});

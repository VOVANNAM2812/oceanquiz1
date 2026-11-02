<?php

use App\Http\Controllers\Admin\{QuestionSetController, QuestionController};
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('ai', [\App\Http\Controllers\Admin\AiGenerationController::class, 'index'])->name('ai.index');
    Route::post('ai', [\App\Http\Controllers\Admin\AiGenerationController::class, 'store'])->middleware('throttle:5,1')->name('ai.store');
    Route::resource('question-sets', QuestionSetController::class)->except('show');
    Route::resource('question-sets.questions', QuestionController::class)->except(['index', 'show']);
    Route::post('question-sets/{questionSet}/publish', [QuestionSetController::class, 'publish'])->name('question-sets.publish');
    Route::post('question-sets/{questionSet}/unpublish', [QuestionSetController::class, 'unpublish'])->name('question-sets.unpublish');
});

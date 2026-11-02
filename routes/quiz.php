<?php

use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::post('/question-sets/{questionSet}/start', [QuizController::class, 'start'])->name('attempts.start');
    Route::get('/attempts/{attempt}/play', [QuizController::class, 'play'])->name('attempts.play');
    Route::post('/attempts/{attempt}/submit', [QuizController::class, 'submit'])->name('attempts.submit');
    Route::get('/attempts/{attempt}/result', [QuizController::class, 'result'])->name('attempts.result');
});

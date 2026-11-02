<?php

use Illuminate\Support\Facades\Route;

Route::get('/leaderboard', \App\Http\Controllers\LeaderboardController::class)->middleware('auth')->name('leaderboard');

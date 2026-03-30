<?php

use App\Http\Controllers\Api\CinemaController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\ShowtimeController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json(['status' => 'ok']));

Route::apiResource('movies', MovieController::class);
Route::apiResource('cinemas', CinemaController::class);
Route::apiResource('showtimes', ShowtimeController::class);
Route::apiResource('feedbacks', FeedbackController::class)->only(['index', 'store', 'show', 'destroy']);

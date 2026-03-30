<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('forgot-password');

Route::get('/movies', function () {
    return view('movies.index');
})->name('movies.index');

Route::get('/booking/{id}', function () {
    return view('booking.show');
})->name('booking.show');

Route::get('/feedback', function () {
    return view('feedback');
})->name('feedback');

Route::get('/send-email', [App\Http\Controllers\SendEmailController::class, 'send'])->name('send-email');
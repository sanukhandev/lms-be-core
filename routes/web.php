<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Swagger Documentation Routes
Route::get('/api/documentation', function () {
    return redirect('/api/documentation/');
});

// API Documentation redirect to make it easier to access
Route::get('/docs', function () {
    return redirect('/api/documentation/');
});
<?php

use Illuminate\Support\Facades\Route;

Route::match(['get', 'post'], 'login', function () {
    abort(404);
});
Route::match(['get', 'post'], 'register', function () {
    abort(404);
});
Route::match(['get', 'post'], 'forgot-password', function () {
    abort(404);
});
Route::match(['get', 'post'], 'reset-password', function () {
    abort(404);
});
Route::match(['get', 'post'], 'reset-password/{token}', function () {
    abort(404);
});

Route::get('/', function () {
    return view('welcome');
});

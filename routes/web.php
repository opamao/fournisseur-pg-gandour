<?php

use App\Http\Controllers\StocksController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('index', function () {
    return view('dashboard.dashboard');
});

Route::resource('stocks', StocksController::class);

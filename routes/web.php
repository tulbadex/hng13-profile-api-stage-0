<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

Route::get('/me', [ProfileController::class, 'me'])->middleware('throttle:60,1');

Route::get('/', function () {
    return view('welcome');
});

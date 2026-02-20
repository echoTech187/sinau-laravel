<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages::auth.login');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/rbac.php';
require __DIR__.'/products.php';
require __DIR__.'/users.php';
require __DIR__.'/settings.php';

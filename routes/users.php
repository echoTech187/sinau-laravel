<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::livewire('users', 'pages::users.show')->name('users.show');
    Route::livewire('users/create', 'pages::users.edit')->name('user.create');
    Route::livewire('users/{user}/edit', 'pages::users.edit')->name('user.edit');
    Route::livewire('users/permission', 'pages::users.permission')->name('user.permission');
});

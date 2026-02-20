<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::redirect('products', 'products');

    // Route::livewire('products/create', 'pages::product.create')->name('product.create');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('products', 'pages::products.show')->name('product.show');
});

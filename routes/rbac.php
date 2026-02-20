<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::livewire('admin/role-base-manager', 'pages::rbac.show')->name('rbac.show');
    Route::livewire('admin/role-base-manager/{roles}/teams', 'pages::rbac.teams')->name('rbac.add.teams');
    Route::livewire('admin/role-base-manager/{roles}/edit', 'pages::rbac.permission')->name('rbac.permission.edit');
    Route::livewire('admin/role-base-manager/{modules}/permissions', 'pages::rbac.modules-permissions')->name('rbac.modules.permissions');
});

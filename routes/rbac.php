<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::livewire('admin/role-base-manager', 'pages::rbac.show')->name('rbac.show');
    Route::livewire('admin/role-base-manager/{roles}/teams', 'pages::rbac.teams')->name('rbac.add.teams');
    Route::livewire('admin/role-base-manager/{roles}/edit', 'pages::rbac.permission')->name('rbac.permission.edit');
    Route::livewire('admin/role-base-manager/{roles}/field-security', 'pages::rbac.field-security')->name('rbac.field.security');
    Route::livewire('admin/role-base-manager/{modules}/permissions', 'pages::rbac.modules-permissions')->name('rbac.modules.permissions');
    Route::livewire('admin/logs', 'pages::rbac.logs')->name('rbac.logs');
    Route::livewire('admin/approvals', 'pages::rbac.approvals')->name('rbac.approvals');
});

<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function() {
    Route::get('/buses', \App\Livewire\Pages\Buses\Index::class)->name('buses.index');
    Route::get('/buses/create', \App\Livewire\Pages\Buses\Create::class)->name('buses.create');
    Route::get('/buses/{bus}/edit', \App\Livewire\Pages\Buses\Edit::class)->name('buses.edit');
    Route::get('/schedules', \App\Livewire\Pages\Schedules\Index::class)->name('schedules.index');
    Route::get('/schedules/create', \App\Livewire\Pages\Schedules\Create::class)->name('schedules.create');
    Route::get('/schedules/{schedule}/edit', \App\Livewire\Pages\Schedules\Edit::class)->name('schedules.edit');
    
    Route::get('/crews', \App\Livewire\Pages\Crews\Index::class)->name('crews.index');
    Route::get('/crews/create', \App\Livewire\Pages\Crews\Create::class)->name('crews.create');
    Route::get('/crews/{crew}/edit', \App\Livewire\Pages\Crews\Edit::class)->name('crews.edit');
    Route::get('/agents', \App\Livewire\Pages\Agents\Index::class)->name('agents.index');
    Route::get('/agents/create', \App\Livewire\Pages\Agents\Create::class)->name('agents.create');
    Route::get('/agents/{agent}/edit', \App\Livewire\Pages\Agents\Edit::class)->name('agents.edit');
    Route::get('/routes', \App\Livewire\Pages\Routes\Index::class)->name('routes.index');
    Route::get('/routes/create', \App\Livewire\Pages\Routes\Create::class)->name('routes.create');
    Route::get('/routes/{route}/edit', \App\Livewire\Pages\Routes\Edit::class)->name('routes.edit');

    Route::get('/locations', \App\Livewire\Pages\Locations\Index::class)->name('locations.index');
    Route::get('/locations/create', \App\Livewire\Pages\Locations\Create::class)->name('locations.create');
    Route::get('/locations/{location}/edit', \App\Livewire\Pages\Locations\Edit::class)->name('locations.edit');

    Route::get('/bus-classes', \App\Livewire\Pages\BusClasses\Index::class)->name('bus-classes.index');
    Route::get('/bus-classes/create', \App\Livewire\Pages\BusClasses\Create::class)->name('bus-classes.create');
    Route::get('/bus-classes/{busClass}/edit', \App\Livewire\Pages\BusClasses\Edit::class)->name('bus-classes.edit');

    Route::get('/seat-layouts', \App\Livewire\Pages\SeatLayouts\Index::class)->name('seat-layouts.index');
    Route::get('/seat-layouts/create', \App\Livewire\Pages\SeatLayouts\Create::class)->name('seat-layouts.create');
    Route::get('/seat-layouts/{seatLayout}/edit', \App\Livewire\Pages\SeatLayouts\Edit::class)->name('seat-layouts.edit');

    Route::get('/bookings', \App\Livewire\Pages\Bookings\Index::class)->name('bookings.index');
    Route::get('/bookings/create', \App\Livewire\Pages\Bookings\Create::class)->name('bookings.create');
    Route::get('/bookings/{booking}', \App\Livewire\Pages\Bookings\Show::class)->name('bookings.show');

    // Cargo & Anti-Fraud
    Route::get('/shipments', \App\Livewire\Pages\Shipments\Index::class)->name('shipments.index');
    Route::get('/shipments/create', \App\Livewire\Pages\Shipments\Create::class)->name('shipments.create');
    Route::get('/shipments/{shipment}', \App\Livewire\Pages\Shipments\Show::class)->name('shipments.show');

    // Domain 7: SJO & P2H
    Route::get('/manifests', \App\Livewire\Pages\Manifests\Index::class)->name('manifests.index');
    Route::get('/manifests/{manifest}/checklist', \App\Livewire\Pages\Manifests\Checklist::class)->name('manifests.checklist');

    Route::get('/cargo/checker', \App\Livewire\Pages\Cargo\Checker::class)->name('cargo.checker');

    // Domain 8: Maintenance
    Route::get('/maintenance', \App\Livewire\Pages\Maintenance\Dashboard::class)->name('maintenance.dashboard');
    Route::get('/maintenance/logs', \App\Livewire\Pages\Maintenance\Logs::class)->name('maintenance.logs');
    Route::get('/maintenance/rules', \App\Livewire\Pages\Maintenance\Rules\Index::class)->name('maintenance.rules');

});

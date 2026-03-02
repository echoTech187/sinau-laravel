<?php

namespace App\Livewire\Pages\Locations;

use App\Livewire\Forms\LocationForm;
use App\Models\LocationRole;
use Livewire\Component;

class Create extends Component
{
    public LocationForm $form;

    public function save()
    {
        try {
            $this->form->store();
            $this->dispatch('notify', type: 'success', title: 'Berhasil', message: 'Lokasi baru berhasil ditambahkan.');
            return $this->redirect(route('locations.index'), navigate: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify', type: 'error', title: 'Gagal', message: $e->validator->errors()->first());
            return;
        }
    }

    public function render()
    {
        return view('livewire.pages.locations.create', [
            'locationRoles' => LocationRole::all(),
        ]);
    }
}


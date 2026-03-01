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
        $this->form->store();
        
        $this->dispatch('notify', message: 'Lokasi baru berhasil ditambahkan.', type: 'success');
        
        return $this->redirect(route('locations.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.pages.locations.create', [
            'locationRoles' => LocationRole::all(),
        ]);
    }
}

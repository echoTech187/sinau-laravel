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
        
        $this->dispatch('notify', 'Lokasi baru berhasil ditambahkan.', 'success');
        
        return $this->redirect(route('locations.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.pages.locations.create', [
            'locationRoles' => LocationRole::all(),
        ]);
    }
}

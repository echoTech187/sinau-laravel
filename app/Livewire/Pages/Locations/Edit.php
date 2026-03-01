<?php

namespace App\Livewire\Pages\Locations;

use App\Livewire\Forms\LocationForm;
use App\Models\Location;
use App\Models\LocationRole;
use Livewire\Component;

class Edit extends Component
{
    public LocationForm $form;

    public function mount(Location $location)
    {
        $this->form->setLocation($location);
    }

    public function save()
    {
        $this->form->update();
        
        $this->dispatch('notify', 'Lokasi berhasil diperbarui.', 'success');
        
        return $this->redirect(route('locations.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.pages.locations.create', [ // Reusing create view as it's identical
            'locationRoles' => LocationRole::all(),
        ]);
    }
}

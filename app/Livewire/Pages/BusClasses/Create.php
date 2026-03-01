<?php

namespace App\Livewire\Pages\BusClasses;

use App\Livewire\Forms\BusClassForm;
use App\Models\Facility;
use Livewire\Component;

class Create extends Component
{
    public BusClassForm $form;

    public function save()
    {
        $this->form->store();
        
        $this->dispatch('notify', message: 'Kelas bus baru berhasil ditambahkan.', type: 'success');
        
        return $this->redirect(route('bus-classes.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.pages.bus-classes.create', [
            'facilities' => Facility::all(),
        ]);
    }
}

<?php

namespace App\Livewire\Pages\BusClasses;

use App\Livewire\Forms\BusClassForm;
use App\Models\BusClass;
use App\Models\Facility;
use Livewire\Component;

class Edit extends Component
{
    public BusClassForm $form;

    public function mount(BusClass $busClass)
    {
        $this->form->setBusClass($busClass);
    }

    public function save()
    {
        $this->form->update();
        
        $this->dispatch('notify', 'Kelas bus berhasil diperbarui.', 'success');
        
        return $this->redirect(route('bus-classes.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.pages.bus-classes.create', [
            'facilities' => Facility::all(),
        ]);
    }
}

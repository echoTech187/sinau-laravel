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
        try {
            $this->form->update();
            $this->dispatch('notify', type: 'success', title: 'Berhasil', message: 'Kelas bus berhasil diperbarui.');
            return $this->redirect(route('bus-classes.index'), navigate: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify', type: 'error', title: 'Gagal', message: $e->validator->errors()->first());
            return;
        }
    }

    public function render()
    {
        return view('livewire.pages.bus-classes.create', [
            'facilities' => Facility::all(),
        ]);
    }
}


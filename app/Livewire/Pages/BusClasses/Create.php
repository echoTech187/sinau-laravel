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
        try {
            $this->form->store();
            $this->dispatch('notify', type: 'success', title: 'Berhasil', message: 'Kelas bus baru berhasil ditambahkan.');
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


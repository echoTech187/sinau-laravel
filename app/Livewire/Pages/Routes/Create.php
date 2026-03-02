<?php

namespace App\Livewire\Pages\Routes;

use App\Livewire\Forms\RouteForm;
use App\Models\Location;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::app')]
class Create extends Component
{
    public RouteForm $form;

    #[Computed]
    public function locations()
    {
        return Location::orderBy('name', 'asc')->get();
    }

    public function addStop()
    {
        $this->form->addStop();
    }

    public function removeStop($index)
    {
        $this->form->removeStop($index);
    }

    public function saveRoute()
    {
        try {
            $this->form->store();
            $this->dispatch('notify', type: 'success', title: 'Berhasil', message: 'Data Rute berhasil ditambahkan!');
            return $this->redirectRoute('routes.index', navigate: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify', type: 'error', title: 'Gagal', message: $e->validator->errors()->first());
            return;
        }
    }

    #[Title('Tambah Rute Perjalanan')]
    public function render()
    {
        return view('livewire.pages.routes.create');
    }
}


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
        $this->form->store();

        session()->flash('message', 'Data Rute berhasil ditambahkan!');

        return $this->redirectRoute('routes.index', navigate: true);
    }

    #[Title('Tambah Rute Perjalanan')]
    public function render()
    {
        return view('livewire.pages.routes.create');
    }
}

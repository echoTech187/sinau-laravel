<?php

namespace App\Livewire\Pages\Buses;

use App\Livewire\Forms\BusForm;
use App\Models\BusClass;
use App\Models\Location;
use App\Models\SeatLayout;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::app')]
class Create extends Component
{
    public BusForm $form;

    #[Computed]
    public function busClasses()
    {
        return BusClass::all();
    }

    #[Computed]
    public function seatLayouts()
    {
        return SeatLayout::all();
    }

    #[Computed]
    public function basePools()
    {
        return Location::whereHas('roles', function($q) {
            $q->where('name', 'Pool');
        })->get();
    }

    public function saveBus()
    {
        $this->form->store();
        
        session()->flash('message', 'Armada berhasil ditambahkan!');
        return $this->redirectRoute('buses.index', navigate: true);
    }

    #[Title('Tambah Armada Baru')]
    public function render()
    {
        return view('livewire.pages.buses.create');
    }
}

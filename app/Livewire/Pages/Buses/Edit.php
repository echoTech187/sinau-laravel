<?php

namespace App\Livewire\Pages\Buses;

use App\Livewire\Forms\BusForm;
use App\Models\Bus;
use App\Models\BusClass;
use App\Models\Location;
use App\Models\SeatLayout;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::app')]
class Edit extends Component
{
    public BusForm $form;
    public Bus $bus;

    public function mount(Bus $bus)
    {
        $this->bus = $bus;
        $this->form->setBus($bus);
    }

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
        $this->form->update();
        
        session()->flash('message', 'Armada berhasil diperbarui!');
        return $this->redirectRoute('buses.index', navigate: true);
    }

    #[Title('Ubah Data Armada')]
    public function render()
    {
        return view('livewire.pages.buses.edit');
    }
}

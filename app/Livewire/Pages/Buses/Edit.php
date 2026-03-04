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
use Livewire\WithFileUploads;

#[Layout('layouts::app')]
class Edit extends Component
{
    use WithFileUploads;
    public BusForm $form;
    public Bus $bus;

    public function mount(Bus $bus)
    {
        $this->bus = $bus;
        $this->form->setBus($bus);
    }

    #[Computed(persist: true)]
    public function busClasses()
    {
        return BusClass::all();
    }

    #[Computed(persist: true)]
    public function seatLayouts()
    {
        return SeatLayout::all();
    }

    #[Computed]
    public function basePools()
    {
        return Location::whereHas('roles', function($q) {
            $q->whereIn('name', ['Pool / Garasi', 'Kantor Cabang']);
        })->get();
    }

    public function saveBus()
    {
        try {
            $this->form->update();
            $this->dispatch('notify', ['type' => 'success', 'title' => 'Berhasil', 'message' => 'Armada berhasil diperbarui!']);
            return $this->redirectRoute('buses.index', navigate: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify', ['type' => 'error', 'title' => 'Gagal', 'message' => $e->validator->errors()->first()]);
            return;
        }
    }

    #[Title('Ubah Data Armada')]
    public function render()
    {
        return view('livewire.pages.buses.edit');
    }
}


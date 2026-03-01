<?php

namespace App\Livewire\Pages\Crews;

use App\Livewire\Forms\CrewForm;
use App\Models\CrewPosition;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::app')]
class Create extends Component
{
    public CrewForm $form;

    #[Computed]
    public function crewPositions()
    {
        return CrewPosition::all();
    }

    public function saveCrew()
    {
        $this->form->store();
        
        session()->flash('message', 'Data Kru berhasil ditambahkan!');
        return $this->redirectRoute('crews.index', navigate: true);
    }

    #[Title('Tambah Data Kru')]
    public function render()
    {
        return view('livewire.pages.crews.create');
    }
}

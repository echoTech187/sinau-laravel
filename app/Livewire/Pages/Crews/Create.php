<?php

namespace App\Livewire\Pages\Crews;

use App\Livewire\Forms\CrewForm;
use App\Models\Crew;
use App\Models\CrewPosition;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::app')]
class Create extends Component
{
    public CrewForm $form;

    public function mount()
    {
        // Inisialisasi model Crew kosong dengan status default untuk mencegah error pada CrewForm
        $crew = new Crew;
        $crew->setRawAttributes(['status' => 'active']);
        $this->form->setCrew($crew);
    }

    #[Computed]
    public function crewPositions()
    {
        return CrewPosition::all();
    }

    public function saveCrew()
    {
        // $this->form->validate();

        try {

            $this->form->store();

            $this->dispatch('notify', type: 'success', title: 'Berhasil', message: 'Data kru berhasil disimpan');

            return $this->redirectRoute('crews.index', navigate: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify', type: 'error', title: 'Gagal', message: $e->validator->errors()->first());

            return;
        }
    }

    #[Title('Tambah Data Kru')]
    public function render()
    {
        return view('livewire.pages.crews.create');
    }
}


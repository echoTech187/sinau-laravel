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
class Edit extends Component
{
    public CrewForm $form;

    public Crew $crew;

    public function mount(Crew $crew)
    {
        $this->crew = $crew;
        $this->form->setCrew($crew);
    }

    #[Computed]
    public function crewPositions()
    {
        return CrewPosition::all();
    }

    public function saveCrew()
    {
        try {
            $this->form->update();

            $this->dispatch('notify', type: 'success', title: 'Berhasil', message: 'Data kru berhasil diperbarui');

            return $this->redirectRoute('crews.index', navigate: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify', type: 'error', title: 'Gagal', message: $e->validator->errors()->first());

            return;
        }
    }

    #[Title('Ubah Data Kru')]
    public function render()
    {
        return view('livewire.pages.crews.edit');
    }
}

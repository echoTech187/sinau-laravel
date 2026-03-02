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
        $validator = \Illuminate\Support\Facades\Validator::make(
            $this->form->all(),
            $this->form->rules()
        );

        if ($validator->fails()) {
            $this->setErrorBag($validator->getMessageBag());
            $this->dispatch('notify', 'Mohon periksa kembali isian form Anda.', 'error');

            return;
        }

        $this->form->store();

        $this->dispatch('notify', 'Data Kru berhasil ditambahkan!', 'success');

        return $this->redirectRoute('crews.index', navigate: true);
    }

    #[Title('Tambah Data Kru')]
    public function render()
    {
        return view('livewire.pages.crews.create');
    }
}

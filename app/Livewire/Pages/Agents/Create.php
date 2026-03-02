<?php

namespace App\Livewire\Pages\Agents;

use App\Livewire\Forms\AgentForm;
use App\Models\Agent;
use App\Models\Location;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::app')]
class Create extends Component
{
    public AgentForm $form;

    #[Computed]
    public function branchOffices()
    {
        return Agent::where('type', '=', 'branch_office', 'and')->where('status', '=', 'active')->get();
    }

    #[Computed]
    public function locations()
    {
        return Location::whereHas('roles', function ($q) {
            $q->where('name', '=', 'Agen');
        })->get();
    }

    public function saveAgent()
    {
        try {
            $this->form->store();
            $this->dispatch('notify', type: 'success', title: 'Berhasil', message: 'Data Agen/Mitra berhasil ditambahkan!');

            return $this->redirectRoute('agents.index', navigate: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify', type: 'error', title: 'Gagal', message: $e->validator->errors()->first());

            return;
        }
    }

    #[Title('Tambah Agen Baru')]
    public function render()
    {
        return view('livewire.pages.agents.create');
    }
}


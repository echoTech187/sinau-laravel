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
class Edit extends Component
{
    public AgentForm $form;
    public Agent $agent;

    public function mount(Agent $agent)
    {
        $this->agent = $agent;
        $this->form->setAgent($agent);
    }

    #[Computed]
    public function branchOffices()
    {
        return Agent::where('type', '=', 'branch_office')->where('status', '=', 'active')->get();
    }

    #[Computed]
    public function locations()
    {
        return Location::whereHas('roles', function($q) {
            $q->where('name', '=', 'Agen');
        })->get();
    }

    public function saveAgent()
    {
        $this->form->update();
        
        session()->flash('message', 'Data Agen/Mitra berhasil diperbarui!');
        return $this->redirectRoute('agents.index', navigate: true);
    }

    #[Title('Ubah Data Agen')]
    public function render()
    {
        return view('livewire.pages.agents.edit');
    }
}

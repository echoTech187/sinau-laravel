<?php

namespace App\Livewire\Pages\Crews;

use App\Livewire\Forms\CrewForm;
use App\Models\Agent;
use App\Models\Bus;
use App\Models\Crew;
use App\Models\CrewPosition;
use App\Models\Location;
use App\Models\Route;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts::app')]
class Create extends Component
{
    use WithFileUploads;

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

    #[Computed]
    public function locations()
    {
        if ($this->form->region) {
            return Location::where('province', $this->form->region)->get();
        }
        return Location::all();
    }

    #[Computed]
    public function agents()
    {
        if ($this->form->pool_id) {
            return Agent::where('location_id', $this->form->pool_id)->get();
        }
        return Agent::all();
    }

    #[Computed]
    public function buses()
    {
        if ($this->form->agent_id) {
            $agent = Agent::find($this->form->agent_id);
            if ($agent && $agent->location_id) {
                return Bus::where('base_pool_id', $agent->location_id)->get();
            }
            return collect(); // Empty collection if agent has no location
        }
        return Bus::all();
    }

    #[Computed]
    public function provinces()
    {
        return Location::whereNotNull('province')->where('province', '!=', '')->distinct()->pluck('province')->sort()->values();
    }

    #[Computed]
    public function routes()
    {
        if ($this->form->agent_id) {
             $agent = Agent::find($this->form->agent_id);
             if ($agent) {
                 return Route::where('origin_agent_id', $agent->id)
                             ->orWhere('destination_agent_id', $agent->id)
                             ->get();
             }
             return collect(); // Empty collection if agent has no location
        }
        return Route::all();
    }

    public function saveCrew()
    {
        // $this->form->validate();

        try {

            $this->form->store();

            $this->dispatch('notify', ['type' => 'success', 'title' => 'Berhasil', 'message' => 'Data Kru berhasil ditambahkan!']);

            return $this->redirectRoute('crews.index', navigate: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify', ['type' => 'error', 'title' => 'Gagal', 'message' => $e->validator->errors()->first()]);

            return;
        }
    }

    #[Title('Tambah Data Kru')]
    public function render()
    {
        return view('livewire.pages.crews.create');
    }
}


<?php

namespace App\Livewire\Pages\Agents;

use App\Livewire\Forms\AgentForm;
use App\Models\Agent;
use App\Models\AgentOperationalHour;
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

    /** Jam operasional per hari, index 0=Senin … 6=Minggu */
    public array $operationalHours = [];

    public function mount(Agent $agent)
    {
        $this->agent = $agent;
        $this->form->setAgent($agent);
        $this->loadOperationalHours();
    }

    private function loadOperationalHours(): void
    {
        $days = range(0, 6);
        $existing = $this->agent->operationalHours->keyBy('day');

        $this->operationalHours = [];
        foreach ($days as $day) {
            $oh = $existing->get($day);
            $this->operationalHours[$day] = [
                'is_closed'   => $oh ? ($oh->open_time === null && !$oh->is_24_hours) : true,
                'is_24_hours' => $oh?->is_24_hours ?? false,
                'open_time'   => $oh?->open_time ? substr($oh->open_time, 0, 5) : '08:00',
                'close_time'  => $oh?->close_time ? substr($oh->close_time, 0, 5) : '17:00',
                'notes'       => $oh?->notes ?? '',
            ];
        }
    }

    #[Computed]
    public function branchOffices()
    {
        return Agent::where('type', '=', 'branch_office', 'and')->where('status', '=', 'active')->get();
    }

    #[Computed]
    public function locations()
    {
        return Location::all();
    }

    public function saveAgent()
    {
        $this->validate([
            'operationalHours.*.open_time'  => 'nullable|date_format:H:i',
            'operationalHours.*.close_time' => 'nullable|date_format:H:i',
        ], [
            'operationalHours.*.open_time.date_format'  => 'Format jam buka tidak valid (HH:MM).',
            'operationalHours.*.close_time.date_format' => 'Format jam tutup tidak valid (HH:MM).',
        ]);

        try {
            $this->form->update();
            $this->saveOperationalHours();
            $this->dispatch('notify', ['type' => 'success', 'title' => 'Berhasil', 'message' => 'Data Agen berhasil diperbarui!']);
            return $this->redirectRoute('agents.index', navigate: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify', ['type' => 'error', 'title' => 'Gagal', 'message' => $e->validator->errors()->first()]);
            return;
        }
    }

    private function saveOperationalHours(): void
    {
        foreach ($this->operationalHours as $day => $data) {
            $isClosed  = (bool) ($data['is_closed'] ?? false);
            $is24      = (bool) ($data['is_24_hours'] ?? false);

            AgentOperationalHour::updateOrCreate(
                ['agent_id' => $this->agent->id, 'day' => $day],
                [
                    'open_time'   => (!$isClosed && !$is24) ? ($data['open_time'] . ':00') : null,
                    'close_time'  => (!$isClosed && !$is24) ? ($data['close_time'] . ':00') : null,
                    'is_24_hours' => $is24,
                    'notes'       => $data['notes'] ?? null,
                ]
            );
        }
    }

    #[Title('Ubah Data Agen')]
    public function render()
    {
        return view('livewire.pages.agents.edit');
    }
}

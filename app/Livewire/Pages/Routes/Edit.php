<?php

namespace App\Livewire\Pages\Routes;

use App\Livewire\Forms\RouteForm;
use App\Models\Agent;
use App\Models\Location;
use App\Models\Route;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::app')]
class Edit extends Component
{
    public RouteForm $form;

    public Route $route;

    public function mount(Route $route)
    {
        $this->route = $route;
        $this->form->setRoute($route);
    }

    #[Computed(persist: true)]
    public function agents()
    {
        return Agent::with('location')->get()->sortBy(function($agent) {
            return ($agent->location->province ?? '') . $agent->name;
        });
    }

    #[Computed(persist: true)]
    public function locations()
    {
        return Location::orderBy('province', 'asc')->orderBy('name', 'asc')->get();
    }

    // Re-estimate whenever origin/destination or any stop agent changes
    public function updatedFormOriginAgentId(): void       { $this->estimateDistance(); }
    public function updatedFormDestinationAgentId(): void  { $this->estimateDistance(); }
    public function updatedFormStops(): void               { $this->estimateDistance(); }

    /**
     * Full-path distance: origin → each stop (in order) → destination.
     */
    private function estimateDistance(): void
    {
        if (!$this->form->origin_agent_id || !$this->form->destination_agent_id) return;

        $origin = Agent::with('location')->find($this->form->origin_agent_id);
        $dest   = Agent::with('location')->find($this->form->destination_agent_id);

        if (!$origin?->location?->latitude || !$dest?->location?->latitude) return;

        $total = 0.0;
        $prev  = $origin;

        foreach ($this->form->stops as $s) {
            if (empty($s['agent_id'])) continue;
            $stopAgent = Agent::with('location')->find($s['agent_id']);
            if (!$stopAgent?->location?->latitude) continue;
            $total += $this->haversineKm($prev->location, $stopAgent->location);
            $prev   = $stopAgent;
        }

        $total += $this->haversineKm($prev->location, $dest->location);

        $this->form->distance_km = (int) round($total * 1.35);
    }

    /**
     * Returns per-segment route data for the preview card.
     */
    #[Computed]
    public function segmentedRoute(): array
    {
        $segments = [];
        $originAgent = $this->agents->firstWhere('id', $this->form->origin_agent_id);
        $destAgent   = $this->agents->firstWhere('id', $this->form->destination_agent_id);

        $segments[] = [
            'name'  => $originAgent?->name ?? '—',
            'label' => 'Keberangkatan',
            'km'    => null,
            'type'  => 'origin',
        ];

        $prev = $originAgent;
        foreach ($this->form->stops as $idx => $s) {
            $stopAgent = $s['agent_id'] ? $this->agents->firstWhere('id', $s['agent_id']) : null;

            $km = null;
            if ($prev?->location?->latitude && $stopAgent?->location?->latitude) {
                $km = round($this->haversineKm($prev->location, $stopAgent->location) * 1.35, 1);
            }

            $typeLabel = match($s['type'] ?? 'both') {
                'boarding_only' => ' · Naik',
                'dropoff_only'  => ' · Turun',
                'transit'       => ' · Transit',
                default         => '',
            };

            $segments[] = [
                'name'  => $stopAgent?->name ?? '— Belum dipilih',
                'label' => 'Stop ' . ($idx + 1) . $typeLabel,
                'km'    => $km,
                'type'  => 'stop',
            ];

            if ($stopAgent) $prev = $stopAgent;
        }

        $km = null;
        if ($prev?->location?->latitude && $destAgent?->location?->latitude) {
            $km = round($this->haversineKm($prev->location, $destAgent->location) * 1.35, 1);
        }

        $segments[] = [
            'name'  => $destAgent?->name ?? '—',
            'label' => 'Destinasi Akhir',
            'km'    => $km,
            'type'  => 'destination',
        ];

        return $segments;
    }

    /** Haversine great-circle distance in km (straight line). */
    private function haversineKm(object $locA, object $locB): float
    {
        $lat1 = deg2rad($locA->latitude);
        $lon1 = deg2rad($locA->longitude);
        $lat2 = deg2rad($locB->latitude);
        $lon2 = deg2rad($locB->longitude);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dlon / 2) ** 2;
        return 6371 * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    public function addStop()    { $this->form->addStop(); }
    public function removeStop($index) { $this->form->removeStop($index); $this->estimateDistance(); }

    public function saveRoute()
    {
        try {
            $this->form->update();
            $this->dispatch('notify', ['type' => 'success', 'title' => 'Berhasil', 'message' => 'Rute operasional berhasil diperbarui.']);
            return $this->redirectRoute('routes.index', navigate: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify', ['type' => 'error', 'title' => 'Gagal', 'message' => $e->validator->errors()->first()]);
            return;
        }
    }

    #[Title('Ubah Data Rute')]
    public function render()
    {
        return view('livewire.pages.routes.edit');
    }
}

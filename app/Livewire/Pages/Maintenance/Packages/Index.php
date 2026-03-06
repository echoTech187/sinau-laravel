<?php

namespace App\Livewire\Pages\Maintenance\Packages;

use App\Models\ServicePackage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $chassisFilter = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedChassisFilter()
    {
        $this->resetPage();
    }

    public function deletePackage(ServicePackage $package)
    {
        $package->delete($package->id);
        $this->dispatch('notify', ['title' => 'Berhasil', 'message' => 'Paket servis berhasil dihapus.', 'type' => 'success']);
    }

    #[Title('Paket Servis')]
    public function render()
    {
        $packages = ServicePackage::withCount('maintenanceRules')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%');
            }, $this->search)
            ->when($this->chassisFilter, function ($query) {
                $query->where('chassis_brand', $this->chassisFilter);
            }, $this->chassisFilter)
            ->orderBy('chassis_brand')
            ->orderBy('km_interval')
            ->paginate(12);

        return view('livewire.pages.maintenance.packages.index', [
            'packages' => $packages,
        ]);
    }
}

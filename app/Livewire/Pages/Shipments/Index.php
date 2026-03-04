<?php

namespace App\Livewire\Pages\Shipments;

use App\Models\Shipment;
use App\Enums\ShipmentStatus;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

use Livewire\Attributes\Title;

#[Title('Daftar Pengiriman')]
class Index extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $status = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    #[Computed]
    public function shipments()
    {
        return Shipment::query()
            ->with(['origin', 'destination', 'schedule.bus', 'booking'])
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('waybill_number', 'like', '%' . $this->search . '%')
                       ->orWhere('barcode_number', 'like', '%' . $this->search . '%')
                       ->orWhere('sender_name', 'like', '%' . $this->search . '%')
                       ->orWhere('receiver_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status, function ($q) {
                $q->where('status', '=', $this->status, 'and');
            })
            ->latest()
            ->paginate(10);
    }

    #[Computed]
    public function stats()
    {
        return [
            'total' => Shipment::count(['*']),
            'in_transit' => Shipment::where('status', '=', ShipmentStatus::IN_TRANSIT, 'and')->count(['*']),
            'pending' => Shipment::where('status', '=', ShipmentStatus::RECEIVED_AT_AGENT, 'and')->count(['*']),
            'completed' => Shipment::where('status', '=', ShipmentStatus::CLAIMED_BY_RECEIVER, 'and')->count(['*']),
        ];
    }

    public function delete($id)
    {
        $shipment = Shipment::findOrFail($id);
        $shipment->delete();
        $this->dispatch('notify', ['title' => 'Terhapus', 'message' => 'Shipment deleted successfully.', 'type' => 'success']);
    }

    public function render()
    {
        return view('livewire.pages.shipments.index');
    }
}


<?php

namespace App\Livewire\Pages\Cargo;

use App\Models\Shipment;
use App\Enums\ShipmentStatus;
use Livewire\Component;

use Livewire\Attributes\Title;

#[Title('Cek Kargo')]
class Checker extends Component
{
    public string $barcode = '';
    public ?Shipment $selectedShipment = null;

    public function check()
    {
        $this->validate([
            'barcode' => 'required|string',
        ]);

        $this->selectedShipment = Shipment::where('barcode_number', '=', $this->barcode, 'and')
            ->orWhere('waybill_number', '=', $this->barcode, 'or')
            ->with(['origin', 'destination', 'schedule.bus'])
            ->first();

        if (!$this->selectedShipment) {
            $this->dispatch('notify', ['title' => 'Gagal', 'message' => 'Resi/Barcode tidak ditemukan! Kargo Ilegal?', 'type' => 'error']);
        }
    }

    public function verify()
    {
        if (!$this->selectedShipment) return;

        $this->selectedShipment->update([
            'status' => ShipmentStatus::INSPECTED_BY_CHECKER
        ]);

        $this->dispatch('notify', ['title' => 'Berhasil', 'message' => 'Kargo Berhasil Diverifikasi (SAFE).', 'type' => 'success']);
        $this->reset(['barcode', 'selectedShipment']);
    }

    public function render()
    {
        return view('livewire.pages.cargo.checker');
    }
}


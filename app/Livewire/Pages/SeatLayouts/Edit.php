<?php

namespace App\Livewire\Pages\SeatLayouts;

use App\Livewire\Forms\SeatLayoutForm;
use App\Models\SeatLayout;
use Livewire\Component;

class Edit extends Component
{
    public SeatLayoutForm $form;

    public function mount(SeatLayout $seatLayout)
    {
        $this->form->setSeatLayout($seatLayout);
    }

    public function updatedFormGridRows()
    {
        $this->generateGrid();
    }

    public function updatedFormGridColumns()
    {
        $this->generateGrid();
    }

    public function generateGrid()
    {
        $newMapping = [];
        for ($r = 1; $r <= $this->form->grid_rows; $r++) {
            for ($c = 1; $c <= $this->form->grid_columns; $c++) {
                $found = collect($this->form->layout_mapping)->first(fn($s) => $s['row'] == $r && $s['col'] == $c);
                
                $newMapping[] = $found ?: [
                    'row' => $r,
                    'col' => $c,
                    'seat_number' => '',
                    'type' => 'available',
                ];
            }
        }
        $this->form->layout_mapping = $newMapping;
    }

    public function toggleSeat($index)
    {
        $currentType = $this->form->layout_mapping[$index]['type'];
        $types = ['available', 'seat', 'driver', 'door', 'toilet', 'stairs'];
        $nextType = $types[(array_search($currentType, $types) + 1) % count($types)];
        $this->form->layout_mapping[$index]['type'] = $nextType;

        if ($nextType === 'seat' && empty($this->form->layout_mapping[$index]['seat_number'])) {
            $lastSeat = collect($this->form->layout_mapping)
                ->where('type', 'seat')
                ->where('seat_number', '!=', '')
                ->last();
            
            $nextNum = $lastSeat ? (int)$lastSeat['seat_number'] + 1 : 1;
            $this->form->layout_mapping[$index]['seat_number'] = (string)$nextNum;
        }
    }

    public function save()
    {
        $this->form->update();
        
        $this->dispatch('notify', message: 'Layout kursi berhasil diperbarui.', type: 'success');
        
        return $this->redirect(route('seat-layouts.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.pages.seat-layouts.create');
    }
}

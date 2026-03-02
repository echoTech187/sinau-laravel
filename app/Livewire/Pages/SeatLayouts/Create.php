<?php

namespace App\Livewire\Pages\SeatLayouts;

use App\Livewire\Forms\SeatLayoutForm;
use Livewire\Component;

class Create extends Component
{
    public SeatLayoutForm $form;

    public function mount()
    {
        $this->generateGrid();
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
                    'type' => 'available', // available, seat, driver, door, toilet, stairs
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
        
        // Auto-assign seat number if it's a seat
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
        try {
            $this->form->store();
            $this->dispatch('notify', type: 'success', title: 'Berhasil', message: 'Layout kursi baru berhasil disimpan.');
            return $this->redirect(route('seat-layouts.index'), navigate: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify', type: 'error', title: 'Gagal', message: $e->validator->errors()->first());
            return;
        }
    }

    public function render()
    {
        return view('livewire.pages.seat-layouts.create');
    }
}


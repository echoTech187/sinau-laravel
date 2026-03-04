<?php

namespace App\Livewire\Pages\SeatLayouts;

use App\Livewire\Forms\SeatLayoutForm;
use App\Models\SeatLayout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Edit Layout Kursi')]
class Edit extends Component
{
    public SeatLayoutForm $form;
    
    // Modal State
    public bool $showEditorModal = false;
    public ?int $selectedDeckIndex = null;
    public ?int $selectedSeatIndex = null;
    
    // Editor Form State
    public string $tempType = 'available';
    public string $tempSeatNumber = '';
    public ?int $tempBusClassId = null;

    public int $activeDeckIndex = 0;

    // Temporary values for grid inputs to prevent auto-regeneration
    public int $tempRows = 10;
    public int $tempCols = 5;

    public function mount(SeatLayout $seatLayout)
    {
        $this->form->setSeatLayout($seatLayout);
        $this->initializeDecks();
        $this->syncTempValues();
    }

    public function syncTempValues()
    {
        $this->tempRows = $this->form->decks[$this->activeDeckIndex]['rows'] ?? 10;
        $this->tempCols = $this->form->decks[$this->activeDeckIndex]['cols'] ?? 5;
    }

    public function updatedActiveDeckIndex()
    {
        $this->syncTempValues();
    }

    public function applyGridChanges()
    {
        $this->form->decks[$this->activeDeckIndex]['rows'] = $this->tempRows;
        $this->form->decks[$this->activeDeckIndex]['cols'] = $this->tempCols;
        $this->generateGrid($this->activeDeckIndex);
        
        $this->dispatch('notify', ['type' => 'info', 'title' => 'Grid Diperbarui', 'message' => 'Layout grid telah disesuaikan.']);
    }

    public function initializeDecks()
    {
        // Grid generation is handled by setSeatLayout via the form structure, 
        // but we ensure all decks are ready.
        foreach ($this->form->decks as $index => $deck) {
            $this->generateGrid($index);
        }
    }

    public function updatedFormIsDoubleDecker()
    {
        if ($this->form->is_double_decker && count($this->form->decks) < 2) {
            $this->form->decks[] = [
                'name' => 'Upper Deck',
                'rows' => 12,
                'cols' => 5,
                'mapping' => []
            ];
            $this->generateGrid(1);
        } elseif (!$this->form->is_double_decker && count($this->form->decks) > 1) {
            array_pop($this->form->decks);
            if ($this->activeDeckIndex > 0) $this->activeDeckIndex = 0;
        }
    }

    public function updatedFormDecks()
    {
        $this->generateGrid($this->activeDeckIndex);
    }

    public function generateGrid($deckIndex)
    {
        $deck = &$this->form->decks[$deckIndex];
        $newMapping = [];
        
        for ($r = 1; $r <= $deck['rows']; $r++) {
            for ($c = 1; $c <= $deck['cols']; $c++) {
                $found = collect((array)($deck['mapping'] ?? []))->first(fn($s) => $s['row'] == $r && $s['col'] == $c);
                
                $newMapping[] = $found ?: [
                    'row' => $r,
                    'col' => $c,
                    'seat_number' => '',
                    'type' => 'available',
                    'bus_class_id' => null,
                ];
            }
        }
        $deck['mapping'] = $newMapping;
    }

    public function openEditor($deckIndex, $seatIndex)
    {
        $this->selectedDeckIndex = $deckIndex;
        $this->selectedSeatIndex = $seatIndex;
        
        $seat = (array)($this->form->decks[$deckIndex]['mapping'][$seatIndex] ?? []);
        $this->tempType = $seat['type'];
        $this->tempSeatNumber = $seat['seat_number'] ?? '';
        $this->tempBusClassId = $seat['bus_class_id'] ?? null;
        
        $this->showEditorModal = true;
    }

    public function saveElement()
    {
        $deck = &$this->form->decks[$this->selectedDeckIndex];
        $deck['mapping'] = (array)($deck['mapping'] ?? []);
        $seat = &$deck['mapping'][$this->selectedSeatIndex];
        
        $seat['type'] = $this->tempType;
        $seat['seat_number'] = ($this->tempType === 'seat') ? $this->tempSeatNumber : '';
        $seat['bus_class_id'] = ($this->tempType === 'seat') ? $this->tempBusClassId : null;
        
        // Auto-increment seat number logic if empty
        if ($this->tempType === 'seat' && empty($seat['seat_number'])) {
            $allSeats = collect($this->form->decks)->flatMap(function($deck) {
                return (array)($deck['mapping'] ?? []);
            });
            $lastSeat = $allSeats->where('type', 'seat')->where('seat_number', '!=', '')->last();
            $nextNum = $lastSeat ? (int)$lastSeat['seat_number'] + 1 : 1;
            $seat['seat_number'] = (string)$nextNum;
        }

        $this->showEditorModal = false;
    }

    public function save()
    {
        try {
            $this->form->update();
            $this->dispatch('notify', ['type' => 'success', 'title' => 'Berhasil', 'message' => 'Layout kursi berhasil diperbarui.']);
            return $this->redirect(route('seat-layouts.index'), navigate: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify', ['type' => 'error', 'title' => 'Gagal', 'message' => $e->validator->errors()->first()]);
            return;
        }
    }

    public function getSeatCounts()
    {
        $counts = [];
        foreach ($this->form->decks as $deck) {
            foreach ($deck['mapping'] as $seat) {
                if (($seat['type'] ?? '') === 'seat' && !empty($seat['bus_class_id'])) {
                    $classId = $seat['bus_class_id'];
                    $counts[$classId] = ($counts[$classId] ?? 0) + 1;
                }
            }
        }
        return $counts;
    }

    public function render()
    {
        return view('livewire.pages.seat-layouts.create', [
            'busClasses' => \App\Models\BusClass::all()
        ]);
    }
}


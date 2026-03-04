<?php

namespace App\Livewire\Forms;

use App\Models\SeatLayout;
use Livewire\Attributes\Rule;
use Livewire\Form;

class SeatLayoutForm extends Form
{
    public ?SeatLayout $seatLayout = null;

    #[Rule('required|string|min:3|max:255')]
    public string $name = '';

    public bool $is_double_decker = false;

    // We keep these for the active/first deck context in the UI if needed, 
    // but the source of truth will be in the decks array.
    public int $grid_rows = 10;
    public int $grid_columns = 5;

    public array $decks = [];

    public function setSeatLayout(SeatLayout $seatLayout)
    {
        $this->seatLayout = $seatLayout;
        $this->name = $seatLayout->name;
        
        $mapping = $seatLayout->layout_mapping;
        
        // Handle Legacy Data or New Structure
        if (isset($mapping['is_double_decker'])) {
            $this->is_double_decker = $mapping['is_double_decker'];
            $this->decks = $mapping['decks'];
            
            // Sync current grid view to first deck
            $this->grid_rows = $this->decks[0]['rows'] ?? 10;
            $this->grid_columns = $this->decks[0]['cols'] ?? 5;
        } else {
            // Convert Legacy to New Structure
            $this->is_double_decker = false;
            $this->grid_rows = $seatLayout->grid_rows;
            $this->grid_columns = $seatLayout->grid_columns;
            $this->decks = [
                [
                    'name' => 'Main Deck',
                    'rows' => $this->grid_rows,
                    'cols' => $this->grid_columns,
                    'mapping' => $mapping ?? []
                ]
            ];
        }
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|min:3|max:255',
            'decks' => 'required|array|min:1',
        ]);
        
        SeatLayout::create([
            'name' => $this->name,
            'grid_rows' => $this->decks[0]['rows'],
            'grid_columns' => $this->decks[0]['cols'],
            'layout_mapping' => [
                'is_double_decker' => $this->is_double_decker,
                'decks' => $this->decks,
            ],
        ]);

        $this->reset();
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|min:3|max:255',
            'decks' => 'required|array|min:1',
        ]);
        
        $this->seatLayout->update([
            'name' => $this->name,
            'grid_rows' => $this->decks[0]['rows'],
            'grid_columns' => $this->decks[0]['cols'],
            'layout_mapping' => [
                'is_double_decker' => $this->is_double_decker,
                'decks' => $this->decks,
            ],
        ]);
    }
}

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

    #[Rule('required|integer|min:1|max:20')]
    public int $grid_rows = 10;

    #[Rule('required|integer|min:1|max:10')]
    public int $grid_columns = 5;

    #[Rule('nullable|array')]
    public array $layout_mapping = [];

    public function setSeatLayout(SeatLayout $seatLayout)
    {
        $this->seatLayout = $seatLayout;
        $this->name = $seatLayout->name;
        $this->grid_rows = $seatLayout->grid_rows;
        $this->grid_columns = $seatLayout->grid_columns;
        $this->layout_mapping = $seatLayout->layout_mapping;
    }

    public function store()
    {
        $this->validate();
        
        SeatLayout::create([
            'name' => $this->name,
            'grid_rows' => $this->grid_rows,
            'grid_columns' => $this->grid_columns,
            'layout_mapping' => $this->layout_mapping,
        ]);

        $this->reset();
    }

    public function update()
    {
        $this->validate();
        
        $this->seatLayout->update([
            'name' => $this->name,
            'grid_rows' => $this->grid_rows,
            'grid_columns' => $this->grid_columns,
            'layout_mapping' => $this->layout_mapping,
        ]);
    }
}

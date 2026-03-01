<?php

namespace App\Livewire\Forms;

use App\Models\BusClass;
use Livewire\Attributes\Rule;
use Livewire\Form;

class BusClassForm extends Form
{
    public ?BusClass $busClass = null;

    #[Rule('required|string|min:3|max:255')]
    public string $name = '';

    #[Rule('required|integer|min:0')]
    public int $free_baggage_kg = 20;

    #[Rule('nullable|string')]
    public string $description = '';

    public array $facility_ids = [];

    public function setBusClass(BusClass $busClass)
    {
        $this->busClass = $busClass;
        $this->name = $busClass->name;
        $this->free_baggage_kg = $busClass->free_baggage_kg;
        $this->description = $busClass->description ?? '';
        $this->facility_ids = $busClass->facilities()->pluck('facilities.id')->toArray();
    }

    public function store()
    {
        $validated = $this->validate();
        
        $busClass = BusClass::create([
            'name' => $this->name,
            'free_baggage_kg' => $this->free_baggage_kg,
            'description' => $this->description,
        ]);
        
        $busClass->facilities()->sync($this->facility_ids);

        $this->reset();
    }

    public function update()
    {
        $this->validate();
        
        $this->busClass->update([
            'name' => $this->name,
            'free_baggage_kg' => $this->free_baggage_kg,
            'description' => $this->description,
        ]);
        
        $this->busClass->facilities()->sync($this->facility_ids);
    }
}

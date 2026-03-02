<?php

namespace App\Livewire\Forms;

use App\Enums\CrewStatus;
use App\Models\Crew;
use Illuminate\Validation\Rule;
use Livewire\Form;

class CrewForm extends Form
{
    public ?Crew $crew = null;

    public $crew_position_id = '';

    public $employee_number = '';

    public $name = '';

    public $phone_number = '';

    public $license_number = '';

    public $license_expired_at = '';

    public $status = 'active';

    public function setCrew(Crew $crew)
    {
        $this->crew = $crew;
        $this->crew_position_id = $crew->crew_position_id;
        $this->employee_number = $crew->employee_number;
        $this->name = $crew->name;
        $this->phone_number = $crew->phone_number;
        $this->license_number = $crew->license_number;
        $this->license_expired_at = $crew->license_expired_at ? $crew->license_expired_at->format('Y-m-d') : null;
        $this->status = $crew->status->value;
    }

    public function rules()
    {
        return [
            'crew_position_id' => 'required|exists:crew_positions,id',
            'employee_number' => [
                'required',
                'string',
                'min:3',
                'max:50',
                Rule::unique('crews', 'employee_number')->ignore($this->crew?->id),
            ],
            'name' => 'required|string|min:3|max:255',
            'phone_number' => 'required|string|min:9|max:20',
            'license_number' => 'nullable|string|max:100',
            'license_expired_at' => 'nullable|date',
            'status' => ['required', Rule::enum(CrewStatus::class)],
        ];
    }

    public function store()
    {
        $this->validate();

        Crew::create($this->all());
    }

    public function update()
    {
        $this->validate();

        $this->crew->update($this->all());
    }
}

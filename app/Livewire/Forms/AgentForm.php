<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\Agent;
use App\Enums\AgentType;
use App\Enums\CommissionType;
use App\Enums\AgentStatus;
use Illuminate\Validation\Rule;

class AgentForm extends Form
{
    public ?Agent $agent = null;

    public $agent_code = '';
    public $location_id = '';
    public $parent_branch_id = '';
    public $name = '';
    public $phone_number = '';
    public $type = 'partner_general';
    public $commission_type = 'percentage';
    public $commission_value = 0;
    public $status = 'active';

    public function setAgent(Agent $agent)
    {
        $this->agent = $agent;
        $this->agent_code = $agent->agent_code;
        $this->location_id = $agent->location_id;
        $this->parent_branch_id = $agent->parent_branch_id;
        $this->name = $agent->name;
        $this->phone_number = $agent->phone_number;
        $this->type = $agent->type->value;
        $this->commission_type = $agent->commission_type->value;
        $this->commission_value = $agent->commission_value;
        $this->status = $agent->status->value;
    }

    public function rules()
    {
        return [
            'agent_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('agents', 'agent_code')->ignore($this->agent?->id)
            ],
            'location_id' => 'nullable|exists:locations,id',
            'parent_branch_id' => 'nullable|exists:agents,id',
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'type' => ['required', Rule::enum(AgentType::class)],
            'commission_type' => ['required', Rule::enum(CommissionType::class)],
            'commission_value' => 'required|numeric|min:0',
            'status' => ['required', Rule::enum(AgentStatus::class)],
        ];
    }

    public function store()
    {
        $this->validate();

        Agent::create($this->all());
    }

    public function update()
    {
        $this->validate();

        $this->agent->update($this->all());
    }
}

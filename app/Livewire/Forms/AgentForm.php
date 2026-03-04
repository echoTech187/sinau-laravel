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
    public ?int $location_id = null;
    public ?int $parent_branch_id = null;
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
                'min:3',
                'max:50',
                Rule::unique('agents', 'agent_code')->ignore($this->agent?->id)
            ],
            'location_id' => 'nullable|exists:locations,id',
            'parent_branch_id' => 'nullable|exists:agents,id',
            'name' => 'required|string|min:3|max:255',
            'phone_number' => 'required|string|min:9|max:20',
            'type' => ['required', Rule::enum(AgentType::class)],
            'commission_type' => ['required', Rule::enum(CommissionType::class)],
            'commission_value' => 'required|numeric|min:0',
            'status' => ['required', Rule::enum(AgentStatus::class)],
        ];
    }

    public function store()
    {
        $this->validate();

        $data = $this->all();
        $data['location_id'] = $data['location_id'] ?: null;
        $data['parent_branch_id'] = $data['parent_branch_id'] ?: null;

        Agent::create($data);
    }

    public function update()
    {
        $this->validate();

        $data = $this->all();
        $data['location_id'] = $data['location_id'] ?: null;
        $data['parent_branch_id'] = $data['parent_branch_id'] ?: null;

        $this->agent->update($data);
    }
}

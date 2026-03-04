<?php

namespace App\Livewire\Forms;

use App\Enums\CrewStatus;
use App\Models\Crew;
use App\Services\ImageService;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Livewire\Form;

class CrewForm extends Form
{
    public ?Crew $crew = null;

    // Identity
    public $nik               = '';
    public $employee_number   = '';
    public $name              = '';
    public $gender            = '';
    public $birth_date        = '';
    public $religion          = '';
    public $marital_status    = '';
    public $blood_type        = '';
    public $original_address  = '';
    public $current_address   = '';
    public $domicile_city     = '';

    // Contact
    public $phone_number      = '';
    public $contact_phone_1   = '';
    public $contact_phone_2   = '';

    // License & Position
    public $license_number    = '';
    public $license_expired_at = '';
    public $crew_position_id  = '';
    public $rank              = '';

    // Personal
    public $spouse_name       = '';
    public $children_count    = 0;
    public $join_date         = '';
    public $education         = '';

    // Assignment
    public $status            = '';
    public $region            = '';
    public $pool_id           = '';
    public $agent_id          = '';
    public $bus_id            = '';
    public $route_id          = '';

    // Photo
    public $photo             = null;
    public $photo_path        = '';

    public function setCrew(Crew $crew)
    {
        $this->crew = $crew;
        $this->nik = $crew->nik;
        $this->crew_position_id = $crew->crew_position_id;
        $this->employee_number = $crew->employee_number;
        $this->name = $crew->name;
        $this->gender = $crew->gender;
        $this->birth_date = $crew->birth_date ? $crew->birth_date->format('Y-m-d') : null;
        $this->religion = $crew->religion;
        $this->marital_status = $crew->marital_status;
        $this->blood_type = $crew->blood_type;
        $this->original_address = $crew->original_address;
        $this->current_address = $crew->current_address;
        $this->domicile_city = $crew->domicile_city;
        $this->phone_number = $crew->phone_number;
        $this->contact_phone_1 = $crew->contact_phone_1;
        $this->contact_phone_2 = $crew->contact_phone_2;
        $this->license_number = $crew->license_number;
        $this->license_expired_at = $crew->license_expired_at ? $crew->license_expired_at->format('Y-m-d') : null;
        $this->rank = $crew->rank;
        $this->spouse_name = $crew->spouse_name;
        $this->children_count = $crew->children_count;
        $this->join_date = $crew->join_date ? $crew->join_date->format('Y-m-d') : null;
        $this->education = $crew->education;
        $this->status = $crew->status->value;
        $this->region = $crew->region;
        $this->pool_id = $crew->pool_id;
        $this->agent_id = $crew->agent_id;
        $this->bus_id = $crew->bus_id;
        $this->route_id = $crew->route_id;
        $this->photo_path = $crew->photo_path;
    }

    public function rules()
    {
        return [
            'nik' => [
                'required', 'string', 'max:20',
                Rule::unique('crews', 'nik')->ignore($this->crew?->id),
            ],
            'crew_position_id' => 'required|exists:crew_positions,id',
            'employee_number' => [
                'required', 'string', 'min:3', 'max:50',
                Rule::unique('crews', 'employee_number')->ignore($this->crew?->id),
            ],
            'name' => 'required|string|min:3|max:255',
            'gender' => 'required|string|in:Laki-laki,Perempuan',
            'birth_date' => 'required|date',
            'religion' => 'required|string|max:50',
            'marital_status' => 'required|string|max:50',
            'blood_type' => 'required|string|max:5',
            'original_address' => 'required|string',
            'current_address' => 'required|string',
            'domicile_city' => 'required|string|max:100',
            'phone_number' => 'required|string|min:9|max:20',
            'contact_phone_1' => 'nullable|string|max:20',
            'contact_phone_2' => 'nullable|string|max:20',
            'license_number' => 'nullable|string|max:100',
            'license_expired_at' => 'nullable|date',
            'rank' => 'required|string|max:50',
            'spouse_name' => 'nullable|string|max:255',
            'children_count' => 'required|integer|min:0',
            'join_date' => 'required|date',
            'education' => 'nullable|string|max:100',
            'status' => ['required', Rule::enum(CrewStatus::class)],
            'region' => 'nullable|string|max:100',
            'pool_id' => 'nullable|exists:locations,id',
            'agent_id' => 'nullable|exists:agents,id',
            'bus_id' => 'nullable|exists:buses,id',
            'route_id' => 'nullable|exists:routes,id',
            'photo' => 'nullable|image|max:2048',
        ];
    }

    public function store()
    {
        $this->validate();

        $data = $this->all();
        
        if ($this->photo) {
            $data['photo_path'] = $this->processPhoto($this->photo);
        }
        
        // Exclude the livewire temporary file object
        unset($data['photo']);

        Crew::create($data);
    }

    public function update()
    {
        $this->validate();

        $data = $this->all();
        
        if ($this->photo) {
            // Delete old photo before replacing
            if ($this->crew?->photo_path) {
                Storage::disk('public')->delete($this->crew->photo_path);
            }
            $data['photo_path'] = $this->processPhoto($this->photo);
        }
        
        unset($data['photo']);

        $this->crew->update($data);
    }

    /**
     * Crop to 1:1 aspect ratio (600×600px) and compress to JPEG.
     */
    private function processPhoto($upload): string
    {
        return app(ImageService::class)->cropAndCompress(
            upload: $upload,
            directory: 'crews/photos',
            disk: 'public',
            width: 600,
            height: 600,
            quality: 85
        );
    }
}

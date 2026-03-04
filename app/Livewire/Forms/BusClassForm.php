<?php

namespace App\Livewire\Forms;

use App\Models\BusClass;
use Livewire\Attributes\Rule;
use Livewire\Form;

class BusClassForm extends Form
{
    public ?BusClass $busClass = null;

    public string $name = '';
    public int $free_baggage_kg = 20;
    public string $description = '';
    public array $facility_ids = [];

    public function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'free_baggage_kg' => 'required|integer|min:0',
            'description' => 'nullable|string|max:1000',
            'facility_ids' => 'nullable|array',
            'facility_ids.*' => 'exists:facilities,id',
        ];
    }

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

    public function generateDescriptionWithAI()
    {
        $this->validateOnly('name');

        $facilityContext = 'berbagai fasilitas standar kenyamanan';
        if (! empty($this->facility_ids)) {
            $facilityNames = \App\Models\Facility::whereIn('id', $this->facility_ids)->pluck('name')->toArray();
            if (count($facilityNames) > 1) {
                $last = array_pop($facilityNames);
                $facilityContext = 'fasilitas unggulan seperti '.implode(', ', $facilityNames).' dan '.$last;
            } elseif (count($facilityNames) === 1) {
                $facilityContext = 'fasilitas '.$facilityNames[0];
            }
        }

        // Try using real AI first
        $prompt = "Buatkan deskripsi singkat, profesional, dan menarik dalam bahasa Indonesia untuk kelas bus bernama ':name'. ".
                  'Kelas ini memiliki :context. '.
                  'Gunakan gaya bahasa pemasaran transportasi yang mewah namun ramah. '.
                  "Jangan gunakan awalan seperti 'Tentu, ini deskripsinya'. Langsung ke teks deskripsinya saja. ".
                  'Maksimal 2-3 kalimat.';

        $prompt = str_replace([':name', ':context'], [$this->name, $facilityContext], $prompt);

        $aiService = new \App\Services\AiService;
        $generated = $aiService->generate($prompt);

        if ($generated) {
            $this->description = trim($generated, " \"\n\r\t");

            return;
        }

        // Fallback to templates if AI fails or API key is missing
        $templates = [
            'Kelas :name menawarkan pengalaman perjalanan premium dengan :context, memastikan kenyamanan maksimal di setiap lintasan.',
            'Nikmati perjalanan eksklusif bersama armada :name yang telah dilengkapi dengan :context untuk kepuasan Anda.',
            'Layanan :name hadir sebagai solusi perjalanan mewah Anda, didukung oleh :context yang dirancang khusus untuk kenyamanan penumpang.',
            'Armada :name merupakan pilihan tepat untuk perjalanan jarak jauh, mengedepankan aspek relaksasi dengan :context.',
        ];

        $template = $templates[array_rand($templates)];

        $this->description = str_replace(
            [':name', ':context'],
            [$this->name, $facilityContext],
            $template
        );
    }
}

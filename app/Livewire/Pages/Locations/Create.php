<?php

namespace App\Livewire\Pages\Locations;

use App\Livewire\Forms\LocationForm;
use App\Models\LocationRole;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Tambah Lokasi')]
class Create extends Component
{
    public LocationForm $form;

    public function updatedFormAddress($value)
    {
        if (empty($this->form->city)) {
            $this->form->city = $this->extractCity($value);
        }
        if (empty($this->form->province)) {
            $this->form->province = $this->extractProvince($value);
        }
    }

    private array $provinceMap = [
        'jawa timur'                => 'Jawa Timur',
        'jawa tengah'               => 'Jawa Tengah',
        'jawa barat'                => 'Jawa Barat',
        'daerah istimewa yogyakarta'=> 'D.I. Yogyakarta',
        'diy'                       => 'D.I. Yogyakarta',
        'yogyakarta'                => 'D.I. Yogyakarta',
        'dki jakarta'               => 'DKI Jakarta',
        'jakarta'                   => 'DKI Jakarta',
        'banten'                    => 'Banten',
        'sumatera selatan'          => 'Sumatera Selatan',
        'lampung'                   => 'Lampung',
        'sumatera utara'            => 'Sumatera Utara',
    ];

    private array $cityPrefixes = ['kota', 'kab.', 'kabupaten', 'kec.', 'kecamatan', 'kel.', 'kelurahan'];

    private function extractCity(string $address): string
    {
        if (empty($address)) return '';

        $parts = array_map('trim', explode(',', $address));
        $parts = array_filter($parts, fn ($p) => $p !== '');
        $parts = array_values($parts);

        foreach (array_reverse($parts) as $part) {
            $cleaned = preg_replace('/\b\d{5}\b/', '', $part);
            $cleaned = trim($cleaned, " \t,");

            if (empty($cleaned)) continue;

            foreach ($this->cityPrefixes as $prefix) {
                $cleaned = preg_replace('/^' . preg_quote($prefix, '/') . '\s+/i', '', $cleaned);
            }

            $cleaned = trim($cleaned);
            if (strlen($cleaned) < 2) continue;

            if (stripos($cleaned, 'jawa') === 0 || stripos($cleaned, 'sumatera') === 0
                || strtolower($cleaned) === 'yogyakarta'
                || strtolower($cleaned) === 'banten'
                || strtolower($cleaned) === 'jakarta'
            ) {
                continue;
            }

            return ucwords(strtolower($cleaned));
        }

        return '';
    }

    private function extractProvince(string $address): string
    {
        if (empty($address)) return '';

        $lower = strtolower($address);

        foreach ($this->provinceMap as $keyword => $province) {
            if (str_contains($lower, $keyword)) {
                return $province;
            }
        }

        return '';
    }

    public function save()
    {
        try {
            $this->form->store();
            $this->dispatch('notify', ['type' => 'success', 'title' => 'Berhasil', 'message' => 'Lokasi baru berhasil ditambahkan.']);
            return $this->redirect(route('locations.index'), navigate: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify', ['type' => 'error', 'title' => 'Gagal', 'message' => $e->validator->errors()->first()]);
            return;
        }
    }

    public function render()
    {
        return view('livewire.pages.locations.create', [
            'locationRoles' => LocationRole::all(),
        ]);
    }
}


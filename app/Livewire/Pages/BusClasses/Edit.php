<?php

namespace App\Livewire\Pages\BusClasses;

use App\Livewire\Forms\BusClassForm;
use App\Models\BusClass;
use App\Models\Facility;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Edit Kelas Bus')]
class Edit extends Component
{
    public BusClassForm $form;

    public function mount(BusClass $busClass)
    {
        $this->form->setBusClass($busClass);
    }

    public function generateDescription()
    {
        try {
            $this->form->generateDescriptionWithAI();
            $this->dispatch('notify', ['title' => 'AI Berhasil', 'message' => 'Deskripsi telah dihasilkan otomatis.', 'type' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('notify', ['title' => 'Gagal', 'message' => 'Mohon isi nama kelas terlebih dahulu.', 'type' => 'error']);
        }
    }

    public function save()
    {
        try {
            $this->form->update();
            session()->flash('notify', [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Kelas bus berhasil diperbarui.'
            ]);
            return $this->redirect(route('bus-classes.index'), navigate: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'title' => 'Gagal',
                'message' => $e->validator->errors()->first()
            ]);
            return;
        }
    }

    public function render()
    {
        return view('livewire.pages.bus-classes.create', [
            'facilities' => Facility::all(),
        ]);
    }
}


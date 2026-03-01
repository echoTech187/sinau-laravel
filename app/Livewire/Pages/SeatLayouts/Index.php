<?php

namespace App\Livewire\Pages\SeatLayouts;

use App\Models\SeatLayout;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $confirmingLayoutDeletion = false;
    public ?int $layoutIdBeingDeleted = null;

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[Computed]
    public function seatLayouts()
    {
        return SeatLayout::query()
            ->where('name', 'like', '%' . $this->search . '%')
            ->withCount('buses')
            ->latest()
            ->paginate(10);
    }

    public function confirmDeleteLayout($id)
    {
        $this->confirmingLayoutDeletion = true;
        $this->layoutIdBeingDeleted = $id;
    }

    public function deleteLayout()
    {
        if ($this->layoutIdBeingDeleted) {
            SeatLayout::find($this->layoutIdBeingDeleted)->delete();
            $this->confirmingLayoutDeletion = false;
            $this->layoutIdBeingDeleted = null;
            $this->dispatch('notify', message: 'Layout kursi berhasil dihapus.', type: 'success');
        }
    }

    public function render()
    {
        return view('livewire.pages.seat-layouts.index');
    }
}

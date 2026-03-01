<?php

namespace App\Livewire\Pages\Manifests;

use App\Models\OperationalManifest;
use App\Models\Schedule;
use App\Enums\OperationalManifestStatus;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $status = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[Computed]
    public function manifests()
    {
        return OperationalManifest::query()
            ->with(['schedule.route', 'schedule.bus', 'checklists', 'approvals'])
            ->when($this->search, function ($q) {
                $q->where('manifest_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('schedule.bus', function ($q2) {
                      $q2->where('fleet_code', 'like', '%' . $this->search . '%');
                  });
            })
            ->when($this->status, function ($q) {
                $q->where('status', '=', $this->status, 'and');
            })
            ->latest()
            ->paginate(10);
    }

    public function generateForSchedule($scheduleId)
    {
        $schedule = Schedule::findOrFail($scheduleId);
        
        // Check if already exists
        if (OperationalManifest::where('schedule_id', '=', $scheduleId, 'and')->exists()) {
            $this->dispatch('notify', 'SJO untuk jadwal ini sudah ada.', 'warning');
            return;
        }

        $manifest = OperationalManifest::create([
            'schedule_id' => $schedule->id,
            'manifest_number' => 'SJO-' . strtoupper(Str::random(8)),
            'authorized_by_id' => Auth::id(),
            'status' => OperationalManifestStatus::DRAFT,
        ]);

        $this->dispatch('notify', 'Draft SJO berhasil dibuat.', 'success');
    }

    #[Computed]
    public function availableSchedules()
    {
        // Schedules that don't have a manifest yet
        return Schedule::whereDoesntHave('manifests')
            ->with(['route', 'bus'])
            ->where('status', '=', 'scheduled', 'and')
            ->latest()
            ->get();
    }

    public function deleteManifest($id)
    {
        OperationalManifest::findOrFail($id)->delete();
        $this->dispatch('notify', 'SJO berhasil dihapus.', 'success');
    }

    public function render()
    {
        return view('livewire.pages.manifests.index');
    }
}

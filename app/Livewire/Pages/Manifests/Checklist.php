<?php

namespace App\Livewire\Pages\Manifests;

use App\Enums\ApprovalStatus;
use App\Enums\ManifestResult;
use App\Enums\OperationalManifestStatus;
use App\Models\InspectionCategory;
use App\Models\InspectionItem;
use App\Models\ManifestApproval;
use App\Models\ManifestChecklist;
use App\Models\OperationalManifest;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Attributes\Layout;
    
#[Layout('layouts::app')]
class Checklist extends Component
{
    public OperationalManifest $manifest;

    public $selectedCategoryId = null;

    public $responses = []; // [item_id => ['result' => 'pass', 'notes' => '']]

    public function mount(OperationalManifest $manifest)
    {
        $this->manifest = $manifest;
        $this->selectedCategoryId = InspectionCategory::first(['id'])?->id;
        $this->loadResponses();
    }

    public function selectCategory($id)
    {
        $this->selectedCategoryId = $id;
        $this->loadResponses();
    }

    public function loadResponses()
    {
        $this->responses = [];
        $existing = ManifestChecklist::where('manifest_id', '=', $this->manifest->id, 'and')
            ->whereIn('inspection_item_id', function ($q) {
                $q->select('id')->from('inspection_items')->where('category_id', '=', $this->selectedCategoryId, 'and');
            })
            ->get();

        foreach ($existing as $check) {
            $this->responses[$check->inspection_item_id] = [
                'result' => $check->result->value,
                'notes' => $check->notes,
            ];
        }

        // Fill remaining with null/default if needed
        $items = InspectionItem::where('category_id', '=', $this->selectedCategoryId, 'and')->get();
        foreach ($items as $item) {
            if (! isset($this->responses[$item->id])) {
                $this->responses[$item->id] = ['result' => '', 'notes' => ''];
            }
        }
    }

    #[Computed]
    public function categories()
    {
        return InspectionCategory::all();
    }

    #[Computed]
    public function currentItems()
    {
        return InspectionItem::where('category_id', '=', $this->selectedCategoryId, 'and')->get();
    }

    public function saveChecklist()
    {
        $totalMaxScore = 0;
        $totalEarnedScore = 0;
        $hasCriticalFail = false;
        $items = $this->currentItems;

        foreach ($items as $item) {
            $response = $this->responses[$item->id] ?? null;

            if (! $response || empty($response['result'])) {
                $this->dispatch('notify', "Harap isi semua item: {$item->item_name}", 'error');

                return;
            }

            $result = ManifestResult::from($response['result']);
            $earned = 0;

            if ($result === ManifestResult::PASS) {
                $earned = $item->max_score;
                $totalMaxScore += $item->max_score;
            } elseif ($result === ManifestResult::PASS_WITH_NOTE) {
                $earned = $item->max_score * 0.5;
                $totalMaxScore += $item->max_score;
            } elseif ($result === ManifestResult::FAIL) {
                $earned = 0;
                $totalMaxScore += $item->max_score;
                if ($item->is_critical) {
                    $hasCriticalFail = true;
                }
            }
            // N/A doesn't count towards totalMaxScore (divisor)

            $totalEarnedScore += (float) $earned;

            ManifestChecklist::updateOrCreate(
                ['manifest_id' => $this->manifest->id, 'inspection_item_id' => $item->id],
                [
                    'checked_by_id' => Auth::id(),
                    'earned_score' => $earned,
                    'notes' => $response['notes'] ?? '',
                    'result' => $result,
                ]
            );
        }

        // Calculate achieved percentage
        $percentage = ($totalMaxScore > 0) ? ($totalEarnedScore / $totalMaxScore) * 100 : 100;
        $category = InspectionCategory::find($this->selectedCategoryId, ['*']);

        $status = ApprovalStatus::APPROVED;
        if ($hasCriticalFail || $percentage < $category->min_passing_percentage) {
            $status = ApprovalStatus::REJECTED;
        }

        ManifestApproval::updateOrCreate(
            ['manifest_id' => $this->manifest->id, 'category_id' => $this->selectedCategoryId],
            [
                'approved_by_id' => Auth::id(),
                'achieved_percentage' => $percentage,
                'status' => $status,
            ]
        );

        // Update overall manifest status
        $this->updateManifestStatus();

        $this->dispatch('notify', type: 'success', title: 'Berhasil', message: 'Data inspeksi divisi berhasil disimpan.');
    }

    protected function updateManifestStatus()
    {
        $approvals = ManifestApproval::where('manifest_id', '=', $this->manifest->id, 'and')->get();
        $totalCats = InspectionCategory::count(['*']);

        if ($approvals->contains('status', ApprovalStatus::REJECTED)) {
            $this->manifest->update(['status' => OperationalManifestStatus::REJECTED]);
        } elseif ($approvals->count() == $totalCats && $approvals->every('status', ApprovalStatus::APPROVED)) {
            $this->manifest->update(['status' => OperationalManifestStatus::APPROVED]);
        } else {
            $this->manifest->update(['status' => OperationalManifestStatus::DRAFT]);
        }
    }

    #[Title('Monitoring SJO & P2H')]
    public function render()
    {
        return view('livewire.pages.manifests.checklist');
    }
}


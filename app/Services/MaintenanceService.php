<?php

namespace App\Services;

use App\Models\Bus;
use App\Models\MaintenanceRule;
use App\Models\MaintenanceLog;
use Carbon\Carbon;

class MaintenanceService
{
    /**
     * Calculate maintenance status for a bus across all applicable rules.
     */
    public function calculateBusHealth(Bus $bus): array
    {
        $rules = MaintenanceRule::with('preferredAgent')->where(function ($query) use ($bus) {
            $query->whereNull('chassis_brand')
                  ->orWhere('chassis_brand', '=', $bus->chassis_brand);
        })->get();

        $healthStatus = [];

        foreach ($rules as $rule) {
            // Find last resolved log for this specific rule
            $lastLog = MaintenanceLog::where('bus_id', '=', $bus->id)
                ->where('maintenance_rule_id', '=', $rule->id)
                ->where('status', '=', 'resolved')
                ->latest()
                ->first();

            $lastOdo = $lastLog ? $lastLog->odometer_at_service : 0;
            $currentOdo = $bus->current_odometer;
            $kmSinceLastService = $currentOdo - $lastOdo;
            
            $remainingKm = $rule->interval_km - $kmSinceLastService;
            $isDue = $remainingKm <= $rule->tolerance_km;
            $isOverdue = $remainingKm <= 0;

            $healthStatus[] = [
                'rule_id' => $rule->id,
                'task_name' => $rule->task_name,
                'last_service_odo' => $lastOdo,
                'km_since_last' => $kmSinceLastService,
                'interval' => $rule->interval_km,
                'remaining_km' => $remainingKm,
                'status' => $isOverdue ? 'overdue' : ($isDue ? 'warning' : 'healthy'),
                'estimated_hours' => $rule->estimated_hours,
                'preferred_agent' => $rule->preferredAgent?->name,
            ];
        }

        return $healthStatus;
    }

    /**
     * Get buses with expiring documents (STNK, KIR, etc) within a threshold.
     */
    public function getExpiringDocuments(int $daysThreshold = 30): array
    {
        $thresholdDate = Carbon::now()->addDays($daysThreshold);
        
        return Bus::where('stnk_expired_at', '<=', $thresholdDate)
            ->orWhere('kir_expired_at', '<=', $thresholdDate)
            ->orWhere('kps_expired_at', '<=', $thresholdDate)
            ->orWhere('insurance_expired_at', '<=', $thresholdDate)
            ->get()
            ->map(function ($bus) use ($thresholdDate) {
                $expiring = [];
                if ($bus->stnk_expired_at <= $thresholdDate) $expiring[] = 'STNK';
                if ($bus->kir_expired_at <= $thresholdDate) $expiring[] = 'KIR';
                if ($bus->kps_expired_at <= $thresholdDate) $expiring[] = 'KPS';
                if ($bus->insurance_expired_at <= $thresholdDate) $expiring[] = 'Asuransi';

                return [
                    'bus_id' => $bus->id,
                    'fleet_code' => $bus->fleet_code,
                    'documents' => implode(', ', $expiring),
                    'min_expiry' => min(array_filter([
                        $bus->stnk_expired_at, 
                        $bus->kir_expired_at, 
                        $bus->kps_expired_at, 
                        $bus->insurance_expired_at
                    ])),
                ];
            })
            ->toArray();
    }
}

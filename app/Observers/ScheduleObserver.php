<?php

namespace App\Observers;

use App\Models\Schedule;

class ScheduleObserver
{
    /**
     * Handle the Schedule "created" event.
     */
    public function saved(Schedule $schedule): void
    {
        if ($schedule->bus && $schedule->end_odometer) {
            $bus = $schedule->bus;
            
            // Only update if the trip odometer is more than current recorded bus odometer
            if ($schedule->end_odometer > $bus->current_odometer) {
                $bus->update([
                    'current_odometer' => $schedule->end_odometer
                ]);
            }
        }
    }
}

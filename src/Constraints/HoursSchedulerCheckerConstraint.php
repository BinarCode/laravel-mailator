<?php

namespace Binarcode\LaravelMailator\Constraints;

use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Illuminate\Support\Collection;

class HoursSchedulerCheckerConstraint implements SendScheduleConstraint
{
    public function canSend(MailatorSchedule $schedule, Collection $logs): bool
    {
        if (! $schedule->hasPrecision()) {
            return true;
        }

        return in_array(now()->hour, $schedule->schedule_at_hours);
    }
}

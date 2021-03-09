<?php

namespace Binarcode\LaravelMailator\Constraints;

use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Illuminate\Support\Collection;

class AfterConstraint implements SendScheduleConstraint
{
    public function canSend(MailatorSchedule $schedule, Collection $logs): bool
    {
        if (! $schedule->isAfter()) {
            return true;
        }

        if (is_null($schedule->timestamp_target)) {
            return true;
        }

        // it's in the future
        if (now()->lt($schedule->timestamp_target)) {
            return false;
        }

        //till ends we should have at least toDays days
        return $schedule->isOnce()
            ? $schedule->timestamp_target->diffInDays(now()) === $schedule->toDays()
            : $schedule->timestamp_target->diffInDays(now()) > $schedule->toDays();
    }
}

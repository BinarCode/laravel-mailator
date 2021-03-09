<?php

namespace Binarcode\LaravelMailator\Constraints;

use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Illuminate\Support\Collection;

class BeforeConstraint implements SendScheduleConstraint
{
    public function canSend(MailatorSchedule $schedule, Collection $logs): bool
    {
        if (! $schedule->isBefore()) {
            return true;
        }

        if (is_null($schedule->timestamp_target)) {
            return true;
        }

        // if already expired
        if ($schedule->timestamp_target->lte(now())) {
            return false;
        }

        //till ends we should have at least toDays days
        return $schedule->isOnce()
            ? $schedule->timestamp_target->diffInDays(now()) === $schedule->toDays()
            : $schedule->timestamp_target->diffInDays(now()) < $schedule->toDays();
    }
}

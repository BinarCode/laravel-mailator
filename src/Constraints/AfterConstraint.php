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

        if ($schedule->toDays() > 0) {
            if (now()->lt($schedule->timestamp_target->addDays($schedule->toDays()))) {
                return false;
            }

            return $schedule->isOnce()
                ? $schedule->timestamp_target->diffInDays(now()) === $schedule->toDays()
                : $schedule->timestamp_target->diffInDays(now()) > $schedule->toDays();
        }

        if ($schedule->toHours() > 0) {
            if (now()->lt($schedule->timestamp_target->addHours($schedule->toHours()))) {
                return false;
            }

            //till ends we should have at least toDays days
            return $schedule->isOnce()
                ? $schedule->timestamp_target->diffInHours(now()) === $schedule->toHours()
                : $schedule->timestamp_target->diffInHours(now()) > $schedule->toHours();
        }

        if (now()->lt($schedule->timestamp_target->addMinutes($schedule->delay_minutes))) {
            return false;
        }

        //till ends we should have at least toDays days
        return $schedule->isOnce()
            ? $schedule->timestamp_target->diffInHours(now()) === $schedule->delay_minutes
            : $schedule->timestamp_target->diffInHours(now()) > $schedule->delay_minutes;
    }
}

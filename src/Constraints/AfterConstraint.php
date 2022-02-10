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
            if (now()->floorSeconds()->lt($schedule->timestampTarget()->addDays($schedule->toDays()))) {
                return false;
            }

            return $schedule->isOnce()
                ? $schedule->timestamp_target->diffInDays(now()->floorSeconds()) === $schedule->toDays()
                : $schedule->timestamp_target->diffInDays(now()->floorSeconds()) > $schedule->toDays();
        }

        if ($schedule->toHours() > 0) {
            if (now()->floorSeconds()->lt($schedule->timestampTarget()->addHours($schedule->toHours()))) {
                return false;
            }

            //till ends we should have at least toDays days
            return $schedule->isOnce()
                ? $schedule->timestamp_target->diffInHours(now()->floorSeconds()) === $schedule->toHours()
                : $schedule->timestamp_target->diffInHours(now()->floorSeconds()) > $schedule->toHours();
        }

        if (now()->floorSeconds()->lt($schedule->timestampTarget()->addMinutes($schedule->delay_minutes))) {
            return false;
        }

        //till ends we should have at least toDays days
        return $schedule->isOnce()
            ? $schedule->timestamp_target->diffInHours(now()->floorSeconds()) === $schedule->delay_minutes
            : $schedule->timestamp_target->diffInHours(now()->floorSeconds()) > $schedule->delay_minutes;
    }
}

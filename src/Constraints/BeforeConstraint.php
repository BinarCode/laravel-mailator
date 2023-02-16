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

        if (is_null($schedule->timestampTarget())) {
            return true;
        }

        // if already expired
        if ($schedule->timestampTarget()->lte(now()->floorSeconds())) {
            return false;
        }

        if ($schedule->toDays() > 0) {
            if (now()->floorSeconds()->gt($schedule->timestampTarget()->addDays($schedule->toDays()))) {
                return false;
            }

            //till ends we should have at least toDays days
            return $schedule->isOnce()
                ? $schedule->timestampTarget()->diffInDays(now()->floorSeconds()) === $schedule->toDays()
                : $schedule->timestampTarget()->diffInDays(now()->floorSeconds()) < $schedule->toDays();
        }

        if ($schedule->toHours() > 0) {
            if (now()->floorSeconds()->gt($schedule->timestampTarget()->addHours($schedule->toHours()))) {
                return false;
            }

            //till ends we should have at least toHours days
            return $schedule->isOnce()
                ? $schedule->timestamp_target->diffInHours(now()->floorSeconds()) === $schedule->toHours()
                : $schedule->timestamp_target->diffInHours(now()->floorSeconds()) < $schedule->toHours();
        }



        //till ends we should have at least toDays days
        return $schedule->isOnce()
            ? $schedule->timestampTarget()->diffInDays(now()->floorSeconds()) === $schedule->toDays()
            : $schedule->timestampTarget()->diffInDays(now()->floorSeconds()) < $schedule->toDays();
    }
}

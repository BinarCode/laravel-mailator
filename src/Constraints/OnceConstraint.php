<?php

namespace Binarcode\LaravelMailator\Constraints;

use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Illuminate\Support\Collection;

class OnceConstraint implements SendScheduleConstraint
{
    public function canSend(MailatorSchedule $schedule, Collection $logs): bool
    {
        return $schedule->isOnce()
            ? is_null($schedule->last_sent_at)
            : true;
    }
}

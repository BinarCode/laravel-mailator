<?php

namespace Binarcode\LaravelMailator\Constraints;

use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Illuminate\Support\Collection;

class ManyConstraint implements SendScheduleConstraint
{
    public function canSend(MailatorSchedule $schedule, Collection $logs): bool
    {
        return $schedule->isMany()
            ? true
            : true;
    }
}

<?php

namespace Binarcode\LaravelMailator\Tests\Fixtures\Constraints;

use Binarcode\LaravelMailator\Constraints\SendScheduleConstraint;
use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Illuminate\Support\Collection;

class DynamicContraint implements SendScheduleConstraint
{
    public function canSend(MailatorSchedule $schedule, Collection $logs): bool
    {
        if (isset($_SERVER['constraints.shouldSend'])) {
            return $_SERVER['constraints.shouldSend'];
        }

        return true;
    }
}

<?php

namespace Binarcode\LaravelMailator\Tests\Fixtures\Constraints;

use Binarcode\LaravelMailator\Constraints\SendScheduleConstraint;
use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Illuminate\Support\Collection;

class FailingConstraint implements SendScheduleConstraint
{
    public function canSend(MailatorSchedule $schedule, Collection $logs): bool
    {
        abort(403, 'Some failing.');
    }
}

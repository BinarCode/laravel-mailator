<?php

namespace Binarcode\LaravelMailator\Tests\Fixtures;

use Binarcode\LaravelMailator\Constraints\SendScheduleConstraint;
use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Illuminate\Support\Collection;

class BeforeInvoiceExpiresConstraint implements SendScheduleConstraint
{
    public function canSend(MailatorSchedule $schedule, Collection $log): bool
    {
        return true;
    }
}

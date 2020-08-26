<?php

namespace Binarcode\LaravelMailator\Constraints;

use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Illuminate\Support\Collection;

interface SendScheduleConstraint
{
    public function canSend(MailatorSchedule $mailatorSchedule, Collection $logs): bool;
}

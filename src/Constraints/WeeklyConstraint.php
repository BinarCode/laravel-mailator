<?php

namespace Binarcode\LaravelMailator\Constraints;

use Binarcode\LaravelMailator\Models\MailatorLog;
use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Illuminate\Support\Collection;

class WeeklyConstraint implements SendScheduleConstraint
{
    public function canSend(MailatorSchedule $schedule, Collection $logs): bool
    {
        if (! $schedule->isWeekly()) {
            return true;
        }

        if ($logs->count() === 0) {
            return true;
        }

        $lastLog = $logs
            ->filter(fn (MailatorLog $log) => $log->isSent())
            ->last();

        if ($lastLog instanceof MailatorLog) {
            return $lastLog->created_at->diffInDays(now()) >= 7;
        } else {
            return true;
        }
    }
}

<?php

namespace Binarcode\LaravelMailator\Actions;

use Binarcode\LaravelMailator\Models\MailatorSchedule;

class ResolveGarbageAction implements Action
{
    public function handle(MailatorSchedule $schedule)
    {
        if ($this->shouldMarkComplete($schedule)) {
            $schedule->markComplete();
        }
    }

    public function shouldMarkComplete(MailatorSchedule $schedule): bool
    {
        $sentOnce = $schedule->fresh()->wasSentOnce();

        if ($schedule->isOnce() && $sentOnce) {
            return true;
        }

        if ($schedule->isManual()) {
            return true;
        }

        if ($schedule->isNever()) {
            return true;
        }

        if ($schedule->isMany()) {
            return false;
        }

        if (! $schedule->nextTrigger()) {
            return true;
        }

        if ($schedule->failedLastTimes(config('mailator.scheduler.mark_complete_after_fails_count', 3))) {
            return true;
        }

        return false;
    }
}

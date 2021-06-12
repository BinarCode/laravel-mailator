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
        if ($schedule->isOnce() && $schedule->fresh()->last_sent_at) {
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

        if (!$schedule->nextTrigger()) {
            return true;
        }

        if ($schedule->failedLastTimes(3)) {
            return true;
        }

        return false;
    }
}

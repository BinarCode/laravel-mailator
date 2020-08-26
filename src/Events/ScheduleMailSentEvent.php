<?php

namespace Binarcode\LaravelMailator\Events;

use Binarcode\LaravelMailator\Models\MailatorSchedule;

class ScheduleMailSentEvent
{
    public MailatorSchedule $schedule;

    public function __construct(MailatorSchedule $schedule)
    {
        $this->schedule = $schedule;
    }
}

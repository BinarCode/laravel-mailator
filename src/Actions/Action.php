<?php

namespace Binarcode\LaravelMailator\Actions;

use Binarcode\LaravelMailator\Models\MailatorSchedule;

interface Action
{
    public function handle(MailatorSchedule $schedule);
}

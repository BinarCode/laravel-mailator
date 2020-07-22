<?php

namespace Binarcode\LaravelMailator;

use Binarcode\LaravelMailator\Models\MailatorLog;
use Binarcode\LaravelMailator\Models\MailatorSchedule;

interface MailatorEvent
{
    public function canSend(MailatorSchedule $mailatorSchedule, MailatorLog $log): bool;
}

<?php

namespace Binarcode\LaravelMailator;

use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Illuminate\Support\Collection;

interface MailatorEvent
{
    public function canSend(MailatorSchedule $mailatorSchedule, Collection $logs): bool;
}

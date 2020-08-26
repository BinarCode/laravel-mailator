<?php

namespace Binarcode\LaravelMailator\Tests\Fixtures;

use Binarcode\LaravelMailator\MailatorEvent;
use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Illuminate\Support\Collection;

class SingleSendingCondition implements MailatorEvent
{
    public function canSend(MailatorSchedule $mailatorSchedule, Collection $logs): bool
    {
        return $_SERVER['can_send'];
    }
}

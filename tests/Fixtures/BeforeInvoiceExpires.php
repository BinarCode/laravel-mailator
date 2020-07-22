<?php

namespace Binarcode\LaravelMailator\Tests\Fixtures;

use Binarcode\LaravelMailator\MailatorEvent;
use Binarcode\LaravelMailator\Models\MailatorLog;
use Binarcode\LaravelMailator\Models\MailatorSchedule;

class BeforeInvoiceExpires implements MailatorEvent
{
    public function canSend(MailatorSchedule $mailatorSchedule, MailatorLog $log): bool
    {
        return true;
    }
}

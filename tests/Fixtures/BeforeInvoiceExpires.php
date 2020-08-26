<?php

namespace Binarcode\LaravelMailator\Tests\Fixtures;

use Binarcode\LaravelMailator\MailatorEvent;
use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Illuminate\Support\Collection;

class BeforeInvoiceExpires implements MailatorEvent
{
    public function canSend(MailatorSchedule $mailatorSchedule, Collection $log): bool
    {
        return true;
    }
}

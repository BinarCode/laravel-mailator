<?php

namespace Binarcode\LaravelMailator\Tests\Fixtures\Actions;

use Binarcode\LaravelMailator\Actions\Action;
use Binarcode\LaravelMailator\Models\MailatorSchedule;

class NoopAction implements Action
{
    public function handle(MailatorSchedule $schedule)
    {
        //
    }
}

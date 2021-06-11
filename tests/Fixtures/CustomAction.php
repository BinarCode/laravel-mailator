<?php

namespace Binarcode\LaravelMailator\Tests\Fixtures;

use Binarcode\LaravelMailator\Actions\Action;
use Binarcode\LaravelMailator\Models\MailatorSchedule;

class CustomAction implements Action
{
    public function __construct(
        private User $user
    ) {
    }

    public function handle(MailatorSchedule $schedule)
    {
        $this->user->markEmailAsVerified();
    }
}

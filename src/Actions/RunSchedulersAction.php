<?php

namespace Binarcode\LaravelMailator\Actions;

use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Binarcode\LaravelMailator\Support\ClassResolver;

class RunSchedulersAction
{
    use ClassResolver;

    public function __invoke(): void
    {
        static::scheduler()::query()
            ->ready()
            ->cursor()
            ->filter(fn (MailatorSchedule $schedule) => $schedule->shouldSend())
            ->each(fn (MailatorSchedule $schedule) => $schedule->execute());
    }
}

<?php

namespace Binarcode\LaravelMailator;

use Binarcode\LaravelMailator\Models\MailatorSchedule;

class SchedulerManager
{
    private ?MailatorSchedule $instance;

    public function init(string $name): MailatorSchedule
    {
        return $this->instance = MailatorSchedule::init($name);
    }

    public function __destruct()
    {
        if (! $this->instance->wasRecentlyCreated) {
            $this->instance->save();
        }
    }
}

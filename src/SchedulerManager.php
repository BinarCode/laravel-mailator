<?php

namespace Binarcode\LaravelMailator;

use Binarcode\LaravelMailator\Actions\RunSchedulersAction;
use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Binarcode\LaravelMailator\Support\ClassResolver;

class SchedulerManager
{
    use ClassResolver;

    private ?MailatorSchedule $instance;

    public function init(string $name = ''): MailatorSchedule
    {
        return $this->instance = (static::scheduler())::init($name);
    }

    public function run(): void
    {
        app(RunSchedulersAction::class)();
    }

    public function __destruct()
    {
        if (! $this->instance->wasRecentlyCreated) {
            $this->instance->save();
        }
    }
}

<?php

namespace Binarcode\LaravelMailator;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Binarcode\LaravelMailator\SchedulerManager
 * @mixin \Binarcode\LaravelMailator\SchedulerManager
 */
class Scheduler extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'mailator-scheduler';
    }
}

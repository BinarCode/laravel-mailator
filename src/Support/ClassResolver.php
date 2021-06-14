<?php

namespace Binarcode\LaravelMailator\Support;

use Binarcode\LaravelMailator\Actions\ResolveGarbageAction;
use Binarcode\LaravelMailator\Models\MailatorSchedule;

trait ClassResolver
{
    public static function garbageResolver(): ResolveGarbageAction
    {
        return app(
            config('mailator.scheduler.garbage_resolver', ResolveGarbageAction::class),
        );
    }

    public static function scheduler(): MailatorSchedule
    {
        return app(
            config('mailator.scheduler.model', MailatorSchedule::class),
        );
    }
}

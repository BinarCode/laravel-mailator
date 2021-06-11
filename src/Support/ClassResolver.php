<?php

namespace Binarcode\LaravelMailator\Support;

use Binarcode\LaravelMailator\Actions\ResolveGarbageAction;

trait ClassResolver
{
    public static function garbageResolver(): ResolveGarbageAction
    {
        return app(
            config('mailator.scheduler.garbage_resolver', ResolveGarbageAction::class),
        );
    }
}

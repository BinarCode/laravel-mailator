<?php

namespace Binarcode\LaravelMailator\Support;

use Binarcode\LaravelMailator\Actions\ResolveGarbageAction;
use Binarcode\LaravelMailator\Actions\SendMailAction;
use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Illuminate\Support\Facades\Config;

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

    public static function sendMailAction(): SendMailAction
    {
        return app(Config::get('mailator.scheduler.send_mail_action', SendMailAction::class));
    }
}

<?php

namespace Binarcode\LaravelMailator\Actions;

use Binarcode\LaravelMailator\Events\ScheduleMailSentEvent;
use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Binarcode\LaravelMailator\Support\ClassResolver;
use Exception;
use Illuminate\Support\Facades\Mail;

class SendMailAction implements Action
{
    use ClassResolver;

    public function handle(MailatorSchedule $schedule)
    {
        try {
            $this->sendMail($schedule);

            static::garbageResolver()->handle($schedule);
        } catch (Exception $exception) {
            $schedule->markAsFailed($exception->getMessage());

            report($exception);
        }
    }

    protected function sendMail(MailatorSchedule $schedule)
    {
        //todo - apply replacers for variables maybe
        Mail::to($schedule->getRecipients())->send(
            $schedule->getMailable()
        );

        $schedule->markAsSent();

        event(new ScheduleMailSentEvent($schedule));
    }
}

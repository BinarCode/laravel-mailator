<?php

namespace Binarcode\LaravelMailator\Actions;

use Binarcode\LaravelMailator\Events\ScheduleMailSentEvent;
use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Exception;
use Illuminate\Support\Facades\Mail;

class SendMailAction implements Action
{
    public function handle(MailatorSchedule $schedule)
    {
        try {
            $this->sendMail($schedule);
        } catch (Exception $exception) {
            report($exception);

            $schedule->markAsFailed($exception->getMessage());
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

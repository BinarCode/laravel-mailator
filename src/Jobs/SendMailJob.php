<?php

namespace Binarcode\LaravelMailator\Jobs;

use Binarcode\LaravelMailator\Actions\SendMailAction;
use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class SendMailJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public bool $deleteWhenMissingModels = true;

    public MailatorSchedule $schedule;

    /** @var string */
    public $queue;

    public function __construct(MailatorSchedule $schedule)
    {
        $this->schedule = $schedule;

        $this->queue = config('mailator.scheduler.send_mail_job_queue', 'default');
    }

    public function handle()
    {
        /** * @var SendMailAction $sendMailAction */
        $sendMailAction = app(Config::get('mailator.scheduler.send_mail_action', SendMailAction::class));

        $sendMailAction->handle($this->schedule);
    }
}

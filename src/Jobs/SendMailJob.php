<?php

namespace Binarcode\LaravelMailator\Jobs;

use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Binarcode\LaravelMailator\Support\ClassResolver;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMailJob implements ShouldQueue
{
    use ClassResolver;
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

    public function handle(): void
    {
        static::sendMailAction()->handle($this->schedule);
    }
}

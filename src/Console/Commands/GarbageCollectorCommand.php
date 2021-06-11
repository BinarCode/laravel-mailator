<?php

namespace Binarcode\LaravelMailator\Console\Commands;

use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Binarcode\LaravelMailator\Support\ClassResolver;
use Illuminate\Console\Command;

class GarbageCollectorCommand extends Command
{
    use ClassResolver;

    protected $signature = 'mailator:garbage
            {--dry : Update items completed_at column}
    ';

    protected $description = 'Will mark as complete all executed mails.';

    public function handle()
    {
        $this->info('----- Starting garbage cleaning -----');
        $ids = collect();

        MailatorSchedule::query()
            ->ready()
            ->cursor()
            ->each(function (MailatorSchedule $mailatorSchedule) use ($ids) {
                if ($this->option('dry')) {
                    static::garbageResolver()->handle($mailatorSchedule);
                } elseif (static::garbageResolver()->shouldMarkComplete($mailatorSchedule)) {
                    $ids->push($mailatorSchedule->id);
                }
            });

        if (!$this->option('dry')) {
            $ids->each(fn($i) => $this->info('Scheduler id to complete: '.$i));
        }

        $this->info("Marked as completed [".$ids->count()."] items.");
        $this->info('All done.');
    }
}

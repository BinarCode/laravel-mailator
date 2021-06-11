<?php

namespace Binarcode\LaravelMailator\Console\Commands;

use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Binarcode\LaravelMailator\Support\ClassResolver;
use Illuminate\Console\Command;

class MailatorSchedulerCommand extends Command
{
    use ClassResolver;

    protected $signature = 'mailator:run';

    protected $description = 'Run the mailator scheduler in Kernel.';

    public function handle(): int
    {
        MailatorSchedule::run();

        return 0;
    }
}

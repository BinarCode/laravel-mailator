<?php

namespace Binarcode\LaravelMailator\Console\Commands;

use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Illuminate\Console\Command;

class PruneMailatorScheduleCommand extends Command
{
    protected $signature = 'mailator:prune {--days=60 : The number of days to retain Mailator data}';

    protected $description = 'Prune stale entries from the Mailator data.';

    public function handle(): void
    {
        $this->info(
            MailatorSchedule::prune(
                now()->subDays((int)$this->option('days')),
                ['logs']
            ) . ' entries pruned.'
        );
    }
}

<?php

namespace Binarcode\LaravelMailator\Console\Commands;

use Binarcode\LaravelMailator\Models\MailatorLog;
use Illuminate\Console\Command;

class PruneMailatorLogsCommand extends Command
{
    protected $signature = 'mailator:logs:prune {--days=60 : The number of days to retain Mailator logs}';

    protected $description = 'Prune stale entries from the Mailator logs.';

    public function handle(): void
    {
        $this->info(MailatorLog::prune(now()->subDays((int)$this->option('days'))) . ' entries pruned.');
    }
}

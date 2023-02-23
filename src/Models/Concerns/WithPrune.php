<?php

namespace Binarcode\LaravelMailator\Models\Concerns;

use Binarcode\LaravelMailator\Models\MailatorSchedule;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Query\Builder;

/**
 * @mixin Model
 */
trait WithPrune
{
    public static function prune(DateTimeInterface $before)
    {
        $query = static::query()
            ->with('logs')
            ->where('created_at', '<', $before);

        $totalDeleted = 0;

        do {
            $deleted = $query->take(1000)->delete();

            $totalDeleted += $deleted;
        } while ($deleted !== 0);

        return $totalDeleted;
    }
}

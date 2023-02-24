<?php

namespace Binarcode\LaravelMailator\Models\Concerns;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

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

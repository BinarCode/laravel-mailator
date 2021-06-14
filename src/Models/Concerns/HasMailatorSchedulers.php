<?php

namespace Binarcode\LaravelMailator\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait HasTarget
 * @mixin Model
 * @package Binarcode\LaravelMailator\Models\Concerns
 */
trait HasMailatorSchedulers
{
    public function schedulers(): MorphMany
    {
        return $this->morphMany(config('mailator.scheduler.model'), 'targetable');
    }
}

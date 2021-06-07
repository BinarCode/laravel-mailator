<?php

namespace Binarcode\LaravelMailator\Models\Concerns;

use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Trait HasTarget
 * @mixin MailatorSchedule
 * @package Binarcode\LaravelMailator\Models\Concerns
 */
trait HasTarget
{
    public function targetable(): MorphTo
    {
        return $this->morphTo();
    }

    public function target(Model $target): self
    {
        $this->targetable_type = $target::class;
        $this->targetable_id = $target->getKey();

        return $this;
    }
}

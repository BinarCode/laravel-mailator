<?php

namespace Binarcode\LaravelMailator\Models\Concerns;

use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Query\Builder;

/**
 * Trait HasTarget
 * @mixin MailatorSchedule
 * @method static Builder|MailatorSchedule targetableType($class)
 * @method static Builder|MailatorSchedule mailableClass($class)
 * @method static Builder|MailatorSchedule targetableId($id)
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
        $this->targetable_type = $target->getMorphClass();
        $this->targetable_id = $target->getKey();

        return $this;
    }

    public function scopeTargetableType($query, $class)
    {
        $query->where('targetable_type', $class);
    }

    public function scopeMailableClass($query, $class)
    {
        $query->where('mailable_class', 'LIKE', "%{$class}%");
    }

    public function scopeTargetableId($query, $id)
    {
        $query->where('targetable_id', $id);
    }
}

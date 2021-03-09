<?php


namespace Binarcode\LaravelMailator\Models\Concerns;

use Binarcode\LaravelMailator\Constraints\AfterConstraint;
use Binarcode\LaravelMailator\Constraints\BeforeConstraint;
use Binarcode\LaravelMailator\Constraints\DailyConstraint;
use Binarcode\LaravelMailator\Constraints\ManyConstraint;
use Binarcode\LaravelMailator\Constraints\OnceConstraint;
use Binarcode\LaravelMailator\Constraints\SendScheduleConstraint;
use Binarcode\LaravelMailator\Constraints\WeeklyConstraint;
use Binarcode\LaravelMailator\Models\MailatorSchedule;

/**
 * Trait ConstraintsResolver
 * @mixin MailatorSchedule
 * @package Binarcode\LaravelMailator\Models\Concerns
 */
trait ConstraintsResolver
{
    public function configurationsPasses(): bool
    {
        return collect([
            BeforeConstraint::class,
            AfterConstraint::class,
            OnceConstraint::class,
            ManyConstraint::class,
            DailyConstraint::class,
            WeeklyConstraint::class,
        ])
            ->map(fn ($class) => app($class))
            ->every(fn (SendScheduleConstraint $event) => $event->canSend($this, $this->logs));
    }

    public function whenPasses(): bool
    {
        return true;
    }

    public function eventsPasses(): bool
    {
        return collect($this->constraints)
            ->map(fn (string $event) => unserialize($event))
            ->filter(fn ($event) => is_subclass_of($event, SendScheduleConstraint::class))
            ->every(fn (SendScheduleConstraint $event) => $event->canSend($this, $this->logs));
    }
}

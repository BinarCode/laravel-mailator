<?php

namespace Binarcode\LaravelMailator\Models\Concerns;

use Binarcode\LaravelMailator\Constraints\AfterConstraint;
use Binarcode\LaravelMailator\Constraints\BeforeConstraint;
use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Carbon\CarbonInterface;

/**
 * Trait HasFuture
 * @mixin MailatorSchedule
 * @package Binarcode\LaravelMailator\Models\Concerns
 */
trait HasFuture
{
    public function nextTrigger(): ?CarbonInterface
    {
        if (!$this->isFutureAction()) {
            return null;
        }

        if ($this->isOnce() && $this->last_sent_at) {
            return null;
        }

        return $this->triggerTarget();
    }

    public function isFutureAction(): bool
    {
        if ($this->isManual()) {
            return false;
        }

        if (is_null($this->timestampTarget())) {
            return false;
        }

        if (is_null($this->time_frame_origin)) {
            return false;
        }

        if ($this->isAfter()) {
            return now()->lt($this->triggerTarget());
        }

        if ($this->isBefore()) {
            if ($this->isRepetitive()) {
                return true;
            }

            return now()->lt($this->triggerTarget());
        }

        return false;
    }

    public function triggerTarget(): ?CarbonInterface
    {
        if (is_null($this->timestampTarget())) {
            return null;
        }

        if ($this->isAfter()) {
           return $this->resolveAfterTriggerTime();
        }

        if ($this->isBefore()) {
          return $this->resolveBeforeTriggerTime();
        }

        return null;
    }

    private function resolveAfterTriggerTime(): CarbonInterface
    {
        if ($this->toDays() > 0) {
            return $this->timestampTarget()->addDays($this->toDays());
        }

        if ($this->toHours() > 0) {
            return $this->timestampTarget()->addHours($this->toHours());
        }

        return $this->timestampTarget()->addMinutes($this->delay_minutes);
    }

    private function resolveBeforeTriggerTime(): CarbonInterface
    {
        if ($this->toDays() > 0) {
            return $this->timestampTarget()->subDays($this->toDays());
        }

        if ($this->toHours() > 0) {
            return $this->timestampTarget()->subHours($this->toHours());
        }

        return $this->timestampTarget()->addMinutes($this->delay_minutes);
    }
}

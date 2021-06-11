<?php

namespace Binarcode\LaravelMailator\Models\Concerns;


use Binarcode\LaravelMailator\Constraints\AfterConstraint;
use Binarcode\LaravelMailator\Constraints\BeforeConstraint;
use Binarcode\LaravelMailator\Constraints\Descriptionable;
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

        return $this->timestamp_target->clone()->addDays(
            $this->toDays()
        );
    }

    public function isFutureAction(): bool
    {
        if ($this->isManual()) {
            return false;
        }

        if (is_null($this->timestamp_target)) {
            return false;
        }

        if (is_null($this->time_frame_origin)) {
            return false;
        }

        if ($this->time_frame_origin === static::TIME_FRAME_ORIGIN_AFTER) {
            return app(AfterConstraint::class)->canSend(
                $this,
                $this->logs
            );
        }

        if ($this->time_frame_origin === static::TIME_FRAME_ORIGIN_BEFORE) {
            return app(BeforeConstraint::class)->canSend(
                $this,
                $this->logs
            );
        }

        return false;
    }
}

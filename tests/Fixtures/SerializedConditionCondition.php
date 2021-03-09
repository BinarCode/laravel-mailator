<?php

namespace Binarcode\LaravelMailator\Tests\Fixtures;

use Binarcode\LaravelMailator\Constraints\SendScheduleConstraint;
use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Illuminate\Support\Collection;

class SerializedConditionCondition implements SendScheduleConstraint
{
    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function canSend(MailatorSchedule $mailatorSchedule, Collection $logs): bool
    {
        return $this->user->email === 'john.doe@binarcode.com';
    }
}

<?php

namespace Binarcode\LaravelMailator\Tests\Fixtures;

use Binarcode\LaravelMailator\Constraints\SendScheduleConstraint;
use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class SerializedConditionCondition implements SendScheduleConstraint
{
    use SerializesModels;

    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function canSend(MailatorSchedule $schedule, Collection $logs): bool
    {
        return $this->user->email === data_get($_SERVER, 'fakeSerializedEmail', 'john.doe@binarcode.com');
    }
}

<?php

namespace Binarcode\LaravelMailator\Exceptions;

use Binarcode\LaravelMailator\Constraints\SendScheduleConstraint;
use Exception;

class InstanceException extends Exception
{
    public static function throw(string $actual)
    {
        return new static('Expected instance of ' . SendScheduleConstraint::class . ", given [$actual].");
    }
}

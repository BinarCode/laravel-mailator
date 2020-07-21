<?php

namespace Binarcode\LaravelMailator\Exceptions;

use Binarcode\LaravelMailator\MailatorEvent;
use Exception;

class InstanceException extends Exception
{
    public static function throw(string $actual)
    {
        return new static('Expected instance of '.MailatorEvent::class.", given [$actual].");
    }
}

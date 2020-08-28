<?php

namespace Binarcode\LaravelMailator\Exceptions;

use Binarcode\LaravelMailator\Models\MailTemplateable;
use Exception;

class InvalidTemplateException extends Exception
{
    public static function throw(string $actual)
    {
        return new static('Expected instance of ' . MailTemplateable::class . ", given [$actual].");
    }
}

<?php

namespace Binarcode\LaravelMailator;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Binarcode\LaravelMailator\MailatorManager
 * @mixin \Binarcode\LaravelMailator\MailatorManager
 */
class Mailator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'mailator';
    }
}

<?php

namespace Binarcode\LaravelMailator\Tests\Fixtures\ExternalHtml;

class SendGrid
{
    public static function html(): string
    {
        return "<h1>Hello ::user::</h1>";
    }
}

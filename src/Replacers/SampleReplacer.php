<?php

namespace Binarcode\LaravelMailator\Replacers;

use Binarcode\LaravelMailator\Models\MailTemplate;

class SampleReplacer implements Replacer
{
    public function replace(string $html, MailTemplate $template): string
    {
        return $html;
    }
}

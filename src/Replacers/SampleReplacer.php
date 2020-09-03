<?php

namespace Binarcode\LaravelMailator\Replacers;

use Binarcode\LaravelMailator\Models\MailTemplateable;

class SampleReplacer implements Replacer
{
    public function replace(string $html, MailTemplateable $template): string
    {
        return $html;
    }
}

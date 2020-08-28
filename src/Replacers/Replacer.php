<?php

namespace Binarcode\LaravelMailator\Replacers;

use Binarcode\LaravelMailator\Models\MailTemplateable;

interface Replacer
{
    public function replace(string $html, MailTemplateable $template): string;
}

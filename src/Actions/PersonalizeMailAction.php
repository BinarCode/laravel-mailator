<?php

namespace Binarcode\LaravelMailator\Actions;

use Binarcode\LaravelMailator\Models\MailTemplateable;
use Binarcode\LaravelMailator\Replacers\Replacer;

class PersonalizeMailAction
{
    public function execute($html, MailTemplateable $template, array $replacers): string
    {
        return collect($replacers)
            ->filter(fn (object $class) => $class instanceof Replacer)
            ->reduce(fn (string $html, Replacer $replacer) => $replacer->replace($html, $template), $html);
    }
}


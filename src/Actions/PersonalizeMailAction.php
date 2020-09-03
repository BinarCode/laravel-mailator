<?php

namespace Binarcode\LaravelMailator\Actions;

use Binarcode\LaravelMailator\Models\MailTemplateable;
use Binarcode\LaravelMailator\Replacers\Replacer;
use Closure;

class PersonalizeMailAction
{
    public function execute($html, MailTemplateable $template, array $replacers): string
    {
        $html = collect($replacers)
            ->filter(fn (object $class) => $class instanceof Replacer)
            ->reduce(fn (string $html, Replacer $replacer) => $replacer->replace($html, $template), $html);

        return collect($replacers)
            ->filter(fn (object $class) => $class instanceof Closure)
            ->reduce(fn (string $html, $replacer) => call_user_func($replacer, $html, $template), $html);
    }
}

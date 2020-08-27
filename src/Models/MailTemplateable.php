<?php

namespace Binarcode\LaravelMailator\Models;

use Illuminate\Support\Collection;

interface MailTemplateable
{
    public function htmlWithInlinedCss(): string;

    public function placeholders();

    public function preparePlaceholders(): Collection;

    public function getContent(): string;
}

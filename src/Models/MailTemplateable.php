<?php

namespace Binarcode\LaravelMailator\Models;

use Illuminate\Support\Collection;

interface MailTemplateable
{
    public function placeholders();

    public function preparePlaceholders(): Collection;

    public function getContent(): string;

    public function getSubject(): ?string;

    public function getFromEmail(): ?string;

    public function getFromName(): ?string;
}

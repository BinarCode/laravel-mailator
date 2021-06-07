<?php

namespace Binarcode\LaravelMailator\Models;

use Binarcode\LaravelMailator\Tests\Fixtures\ExternalHtml\SendGrid;
use Illuminate\Support\Collection;

class PlainHtmlTemplate implements MailTemplateable
{
    public string $html;

    public string $subject;

    public function placeholders(): array
    {
        return [
            'name' => 'name',
            'description' => 'Client name',
        ];
    }

    public function preparePlaceholders(): Collection
    {
        return collect($this->placeholders());
    }

    public function getContent(): string
    {
        return SendGrid::html();
    }

    public function getSubject(): ?string
    {
        return null;
    }

    public function getFromEmail(): ?string
    {
        return null;
    }

    public function getFromName(): ?string
    {
        return null;
    }

    public function setHtml(string $html): self
    {
        $this->html = $html;

        return $this;
    }
}

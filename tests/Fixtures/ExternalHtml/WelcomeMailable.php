<?php

namespace Binarcode\LaravelMailator\Tests\Fixtures\ExternalHtml;

use Binarcode\LaravelMailator\Models\PlainHtmlTemplate;
use Binarcode\LaravelMailator\Support\WithMailTemplate;
use Binarcode\LaravelMailator\Tests\Fixtures\User;
use Illuminate\Mail\Mailable;

class WelcomeMailable extends Mailable
{
    use WithMailTemplate;

    public User $user;

    public function __construct()
    {
        $this->user = new User([
            'name' => 'John Doe',
        ]);
    }

    public function build(): self
    {
        return $this->template(
            (new PlainHtmlTemplate)->setHtml(SendGrid::html())
        );
    }

    public function getReplacers(): array
    {
        return [
            \Binarcode\LaravelMailator\Replacers\ModelAttributesReplacer::makeWithModel($this->user),
        ];
    }
}

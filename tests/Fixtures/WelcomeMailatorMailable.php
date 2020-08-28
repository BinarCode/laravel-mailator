<?php

namespace Binarcode\LaravelMailator\Tests\Fixtures;

use Binarcode\LaravelMailator\Models\MailTemplate;
use Binarcode\LaravelMailator\Replacers\PlaceholdersReplacer;
use Binarcode\LaravelMailator\Support\WithMailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;

class WelcomeMailatorMailable extends Mailable
{
    use Queueable,
        WithMailTemplate;

    /**
     * @var Model
     */
    private Model $user;

    public function __construct(Model $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->template(MailTemplate::first());
    }

    public function getReplacers(): array
    {
        return [
            PlaceholdersReplacer::makeWithModel($this->user),
        ];
    }
}

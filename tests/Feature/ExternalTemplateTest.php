<?php

namespace Binarcode\LaravelMailator\Tests\Feature;

use Binarcode\LaravelMailator\Tests\Fixtures\ExternalHtml\WelcomeMailable;
use Binarcode\LaravelMailator\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

class ExternalTemplateTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_use_external_html_template(): void
    {
        Mail::fake();

        Mail::to('foo@bar.com')->send(new WelcomeMailable());


        Mail::assertSent(WelcomeMailable::class, function (WelcomeMailable $mailable) {
            dd($mailable->build());
        });
    }
}

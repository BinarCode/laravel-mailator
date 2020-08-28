<?php

namespace Binarcode\LaravelMailator\Tests\Feature\Models;

use Binarcode\LaravelMailator\Models\MailTemplate;
use Binarcode\LaravelMailator\Tests\Fixtures\User;
use Binarcode\LaravelMailator\Tests\Fixtures\WelcomeMailatorMailable;
use Binarcode\LaravelMailator\Tests\TestCase;

class MailTemplateTest extends TestCase
{
    public function test_can_create_mail_template()
    {
        MailTemplate::create([
            'name' => 'Welcome Email.',
            'from_email' => 'from@bar.com',
            'from_name' => 'From Bar',
            'subject' => 'Welcome to Mailator.',
            'html' => '<h1>Welcome to the party!</h1>',
            'email_html' => '<h1>Welcome to the party!</h1>',
            'webview_html' => '<h1>Welcome to the party!</h1>',
        ]);

        $this->assertDatabaseCount('mail_templates', 1);
    }

    public function test_can_send_template()
    {
        $template = MailTemplate::create([
            'name' => 'Welcome Email.',
            'from_email' => 'from@bar.com',
            'from_name' => 'From Bar',
            'subject' => 'Welcome to Mailator.',
            'html' => '<h1>Welcome to the party ::name::. You have ::age:: age.!</h1>',
            'email_html' => '<h1>Welcome to the party!</h1>',
            'webview_html' => '<h1>Welcome to the party!</h1>',
        ]);

        $template->placeholders()->create(
            [
                'name' => 'name',
                'description' => 'Name',
            ],
        );
        $template->placeholders()->create(
            [
                'name' => 'age',
                'description' => 'Age',
            ]
        );

        $dd = (new WelcomeMailatorMailable(
            new User([
                'name' => 'Eduard',
                'age' => 15,
            ])
        ))->render();

        $this->assertStringContainsString(
            "Welcome to the party Eduard. You have 15 age",
            $dd
        );
    }
}

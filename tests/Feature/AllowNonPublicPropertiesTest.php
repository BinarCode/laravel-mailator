<?php

namespace Binarcode\LaravelMailator\Tests\Feature;

use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Binarcode\LaravelMailator\Tests\Fixtures\PrivatePropertyMailable;
use Binarcode\LaravelMailator\Tests\Fixtures\ProtectedPropertyMailable;
use Binarcode\LaravelMailator\Tests\Fixtures\PublicPropertyMailable;
use Binarcode\LaravelMailator\Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class AllowNonPublicPropertiesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
    }

    public function test_can_send_email_with_private_property(): void
    {
        config()->set('mailator.serialization.enforce_public_properties', false);

        MailatorSchedule::init('private')
            ->mailable(new PrivatePropertyMailable('test'))
            ->execute();

        Mail::assertSent(PrivatePropertyMailable::class);
    }

    public function test_can_not_send_email_with_private_property(): void
    {
        config()->set('mailator.serialization.enforce_public_properties', true);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Mailable contains non-public constructor properties which cannot be safely serialized');

        MailatorSchedule::init('private')
            ->mailable(new PrivatePropertyMailable('test'))
            ->execute();
    }

    public function test_can_send_email_with_protected_property(): void
    {
        config()->set('mailator.serialization.enforce_public_properties', false);

        MailatorSchedule::init('protected')
            ->mailable(new ProtectedPropertyMailable('test'))
            ->execute();

        Mail::assertSent(ProtectedPropertyMailable::class);
    }

    public function test_can_not_send_email_with_protected_property(): void
    {
        config()->set('mailator.serialization.enforce_public_properties', true);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Mailable contains non-public constructor properties which cannot be safely serialized');

        MailatorSchedule::init('protected')
            ->mailable(new ProtectedPropertyMailable('test'))
            ->execute();
    }

    public function test_can_send_email_with_public_property(): void
    {
        config()->set('mailator.serialization.enforce_public_properties', true);

        MailatorSchedule::init('protected')
            ->mailable(new PublicPropertyMailable('test'))
            ->execute();

        Mail::assertSent(PublicPropertyMailable::class);

        config()->set('mailator.serialization.enforce_public_properties', false);

        MailatorSchedule::init('protected')
            ->mailable(new PublicPropertyMailable('test'))
            ->execute();
    }
}

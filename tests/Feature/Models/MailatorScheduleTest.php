<?php

namespace Binarcode\LaravelMailator\Tests\Feature\Models;

use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Binarcode\LaravelMailator\Tests\Fixtures\BeforeInvoiceExpiresConstraint;
use Binarcode\LaravelMailator\Tests\Fixtures\InvoiceReminderMailable;
use Binarcode\LaravelMailator\Tests\Fixtures\SingleSendingCondition;
use Binarcode\LaravelMailator\Tests\TestCase;
use Illuminate\Support\Facades\Mail;

class MailatorScheduleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_can_create_mailator_schedule()
    {
        MailatorSchedule::init('Invoice reminder.')
            ->mailable(new InvoiceReminderMailable())
            ->days(1)
            ->before(BeforeInvoiceExpiresConstraint::class)
            ->when(function () {
                return 'Working.';
            })
            ->save();

        $this->assertDatabaseCount('mailator_schedulers', 1);
    }

    public function test_sending_email_only_once()
    {
        Mail::fake();
        Mail::assertNothingSent();

        MailatorSchedule::init('Invoice reminder.')
            ->recipients([
                'zoo@bar.com',
            ])
            ->mailable(
                (new InvoiceReminderMailable())->to('foo@bar.com')
            )
            ->days(1)
            ->before(
                SingleSendingCondition::class,
            )
            ->save();

        $_SERVER['can_send'] = true;
        MailatorSchedule::run();
        Mail::assertSent(InvoiceReminderMailable::class, 1);

        $_SERVER['can_send'] = false;

        MailatorSchedule::run();
        Mail::assertSent(InvoiceReminderMailable::class, 1);

        MailatorSchedule::run();
        Mail::assertSent(InvoiceReminderMailable::class, 1);

        Mail::assertSent(InvoiceReminderMailable::class, function (InvoiceReminderMailable  $mail) {
            return $mail->hasTo('foo@bar.com') && $mail->hasTo('zoo@bar.com');
        });
    }
}

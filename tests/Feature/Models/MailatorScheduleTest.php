<?php

namespace Binarcode\LaravelMailator\Tests\Feature\Models;

use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Binarcode\LaravelMailator\Tests\Fixtures\BeforeInvoiceExpiresConstraint;
use Binarcode\LaravelMailator\Tests\Fixtures\InvoiceReminderMailable;
use Binarcode\LaravelMailator\Tests\Fixtures\SingleSendingCondition;
use Binarcode\LaravelMailator\Tests\TestCase;
use Illuminate\Foundation\Application;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\SendQueuedMailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Testing\Fakes\QueueFake;

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

    public function test_can_queue_email_from_mailator()
    {
        MailatorSchedule::init('Invoice reminder.')
            ->mailable(
                new InvoiceReminderMailable()
            )
            ->days(1)
            ->before(BeforeInvoiceExpiresConstraint::class)
            ->save();

        $mailator = MailatorSchedule::first();

        $queueFake = new QueueFake(new Application());

        $mailer = $this->getMockBuilder(Mailer::class)
            ->setConstructorArgs($this->getMocks())
            ->setMethods(['createMessage', 'to'])
            ->getMock();

        $mailer->setQueue($queueFake);
        $mailable = unserialize($mailator->mailable_class);
        $queueFake->assertNothingPushed();
        $mailer->send($mailable);
        $queueFake->assertPushedOn(null, SendQueuedMailable::class);
    }

    public function test_sending_email_only_once()
    {
        Mail::fake();
        Mail::assertNothingSent();

        MailatorSchedule::init('Invoice reminder.')
            ->recipients([
                'foo@bar.com',
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
    }
}

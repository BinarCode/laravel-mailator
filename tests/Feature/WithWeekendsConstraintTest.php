<?php

namespace Binarcode\LaravelMailator\Tests\Feature;

use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Binarcode\LaravelMailator\Tests\Fixtures\InvoiceReminderMailable;
use Binarcode\LaravelMailator\Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Spatie\TestTime\TestTime;

class WithWeekendsConstraintTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        TestTime::freeze();  // Freeze time at the beginning of each test
    }

    protected function tearDown(): void
    {
        TestTime::unfreeze();  // Unfreeze time after each test

        parent::tearDown();
    }

    public function test_can_send_mail_with_precision_at_the_given_hour(): void
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
            ->precision([5])
            ->save();

        MailatorSchedule::run();
        Mail::assertNotSent(InvoiceReminderMailable::class);

        $this->travelTo(Carbon::parse('05:00:00'));
        MailatorSchedule::run();

        Mail::assertSent(InvoiceReminderMailable::class);

        $this->travelTo(Carbon::parse('06:00:00'));

        MailatorSchedule::run();

        Mail::assertSent(InvoiceReminderMailable::class, 1);
    }

    public function test_can_set_precision_in_interval(): void
    {
        TestTime::freeze();

        Mail::fake();
        Mail::assertNothingSent();

        MailatorSchedule::init('Invoice reminder.')
            ->recipients([
                'zoo@bar.com',
            ])
            ->mailable(
                (new InvoiceReminderMailable())->to('foo@bar.com')
            )
            ->many()
            ->precision([1, 2])
            ->save();

        $this->travelTo(Carbon::parse('12:00:00'));
        MailatorSchedule::run();
        Mail::assertNotSent(InvoiceReminderMailable::class);

        $this->travelTo(Carbon::parse('01:00:00'));
        MailatorSchedule::run();
        Mail::assertSent(InvoiceReminderMailable::class);

        $this->travelTo(Carbon::parse('02:59:59'));
        MailatorSchedule::run();
        Mail::assertSent(InvoiceReminderMailable::class);

        $this->travelTo(Carbon::parse('03:00:00'));
        MailatorSchedule::run();

        Mail::assertSent(InvoiceReminderMailable::class, 2);
    }
}

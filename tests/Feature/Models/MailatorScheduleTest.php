<?php

namespace Binarcode\LaravelMailator\Tests\Feature\Models;

use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Binarcode\LaravelMailator\Tests\database\Factories\UserFactory;
use Binarcode\LaravelMailator\Tests\Fixtures\InvoiceReminderMailable;
use Binarcode\LaravelMailator\Tests\Fixtures\User;
use Binarcode\LaravelMailator\Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use Spatie\TestTime\TestTime;

class MailatorScheduleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        TestTime::freeze();
    }

    public function test_can_create_mailator_schedule(): void
    {
        $user = UserFactory::one();

        MailatorSchedule::init($name = 'Invoice reminder.')
            ->mailable(new InvoiceReminderMailable())
            ->days(1)
            ->before(now()->addWeek())
            ->target($user)
            ->when(function () {
                return 'Working.';
            })
            ->save();

        $this->assertCount(1, MailatorSchedule::all());

        self::assertSame(
            $name,
            MailatorSchedule::first()->name
        );

        self::assertCount(1, $user->schedulers);
    }

    public function test_can_use_carbon_target_date_before(): void
    {
        Mail::fake();
        Mail::assertNothingSent();

        $scheduler = MailatorSchedule::init('Invoice reminder.')
            ->recipients([
                'zoo@bar.com',
            ])
            ->mailable(
                (new InvoiceReminderMailable())->to('foo@bar.com')
            )
            ->days(1)
            ->before(now()->addDays(7));

        $scheduler->save();

        MailatorSchedule::run();
        Mail::assertNothingSent();

        TestTime::addDays(6);
        MailatorSchedule::run();

        Mail::assertSent(InvoiceReminderMailable::class, 1);
    }

    public function test_can_use_carbon_target_date_after(): void
    {
        Mail::fake();
        Mail::assertNothingSent();

        $scheduler = MailatorSchedule::init('Invoice reminder.')
            ->recipients([
                'zoo@bar.com',
            ])
            ->mailable(
                (new InvoiceReminderMailable())->to('foo@bar.com')
            )
            ->days(1)
            ->after(now()->addDays(7));

        $scheduler->save();

        MailatorSchedule::run();
        Mail::assertNothingSent();

        TestTime::addDays(8);
        MailatorSchedule::run();
        MailatorSchedule::run();

        Mail::assertSent(InvoiceReminderMailable::class, 1);
    }

    public function test_recipients_merges(): void
    {
        Mail::fake();

        $scheduler = MailatorSchedule::init('Invoice reminder.')
            ->recipients(
                $mail = 'zoo@bar.com',
            );

        self::assertSame([$mail], $scheduler->recipients);

        $scheduler->recipients([$mail2 = 'foo@bar.com']);

        self::assertSame([$mail2, $mail], $scheduler->recipients);

        $scheduler->recipients($mail3 = 'too@bar.com');

        self::assertSame([$mail3, $mail2, $mail], $scheduler->recipients);
    }

    public function test_unserialized_exception_store_exception_log(): void
    {
        Mail::fake();
        Mail::assertNothingSent();

        $scheduler = MailatorSchedule::init('Invoice reminder.')
            ->recipients([
                'zoo@bar.com',
            ])
            ->mailable(
                (new InvoiceReminderMailable($user = User::factory()->create()))->to('foo@bar.com')
            )
            ->days(1)
            ->after(now()->addDays(7));

        $scheduler->save();

        $user->forceDelete();

        $this->assertCount(0, $scheduler->logs);

        $scheduler->getMailable();

        $this->assertCount(1, $scheduler->logs()->get());
    }
}

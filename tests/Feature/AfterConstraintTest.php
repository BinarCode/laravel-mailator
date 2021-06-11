<?php

namespace Binarcode\LaravelMailator\Tests\Feature;

use Binarcode\LaravelMailator\Constraints\AfterConstraint;
use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Binarcode\LaravelMailator\Tests\Fixtures\InvoiceReminderMailable;
use Binarcode\LaravelMailator\Tests\TestCase;
use Illuminate\Support\Facades\Mail;

class AfterConstraintTest extends TestCase
{
    public function test_past_target_with_after_now_passed_after_constraint_day_bases(): void
    {
        Mail::fake();
        Mail::assertNothingSent();

        $scheduler = MailatorSchedule::init('Invoice reminder.')
            ->recipients($mail = 'zoo@bar.com')
            ->mailable(
                (new InvoiceReminderMailable())->to('foo@bar.com')
            )
            ->days(2)
            ->after(now()->subDay());

        $scheduler->save();

        $this->travel(1)->days();

        $can = app(
            AfterConstraint::class
        )->canSend(
            $scheduler,
            $scheduler->logs
        );

        self::assertTrue(
            $scheduler->fresh()->isFutureAction()
        );

        self::assertTrue(
            $can
        );
    }

    public function test_past_target_with_after_now_passed_after_constraint_hourly_bases(): void
    {
        Mail::fake();
        Mail::assertNothingSent();

        $scheduler = MailatorSchedule::init('Invoice reminder.')
            ->recipients($mail = 'zoo@bar.com')
            ->mailable(
                (new InvoiceReminderMailable())->to('foo@bar.com')
            )
            ->hours(2)
            ->after(now()->subHours(1));

        $scheduler->save();

        $this->travel(3)->hours();

        $can = app(
            AfterConstraint::class
        )->canSend(
            $scheduler,
            $scheduler->logs
        );

        self::assertFalse(
            $scheduler->fresh()->isFutureAction()
        );

        self::assertFalse(
            $can
        );
    }

}

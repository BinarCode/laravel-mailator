<?php

namespace Binarcode\LaravelMailator\Tests\Feature;

use Binarcode\LaravelMailator\Constraints\AfterConstraint;
use Binarcode\LaravelMailator\Constraints\BeforeConstraint;
use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Binarcode\LaravelMailator\Tests\Fixtures\InvoiceReminderMailable;
use Binarcode\LaravelMailator\Tests\TestCase;
use Illuminate\Support\Facades\Mail;

class BeforeConstraintTest extends TestCase
{
    public function test_past_target_with_before_now_passed_after_constraint_day_bases(): void
    {
        Mail::fake();
        Mail::assertNothingSent();

        $scheduler = MailatorSchedule::init('Invoice reminder.')
            ->recipients($mail = 'zoo@bar.com')
            ->mailable(
                (new InvoiceReminderMailable())->to('foo@bar.com')
            )
            ->days(1)
            ->before(now()->addDays(2));

        $scheduler->save();

        $this->travel(1)->days();

        self::assertFalse(
            $scheduler->fresh()->isFutureAction()
        );

        $can = app(BeforeConstraint::class)->canSend(
            $scheduler,
            $scheduler->logs
        );

        self::assertTrue(
            $can
        );
    }

    public function test_past_target_with_before_now_passed_after_constraint_hourly_bases(): void
    {
        Mail::fake();
        Mail::assertNothingSent();

        $scheduler = MailatorSchedule::init('Invoice reminder.')
            ->recipients($mail = 'zoo@bar.com')
            ->mailable(
                (new InvoiceReminderMailable())->to('foo@bar.com')
            )
            ->hours(1)
            ->before(now()->addHours(3));

        $scheduler->save();

        $this->travel(1)->hours();

        $this->travel(1)->hours();

        $can = app(
            BeforeConstraint::class
        )->canSend(
            $scheduler,
            $scheduler->logs
        );

        self::assertTrue(
            $can
        );

        $this->travel(1)->hours();

        $can = app(
            BeforeConstraint::class
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

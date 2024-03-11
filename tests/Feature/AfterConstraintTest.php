<?php

namespace Binarcode\LaravelMailator\Tests\Feature;

use Binarcode\LaravelMailator\Constraints\AfterConstraint;
use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Binarcode\LaravelMailator\Tests\Fixtures\InvoiceReminderMailable;
use Binarcode\LaravelMailator\Tests\TestCase;
use Carbon\Carbon;
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
            ->days(3)
            ->after(now()->subDay());

        $scheduler->save();

        $this->travel(1)->days();

        self::assertTrue(
            $scheduler->fresh()->isFutureAction()
        );

        $this->travel(1)->days();

        $can = app(AfterConstraint::class)->canSend(
            $scheduler,
            $scheduler->logs
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
            ->hours(3)
            ->after(now()->subHours(1));

        $scheduler->save();

        $this->travel(1)->hours();

        self::assertTrue(
            $scheduler->fresh()->isFutureAction()
        );

        $this->travel(1)->hours();

        $can = app(
            AfterConstraint::class
        )->canSend(
            $scheduler,
            $scheduler->logs
        );

        self::assertTrue(
            $can
        );

        $this->travel(1)->hours();

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

    public function test_past_target_with_after_now_passed_after_constraint_minutes_bases()
    {
        Mail::fake();
        Mail::assertNothingSent();

        $scheduler = MailatorSchedule::init('reminder')
            ->recipients('zoo@bar.com')
            ->mailable(
                (new InvoiceReminderMailable())->to('foo@bar.com')
            )
            ->minutes(10)
            ->after(now());

        $scheduler->save();

        $this->travelTo(now()->addMinutes(5));

        self::assertTrue(
            $scheduler->fresh()->isFutureAction()
        );

        $this->travelTo(now()->addMinutes(5));

        self::assertTrue(
            app(AfterConstraint::class)
                ->canSend(
                    $scheduler,
                    $scheduler->logs
                )
        );

        $this->travelTo(now()->addMinutes(5));

        //as long as we have passed the "after" minutes target this should return true
        self::assertTrue(
            app(AfterConstraint::class)
                ->canSend(
                    $scheduler,
                    $scheduler->logs
                )
        );

    }
}

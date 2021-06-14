<?php

namespace Binarcode\LaravelMailator\Tests\Feature;

use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Binarcode\LaravelMailator\Tests\Fixtures\Actions\NoopAction;
use Binarcode\LaravelMailator\Tests\Fixtures\Constraints\FailingConstraint;
use Binarcode\LaravelMailator\Tests\Fixtures\CustomAction;
use Binarcode\LaravelMailator\Tests\Fixtures\User;
use Binarcode\LaravelMailator\Tests\TestCase;
use Illuminate\Support\Facades\Mail;

class SchedulerGarbageTest extends TestCase
{
    public function test_executed_schedulers_filtered_out(): void
    {
        Mail::fake();
        Mail::assertNothingSent();

        MailatorSchedule::init('Invoice reminder.')
            ->days(1)
            ->before(now()->addDays(2))
            ->actionClass(
                new CustomAction(
                    $user = User::factory()->create([
                        'email_verified_at' => null,
                    ])
                )
            )
            ->save();

        self::assertCount(
            1,
            MailatorSchedule::query()
                ->ready()
                ->get()
        );

        $this->travel(1)->days();
        MailatorSchedule::run();

        self::assertCount(
            0,
            MailatorSchedule::query()
                ->ready()
                ->get()
        );
    }

    public function test_three_failed_is_considered_completed(): void
    {
        Mail::fake();
        Mail::assertNothingSent();

        $schedule = MailatorSchedule::init('Invoice reminder.')
            ->days(1)
            ->before(now()->addDays(2))
            ->constraint(new FailingConstraint)
            ->actionClass(new NoopAction);

        $schedule->save();

        self::assertCount(
            1,
            MailatorSchedule::query()
                ->ready()
                ->get()
        );

        $this->travel(1)->days();
        MailatorSchedule::run();
        MailatorSchedule::run();
        MailatorSchedule::run();

        self::assertCount(
            0,
            MailatorSchedule::query()
                ->ready()
                ->get()
        );
    }
}

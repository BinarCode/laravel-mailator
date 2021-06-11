<?php

namespace Binarcode\LaravelMailator\Tests\Feature\Console;

use Binarcode\LaravelMailator\Console\Commands\MailatorSchedulerCommand;
use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Binarcode\LaravelMailator\Tests\Fixtures\Actions\NoopAction;
use Binarcode\LaravelMailator\Tests\Fixtures\Constraints\FailingConstraint;
use Binarcode\LaravelMailator\Tests\Fixtures\Constraints\TrueConstraint;
use Binarcode\LaravelMailator\Tests\Fixtures\CustomAction;
use Binarcode\LaravelMailator\Tests\Fixtures\User;
use Binarcode\LaravelMailator\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MailatorScheduleCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_failing_during_run_dont_stop_execution(): void
    {
        MailatorSchedule::init('A')
            ->constraint(new FailingConstraint)
            ->actionClass(new NoopAction)
            ->save();

        MailatorSchedule::init('B')
            ->constraint(new TrueConstraint)
            ->actionClass(
                new CustomAction(
                $user = User::factory()->create([
                        'email_verified_at' => null,
                    ])
            )
            )
            ->save();


        $this->artisan(
            MailatorSchedulerCommand::class
        );

        self::assertTrue($user->fresh()->hasVerifiedEmail());
    }
}

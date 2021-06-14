<?php

namespace Binarcode\LaravelMailator\Tests\Feature;

use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Binarcode\LaravelMailator\Tests\Fixtures\CustomAction;
use Binarcode\LaravelMailator\Tests\Fixtures\User;
use Binarcode\LaravelMailator\Tests\TestCase;
use Illuminate\Support\Facades\Mail;

class SchedulerCustomActionTest extends TestCase
{
    public function test_can_serialize_custom_actions(): void
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

        self::assertNull($user->fresh()->email_verified_at);

        $this->travel(1)->days();

        MailatorSchedule::run();

        self::assertNotNull($user->fresh()->email_verified_at);
    }
}

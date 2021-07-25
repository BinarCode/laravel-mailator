<?php

namespace Binarcode\LaravelMailator\Tests\Feature;

use Binarcode\LaravelMailator\Scheduler;
use Binarcode\LaravelMailator\Tests\database\Factories\UserFactory;
use Binarcode\LaravelMailator\Tests\Fixtures\InvoiceReminderMailable;
use Binarcode\LaravelMailator\Tests\TestCase;
use Illuminate\Support\Facades\Mail;

class UniqueSchedulerTest extends TestCase
{
    public function test_unique_per_target_will_prevent_duplications(): void
    {
        Mail::fake();
        $user = UserFactory::one();

        Mail::assertNothingSent();

        Scheduler::init()
            ->mailable(new InvoiceReminderMailable())
            ->target($user)
            ->unique()
            ->save();

        Scheduler::init()
            ->mailable(new InvoiceReminderMailable())
            ->target($user)
            ->unique()
            ->save();

        self::assertCount(1, $user->schedulers()->get());

        Scheduler::init()
            ->mailable(new InvoiceReminderMailable())
            ->target($user)
            ->save();

        self::assertCount(2, $user->schedulers()->get());
    }

    public function test_second_unique_doesnt_send_when_execute(): void
    {
        Mail::fake();
        $user = UserFactory::one();

        Mail::assertNothingSent();

        Scheduler::init()
            ->mailable(new InvoiceReminderMailable())
            ->target($user)
            ->unique()
            ->execute(true);

        Mail::assertSent(InvoiceReminderMailable::class, 1);

        Scheduler::init()
            ->mailable(new InvoiceReminderMailable())
            ->target($user)
            ->unique()
            ->execute(true);

        Mail::assertSent(InvoiceReminderMailable::class, 1);
    }
}

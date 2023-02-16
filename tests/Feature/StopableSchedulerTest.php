<?php

namespace Binarcode\LaravelMailator\Tests\Feature;

use Binarcode\LaravelMailator\Scheduler;
use Binarcode\LaravelMailator\Tests\Fixtures\Constraints\DynamicContraint;
use Binarcode\LaravelMailator\Tests\Fixtures\InvoiceReminderMailable;
use Binarcode\LaravelMailator\Tests\TestCase;
use Illuminate\Support\Facades\Mail;

class StopableSchedulerTest extends TestCase
{
    public function test_stopable_scheduler_mark_completed_when_constraint_falsy(): void
    {
        Mail::fake();

        $invoiceExpiration = now()->addDays(10);

        Mail::assertNothingSent();

        $scheduler = Scheduler::init()
            ->mailable(new InvoiceReminderMailable())
            ->constraint(new DynamicContraint())
            ->daily()
            ->days(7)
            ->before($invoiceExpiration)
            ->stopable();

        $scheduler->save();

        Scheduler::run();

        Mail::assertNothingSent();

        $this->travel(4)->days();

        Scheduler::run();
        Mail::assertSent(InvoiceReminderMailable::class, 1);

        $this->travel(1)->days();

        Scheduler::run();
        Mail::assertSent(InvoiceReminderMailable::class, 2);
        $this->assertFalse($scheduler->isCompleted());

        $_SERVER['constraints.shouldSend'] = false;

        $this->travel(1)->days();

        Scheduler::run();
        Mail::assertSent(InvoiceReminderMailable::class, 2);
        $this->assertTrue($scheduler->fresh()->isCompleted());

        $_SERVER['constraints.shouldSend'] = true;
        $this->travel(1)->days();

        Scheduler::run();
        Mail::assertSent(InvoiceReminderMailable::class, 2);
    }
}

<?php

namespace Binarcode\LaravelMailator\Tests\Feature;

use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Binarcode\LaravelMailator\Scheduler;
use Binarcode\LaravelMailator\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SchedulerFacadeTest extends TestCase
{
    use RefreshDatabase;

    public function test_scheduler_methods_works(): void
    {
        self::assertInstanceOf(
            MailatorSchedule::class,
            Scheduler::init('Test')
        );

        Scheduler::__destruct();

        $this->assertDatabaseHas('mailator_schedulers', [
            'name' => 'Test',
        ]);
    }
}

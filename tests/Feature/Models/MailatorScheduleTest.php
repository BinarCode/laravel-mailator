<?php

namespace Binarcode\LaravelMailator\Tests\Feature\Models;

use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Binarcode\LaravelMailator\Tests\TestCase;

class MailatorScheduleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_can_create_mailator_schedule()
    {
        $mailator = new MailatorSchedule;

        $mailator->name = 'Sample schedule.';

        $mailator->save();

        $this->assertDatabaseCount('mailator_schedulers', 1);
    }

}

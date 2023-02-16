<?php

namespace Binarcode\LaravelMailator\Tests\Feature;

use Binarcode\LaravelMailator\Scheduler;
use Binarcode\LaravelMailator\Tests\Fixtures\InvoiceReminderMailable;
use Binarcode\LaravelMailator\Tests\TestCase;

class ConstraintableTest extends TestCase
{
    public function test_mailable_could_define_intern_constraints(): void
    {
        $scheduler = Scheduler::init('Invoice reminder.')
            ->recipients('zoo@bar.com')
            ->mailable(new InvoiceReminderMailable);

        $scheduler->save();

        $this->assertCount(1, $scheduler->getConstraints());
    }
}

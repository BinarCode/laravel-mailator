<?php

namespace Binarcode\LaravelMailator\Tests\Feature;

use Binarcode\LaravelMailator\Models\MailatorSchedule;
use Binarcode\LaravelMailator\Tests\Fixtures\InvoiceReminderMailable;
use Binarcode\LaravelMailator\Tests\Fixtures\Post;
use Binarcode\LaravelMailator\Tests\Fixtures\SerializedConditionCondition;
use Binarcode\LaravelMailator\Tests\Fixtures\User;
use Binarcode\LaravelMailator\Tests\TestCase;
use Illuminate\Support\Facades\Mail;

class SerializationTest extends TestCase
{
    public function test_constraints_gets_latest_database_values(): void
    {
        Mail::fake();
        Mail::assertNothingSent();

        $user = User::factory()->has(
            Post::factory()->state([
                'title' => 'Test title',
            ]),
            'posts'
        )->create([
            'email' => 'john.doe@binarcode.com',
        ]);

        $scheduler = MailatorSchedule::init('Invoice reminder.')
            ->recipients([
                'zoo@bar.com',
            ])
            ->mailable(
                (new InvoiceReminderMailable())->to('foo@bar.com')
            )
            ->many()
            ->constraint(
                new SerializedConditionCondition($user)
            );

        $scheduler->save();

        $user->update([
            'email' => $newEmail = 'foo@bar.com',
        ]);

        $_SERVER['fakeSerializedEmail'] = $newEmail;

        MailatorSchedule::run();

        Mail::assertSent(InvoiceReminderMailable::class, 1);
    }
}

<?php

namespace Binarcode\LaravelMailator\Tests\Fixtures;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;

class InvoiceReminderMailable extends Mailable implements ShouldQueue
{
    use Queueable;

    public function build()
    {
        $this
            ->subject('lorem ipsum')
            ->view('laravel-mailator::mails.stub_invoice_reminder_view')
            ->to('foo@example.tld');

        return $this;
    }
}

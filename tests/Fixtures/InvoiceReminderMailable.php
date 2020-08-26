<?php

namespace Binarcode\LaravelMailator\Tests\Fixtures;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class InvoiceReminderMailable extends Mailable
{
    use Queueable;

    public function build()
    {
        return $this->view('laravel-mailator::mails.stub_invoice_reminder_view');
    }
}

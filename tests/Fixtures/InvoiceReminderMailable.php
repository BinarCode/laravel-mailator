<?php

namespace Binarcode\LaravelMailator\Tests\Fixtures;

use Binarcode\LaravelMailator\Constraints\Constraintable;
use Binarcode\LaravelMailator\Tests\Fixtures\Constraints\DynamicContraint;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceReminderMailable extends Mailable implements Constraintable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        private ?User $user = null,
    ) {
    }

    public function build()
    {
        return $this->view('laravel-mailator::mails.stub_invoice_reminder_view');
    }

    public function constraints(): array
    {
        return [
            new DynamicContraint
        ];
    }
}

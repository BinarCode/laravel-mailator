<?php


namespace Binarcode\LaravelMailator\Tests\Fixtures;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PublicPropertyMailable extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $name
    ) {
    }

    public function build()
    {
        return $this->view('laravel-mailator::mails.stub_invoice_reminder_view');
    }
}

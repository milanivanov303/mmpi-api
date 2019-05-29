<?php

namespace Modules\Certificates\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class CheckExpiryMail extends \Illuminate\Mail\Mailable
{
    use Queueable, SerializesModels;

    /**
     * Certificate expiry data.
     *
     * @var array
     */
    public $data;

    /**
     * Create a new message instance.
     *
     * @param array $data
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.check-expiry')->with($this->data);
    }
}

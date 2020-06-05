<?php

namespace Modules\ProjectEvents\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class NewEstimationMail extends \Illuminate\Mail\Mailable
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
        return $this
                ->view('mails.new-estimation')
                ->subject('New estimated effort has been added for ' . $this->data['project'])
                ->with($this->data);
    }
}

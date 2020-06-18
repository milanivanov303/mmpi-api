<?php

namespace Modules\InstanceDowntimes\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InstanceDowntimeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Mail to data.
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
        $this->from($data['from']);
        $this->to($data['to']);
        $this->cc($data['cc']);
        
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
                ->view($this->data['template'])
                ->subject("INF: {$this->data['instance']} - Downtime ")
                ->with($this->data);
    }
}

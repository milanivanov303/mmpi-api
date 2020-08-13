<?php

namespace Modules\ProjectEvents\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class ImportEventsMail extends Mailable
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
        $this->from("api-mmpi@codix.bg", "MMPI API auto");
        $this->to(Auth::user()->email);
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
            ->view('mails.import-project-events')
            ->subject('Project events import failures.')
            ->with($this->data);
    }
}

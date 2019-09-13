<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MissingProjectDevKeyMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Project name
     *
     * @var string
     */
    public $projectName;

    /**
     * Create a new message instance.
     *
     * @param string $projectName
     * @return void
     */
    public function __construct(string $projectName)
    {
        $this->projectName = $projectName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
                ->html("Please fill tts_dev_project_key for project {$this->projectName}")
                ->subject('MMPI Notification - Missing tts dev project key for ' . $this->projectName);
    }
}

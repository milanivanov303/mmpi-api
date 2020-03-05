<?php

namespace Modules\Projects\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class ProjectRolesChangeMail extends \Illuminate\Mail\Mailable
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
    public function __construct(string $projectName, string $userName, string $link)
    {
        $this->data =
        [
            'projectName' => $projectName,
            'userName'    => $userName,
            'link'        => $link
        ];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
                ->view('mails.project-roles-change')
                ->subject('MMPI Notification - ' .  $this->data['projectName'] . ' - Project Roles Has Been Changed')
                ->with($this->data);
    }
}

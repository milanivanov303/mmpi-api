<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Base extends Mailable
{
    use Queueable, SerializesModels;

    const PROD_ENV = 'production';
    const INT_ENV = 'integration';
    const DEV_ENV = 'local';

    public $original = [];

    /**
     * Set testing recipients
     *
     * @param $to
     * @param string $cc
     * @param string $bcc
     */
    public function setRecipients($to, $cc = '', $bcc = '')
    {
        $this->original = [
            'to' => $to,
            'cc' => $cc,
            'bcc' => $bcc
        ];

        if (app('env') !== self::PROD_ENV) {
            $testMail = config('mail.recruitment_test_mail');

            $mails = explode(',', $testMail);
            foreach ($mails as $mail) {
                $this->to($mail);
            }

            return;
        }

        $recipientTypes = ['to' => $to, 'cc' => $cc, 'bcc' => $bcc];
        foreach ($recipientTypes as $key => $type) {
            if (!empty($type)) {
                $data = explode(', ', $type);
                foreach ($data as $item) {
                    $this->{$key}($item);
                }
            }
        }
    }

    /**
     * Get original recipients
     *
     * @return array
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * Set environment in subject
     *
     * @param string $subject
     * @return $this
     */
    public function subject($subject)
    {
        if (app('env') === self::DEV_ENV) {
            $subject = '[DEV] ' . $subject;
        }

        if (app('env') === self::INT_ENV) {
            $subject = '[INT] ' . $subject;
        }

        return parent::subject($subject);
    }
}

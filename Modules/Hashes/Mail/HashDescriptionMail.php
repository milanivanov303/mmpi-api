<?php

namespace Modules\Hashes\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HashDescriptionMail extends Mailable
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
        $hashCommit = $this->data['hashCommit'];

        return $this
                ->view('mails.hash-description')
                ->subject('MMPI Notification - Hash Commit Message - ' . $hashCommit->hash_rev)
                ->with(
                    array_merge(
                        [
                            'getToName' => function () use ($hashCommit) {
                                return $hashCommit->committedBy ? $hashCommit->committedBy->name : '';
                            },
                            'getRepoLink' => function () use ($hashCommit) {
                                if ($hashCommit->repoType->subtype === 'repo_hg') {
                                    return $hashCommit->repoType->url . '/rev/' . $hashCommit->hash_rev;
                                }

                                return '#!';
                            }
                        ],
                        $this->data
                    )
                );
    }
}

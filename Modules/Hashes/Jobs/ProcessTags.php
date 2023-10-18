<?php

namespace Modules\Hashes\Jobs;

use Illuminate\Support\Facades\Mail;
use Modules\Hashes\Mail\HashDescriptionMail;
use Modules\Hashes\Models\HashCommit;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Hashes\Services\DescriptionParserService;
use Modules\Hashes\Services\TagsService;
use Illuminate\Support\Facades\Log;

class ProcessTags implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var HashCommit
     */
    protected $hashCommit;
    
     /**
     * Whether to send mails or not. Added for command hashes:synchronize
     * @var bool
     */
    protected $validateDescription;

    /**
     * Create a new job instance.
     *
     * @param HashCommit $hashCommit
     * @return void
     */
    public function __construct(HashCommit $hashCommit, $validateDescription = true)
    {
        $this->hashCommit = $hashCommit;
        $this->validateDescription = $validateDescription;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (is_null($this->hashCommit->commit_description)) {
            return Log::channel('tags')->info("Description for hash '{$this->hashCommit->hash_rev}' is empty!");
        }

        $description = new DescriptionParserService($this->hashCommit->commit_description);

        Log::channel('tags')->info("Start processing tags for hash '{$this->hashCommit->hash_rev}'");
        Log::channel('tags')->info("Description '{$this->hashCommit->commit_description}'");

        $tags = new TagsService($this->hashCommit, $description);
        $tags->save();

        Log::channel('tags')->info("End" . PHP_EOL);
    }
}

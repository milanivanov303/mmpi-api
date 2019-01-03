<?php

namespace Modules\Hashes\Jobs;

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
     * Create a new job instance.
     *
     * @param HashCommit $hashCommit
     * @return void
     */
    public function __construct(HashCommit $hashCommit)
    {
        $this->hashCommit = $hashCommit;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::channel('tags')->info("Start processing tags for hash '{$this->hashCommit->id}'");
        Log::channel('tags')->info("Description '{$this->hashCommit->commit_description}'");

        $parser = new DescriptionParserService($this->hashCommit->commit_description);

        $tags = new TagsService($this->hashCommit, $parser);
        $tags->save();

        Log::channel('tags')->info("End" . PHP_EOL);
    }

    /**
     * The job failed to process.
     *
     * @param \Exception  $e
     * @return void
     */
    public function failed(\Exception $e)
    {
        Log::channel('tags')->warning($e->getMessage());

        // Send user notification of failure, etc...
        //var_dump($e->getMessage());
    }
}

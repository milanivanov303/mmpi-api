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

    protected $model;

    /**
     * Create a new job instance.
     *
     * @param HashCommit $model
     * @return void
     */
    public function __construct(HashCommit $model)
    {
        $this->model = $model;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::channel('tags')->info("Start processing tags for hash '{$this->model->id}'");
        Log::channel('tags')->info("Description '{$this->model->commit_description}'");

        $parser = new DescriptionParserService($this->model->commit_description);

        $tags = new TagsService($this->model, $parser);
        $tags->save();

        Log::channel('tags')->info("End" . PHP_EOL);
    }

    /**
     * The job failed to process.
     *
     * @param \Exception  $exception
     * @return void
     */
    public function failed(\Exception $exception)
    {
        // Send user notification of failure, etc...
        var_dump($exception->getMessage());
    }
}

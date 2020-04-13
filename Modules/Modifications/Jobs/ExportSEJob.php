<?php

namespace Modules\Modifications\Jobs;

use App\Helpers\Broadcast;
use Core\Jobs\Job;
use Modules\Modifications\Models\SeTransferModification;
use Modules\Modifications\Services\SeService;

class ExportSEJob extends Job
{
    /**
     * @var array
     */
    protected $data;

    /**
     * SeTransferModification model instance
     *
     * @var SeTransferModification
     */
    protected $seTransfer;

    /**
     * Create a new job instance.
     *
     * @param string $data
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle()
    {
        $this->seTransfer = $this->data['model'];

        $exported = $this->export();
        if (!$exported) {
            // $this->seTransfer->update(['comments' => 'SE export failed']);
            $this->seTransfer->delete();
            return false;
        }
        
        // SSH demon in container needs time to start
        sleep(5);
        
        return true;
    }

    /**
     * Export
     *
     * @return bool
     */
    protected function export() : bool
    {
        try {
            $ssh2 = app('SeExport', [
                'instance' => $this->data['instance']['host']
            ]);
            $export = new SeService(
                $ssh2,
                $this->seTransfer->subtype_id,
                $this->seTransfer->delivery_chain_id,
                $this->data['instance']['user'],
                $this->seTransfer->contents,
                function (array $message) {
                    $this->broadcast($message);
                }
            );
            $result = $export->run();
        } catch (\Exception $e) {
            $this->broadcast([
                'action' => 'export',
                'status' => 'failed',
                'comments' => $e->getMessage()
            ]);
            $result = false;
        }

        return $result;
    }

    /**
     * Broadcast progress
     *
     * @param array $message
     */
    protected function broadcast(array $message)
    {
        if (array_key_exists('comments', $message) && $message['comments']) {
            $this->seTransfer->update(['comments' => "{$message['comments']}"]);
        }

        if (array_key_exists('artifact', $message) && $message['artifact']) {
            $this->seTransfer->update(['maven_repository' => "{$message['artifact']}"]);
        }

        if (array_key_exists('version', $message) && $message['version']) {
            $this->seTransfer->update(['version' => "{$message['version']}"]);
        }

        Broadcast::topic(
            $this->data['broadcast']['queue'],
            $message,
            [
                'queue_durable'     => $this->data['broadcast']['durable'],
                'queue_auto_delete' => $this->data['broadcast']['auto_delete']
            ]
        );
    }
}

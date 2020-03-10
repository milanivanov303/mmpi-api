<?php

namespace Modules\Modifications\Jobs;

use App\Helpers\Broadcast;
use Core\Jobs\Job;
use Illuminate\Support\Facades\Storage;
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
            $username = config('app.ssh.username');
            $password = config('app.ssh.password');
            $port     = config('app.ssh.port');
            $key      = Storage::get(config('app.ssh.public_key'));
            $host     = strpos($this->data['instance']['host'], '.codixfr.private')
                        ? $this->data['instance']['host']
                        : $this->data['instance']['host'] . '.codixfr.private';
            $ssh2 = new \Core\Helpers\SSH2($host, $port);
            
            // login using public key
            if ($key) {
                if (!$ssh2->loginRSA($username, $key)) {
                    // login with password
                    if (!$ssh2->login($username, $password)) {
                        throw new \Exception("Could not login to {$host}");
                    }
                }
            } else {
                if (!$ssh2->login($username, $password)) {
                    throw new \Exception("Could not login to {$host}");
                }
            }

            $export = new SeService(
                $ssh2,
                $this->seTransfer->subtype_id,
                $this->data['delivery_chain_id'],
                $this->data['instance']['user'],
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

<?php

namespace Modules\Modifications\Jobs;

use App\Helpers\Broadcast;
use Core\Jobs\Job;
use Illuminate\Support\Facades\Storage;
use Modules\Modifications\Models\SeTransferModification;
use Modules\Modifications\Services\SeService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
        $this->seTransfer = new SeTransferModification([
            'type_id'           => $this->data['type_id'],
            'subtype_id'        => $this->data['subtype_id'],
            'name'              => $this->data['name'],
            'issue_id'          => $this->data['issue_id'],
            'active'            => $this->data['active'],
            'visible'           => $this->data['visible'],
            'issue_id'          => $this->data['issue_id'],
            'delivery_chain_id' => $this->data['delivery_chain_id'],
            'instance_status'   => $this->data['instance_status'],
            'instance'          => $this->data['instance'],
            'created_by_id'     => Auth::user()->id,
            'created_on'        => Carbon::now()->format('Y-m-d H:i:s'),
            'comments'          => 'exporting'
        ]);
        $this->seTransfer->save(); // Got to resolve double modif save !

        $exported = $this->export();
        if (!$exported) {
            $this->seTransfer->update(['comments' => 'SE export failed']);
            return false;
        }
        
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
                $this->data['subtype_id'],
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

<?php

namespace Modules\Modifications\Jobs;

use App\Helpers\Broadcast;
use Core\Jobs\Job;
use Illuminate\Support\Facades\Storage;
use Modules\Modifications\Models\SeTransferModification;
use Modules\Modifications\Services\SeService;
use Modules\Modifications\Helpers\SSHConnect;

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
            'version'           => $this->data['version'],
            'delivery_chain_id' => $this->data['chain'],
            'instance_status'   => $this->data['instance_status'],
            'instance'          => $this->data['instance'],
            'created_by'        => $this->data['created_by'],
            'created_on'        => time(),
            'comment'           => 'building'
        ]);
        $this->seTransfer->save();

        $exported = $this->export();
        if (!$exported) {
            $this->seTransfer->update(['comment' => 'SE export failed']);
            return false;
        }

        // SSH demon in container needs time to start
        sleep(5);

        $this->seTransfer->update(['comment' => 'exporting']);
        
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

            //$ssh = new SSHConnect($host, $port, $username, $key, $password);
            $ssh2 = new \Core\Helpers\SSH2($host, $port);
            
            // login using public key
            if (!$key) {
                throw new \Exception("Could not find public key for {$host}");
            }
            if (!$ssh2->loginRSA($username, $key)) {
                // login with password
                if (!$ssh2->login($username, $password)) {
                    throw new \Exception("Could not login to {$host}");
                }
            }

            $export = new SeService(
                $ssh2,
                // $this->data['version'],
                // $this->data['subtype_id'],
                // $this->data['delivery_chain_id'],
                function (array $message) {
                    $this->broadcast($message);
                }
            );
            $result = $export->run();
        } catch (\Exception $e) {
            $this->broadcast([
                'action' => 'buid',
                'status' => 'failed',
                'comment' => $e->getMessage()
            ]);
            $result = false;
        }

        return $result;
    }

    /**
     * Deploy
     *
     * @return bool
     */
    protected function deploy() : void
    {
        // try {
        //     $host     = parse_url(config('app.extranet.docker.url'), PHP_URL_HOST);
        //     $port     = $this->container['NetworkSettings']['Ports']['22/tcp'][0]['HostPort'];
        //     $username = 'ex1';
        //     $password = 'Sofphia';

        //     $sftp = new SFTP($host, $port, 600);
        //     if (!$sftp->login($username, $password)) {
        //         throw new \Exception("Could not login to instance {$host}:{$port}");
        //     }

        //     $deploy = new DeployService(
        //         $sftp,
        //         $this->getLocalFile(),
        //         function (array $message) {
        //             $this->broadcast($message);
        //         }
        //     );

        //     $result = $deploy->run();
        // } catch (\Exception $e) {
        //     $this->broadcast([
        //         'action' => 'deploy',
        //         'status' => 'failed',
        //         'log' => $e->getMessage()
        //     ]);
        //     $result = false;
        // }

        // $sftp->disconnect();
        // return $result;
    }

    /**
     * Broadcast progress
     *
     * @param array $message
     */
    protected function broadcast(array $message)
    {
        if (array_key_exists('log', $message) && $message['log']) {
            $this->seTransfer->newQuery()
                ->setBindings([$message['log']])
                ->update(['log' => 'concat(`log`, ?)']);
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

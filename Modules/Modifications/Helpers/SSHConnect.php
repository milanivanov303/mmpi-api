<?php

namespace Modules\Modifications\Helpers;

use Illuminate\Support\Facades\Storage;

class SSHConnect
{
    /**
     * @var \Core\Helpers\SSH2
     */
    protected $ssh2;

    /**
     * SSH connect constructor.
     *
     * @param string $host
     * @param int $port
     * @param string $username
     * @param string $password
     * @param string $publicKey
     * @throws \Exception
     */
    public function __construct(string $host, int $port, string $username, string $publicKey, string $password)
    {
        $this->ssh2 = new \Core\Helpers\SSH2($host, $port);

        $key = Storage::get($publicKey);
        
        // login using public key
        if (!$publicKey) {
            throw new \Exception("Could not find public key for {$host}");
        }
        
        if (!$this->ssh2->loginRSA($username, $key)) {
            // login with password
            if (!$this->ssh2->login($username, $password)) {
                throw new \Exception("Could not login to {$host}");
            }
        }

        return $this->ssh2;
    }
}

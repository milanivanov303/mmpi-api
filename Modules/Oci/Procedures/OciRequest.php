<?php

namespace Modules\Oci\Procedures;

use Illuminate\Support\Facades\Log;
use Modules\Modifications\Services\SeService;

class OciRequest
{
    /**
     * Oci connection
     *
     * @var object
     */
    protected $oci = '';

    /**
     * Oci select
     *
     * @var string
     */
    protected $query = '';

    /**
     * OciRequest constructor
     *
     * @param $oci
     * @param array $query
     */
    public function __construct($oci, array $query)
    {
        $this->oci   = $oci;
        $this->query = $this->getQuery($query);
    }

    /**
     * Select data
     *
     * @return array
     * @throws \Exception
     */
    public function run()
    {
        $stid = oci_parse($this->oci, $this->query);
        if (oci_statement_type($stid) != "SELECT") {
            Log::error("Only oci SELECT statements are allowed - \"{$this->query}");
            throw new \Exception("Only oci SELECT statements are allowed!");
        }

        oci_execute($stid);
        oci_fetch_all($stid, $data);
        oci_free_statement($stid);
        oci_close($this->oci);
        return $data;
    }

    /**
     * Get oci select for operation
     *
     * @param array $selectData
     * @return string
     * @throws \Exception
     */
    protected function getQuery($data) : string
    {
        if (isset($data['query'])) {
            return $data['query'];
        }

        if (isset($data['operation'])) {
            switch ($data['operation']['key']) {
                case SeService::BTPROC:
                    $query = "SELECT ecran FROM v_domaine WHERE type = 'filiere'";
                    return $query;
                    break;
                case SeService::BTTEXT:
                    $query = "SELECT DISTINCT texte FROM brregleediteur";
                    return $query;
                    break;
                default:
                    throw new \Exception("Not provided oci select or valid operation!");
            }
        }
    }
}

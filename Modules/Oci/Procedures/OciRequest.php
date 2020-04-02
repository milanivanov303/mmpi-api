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
    protected $select = '';

    /**
     * OciRequest constructor
     *
     * @param $oci
     * @param array $query
     */
    public function __construct($oci, array $query)
    {
        $this->oci    = $oci;
        $this->select = $this->checkSelect($query);
    }

    /**
     * Select data
     *
     * @return array
     * @throws \Exception
     */
    public function run()
    {
        $stid = oci_parse($this->oci, $this->select);
        if (oci_statement_type($stid) != "SELECT") {
            Log::error("Only oci SELECT statements are allowed - \"{$this->select}");
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
    protected function checkSelect($selectData) : string
    {
        if (isset($selectData['select'])) {
            return $selectData['select'];
        }

        if (isset($selectData['operation'])) {
            $operation = $selectData['operation']['key'];
            switch ($operation) {
                case SeService::BTPROC:
                    $select = "SELECT ecran FROM v_domaine WHERE type = 'filiere'";
                    break;
                case SeService::BTTEXT:
                    $select = "SELECT DISTINCT texte FROM brregleediteur";
                    break;
                default:
                    throw new \Exception("Not provided oci select or valid operation!");
            }
            return $select;
        }
    }
}

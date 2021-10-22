<?php

namespace Modules\Hashes\Helpers\JsonSchema\Filters;

use App\Models\EnumValue;
use Core\Helpers\Utils;
use Modules\Branches\Models\Branch;
use Modules\Hashes\Models\HashCommit;
use Opis\JsonSchema\IFilter;

class CheckHashBranchFilter implements IFilter
{
    /**
     * Validate
     *
     * @param mixed $data
     * @param array $args
     * @return bool
     */
    public function validate($data, array $args) : bool
    {
        $branchId = app(Branch::class)->getModelId((array) $data, 'name', [
            'repo_type_id' => $this->getRepoTypeId($data, $args),
            'status' => 1
        ]);

        if ($branchId) {
            return true;
        }

        return false;
    }

    /**
     * Get repo type id
     *
     * @param array $args
     * @param mixed $data
     * @return int|null
     */
    protected function getRepoTypeId($data, array $args) : ?int
    {
        if (array_key_exists('repo_type', $args)) {
            return app(EnumValue::class)->getModelId((array) $args['repo_type'], 'key', [
                'type' =>'repository_type'
            ]);
        }

        return $data->repo_type_id;
    }
}

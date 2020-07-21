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
     * @param mixed $value
     * @param array $args
     * @return bool
     */
    public function validate($value, array $args) : bool
    {
        $branchId = app(Branch::class)->getModelId((array) $value, 'name', [
            'repo_type_id' => $this->getRepoTypeId($args),
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
     * @return int|null
     */
    protected function getRepoTypeId(array $args) : ?int
    {
        if (array_key_exists('repo_type', $args)) {
            return app(EnumValue::class)->getModelId((array) $args['repo_type'], 'key', [
                'type' =>'repository_type'
            ]);
        }

        $primaryKey      = "hash_rev";
        $primaryKeyValue = Utils::getPropertyValueFromUrl($primaryKey);

        return app(HashCommit::class)->where($primaryKey, $primaryKeyValue)->value('repo_type_id');
    }
}

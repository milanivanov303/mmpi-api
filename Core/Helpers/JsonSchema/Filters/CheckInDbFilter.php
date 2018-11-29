<?php

namespace Core\Helpers\JsonSchema\Filters;

use Opis\JsonSchema\IFilter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CheckInDbFilter implements IFilter
{
    /**
     * Primary key column
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Validate
     *
     * @param $value
     * @param array $args
     * @return bool
     */
    public function validate($value, array $args): bool
    {
        // skip validation for null values
        if (is_null($value)) {
            return true;
        }

        if ($this->isUniqueCheckOnUpdate($args['rule'])) {
            $args['rule'] = $this->getUniqueRuleOnUpdate($args, $value);
        }

        return Validator::make(['field' => $value], [
            'field' => $args['rule'],
        ])->passes();
    }

    /**
     * Check if it is update with unique check
     *
     * @param string $rule
     * @return boolean
     */
    protected function isUniqueCheckOnUpdate(string $rule): bool
    {
        return app('request')->method() === 'PUT' && strpos($rule, 'unique:') !== false;
    }

    /**
     * Skip record primary key value from unique check
     *
     * @param array $args
     * @param mixed $value
     * @return string
     */
    protected function getUniqueRuleOnUpdate(array $args, $value): string
    {
        list($table, $column) = explode(',', last(explode(':', $args['rule'])));

        $record = DB::table($table)->where($column, $value)->first();

        if ($record) {
            return $args['rule'] . ',' . $record->{$args['primaryKey'] ?? $this->primaryKey};
        }

        return $args['rule'];
    }
}

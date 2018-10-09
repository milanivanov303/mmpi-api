<?php

namespace App\Helpers\JsonSchema\Filters;

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

    public function validate($value, array $args): bool
    {
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
    protected function isUniqueCheckOnUpdate($rule)
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
    protected function getUniqueRuleOnUpdate($args, $value)
    {
        list($table, $column) = explode(',', last(explode(':', $args['rule'])));

        $record = DB::table($table)->where($column, $value)->first();

        if ($record) {
            return $args['rule'] . ',' . $record->{$args['primaryKey'] ?? $this->primaryKey};
        }

        return $args['rule'];
    }

}

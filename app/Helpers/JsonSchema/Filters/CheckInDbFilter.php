<?php

namespace App\Helpers\JsonSchema\Filters;

use Opis\JsonSchema\IFilter;
use Illuminate\Support\Facades\Validator;

class CheckInDbFilter implements IFilter
{
    public function validate($value, array $args): bool
    {
         return Validator::make(['field' => $value], [
            'field' => $args['rule'],
         ])->passes();
    }
}

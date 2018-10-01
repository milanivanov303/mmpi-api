<?php

namespace App\Helpers\JsonSchema\Errors;

class FiltersError extends Error
{
    public function toString()
    {
        $filter = $this->error->keywordArgs()['filter'];
        return "Property {$this->property} does not match filter {$filter}";
    }
}

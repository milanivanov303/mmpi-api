<?php

namespace App\Helpers\JsonSchema\Errors;

class TypeError extends Error
{
    public function toString()
    {
        $expected = $this->error->keywordArgs()['expected'];
        return "Property {$this->property} should be {$expected}";
    }
}

<?php

namespace App\Helpers\JsonSchema\Errors;

class PatternError extends Error
{
    public function toString()
    {
        $pattern = $this->error->keywordArgs()['pattern'];
        return "Property {$this->property} should match pattern {$pattern}";
    }
}

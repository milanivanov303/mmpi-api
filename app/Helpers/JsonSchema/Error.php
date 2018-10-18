<?php

namespace App\Helpers\JsonSchema;

use Opis\JsonSchema\ValidationError;

class Error
{
    /**
     * Validation error object
     *
     * @var ValidationError
     */
    protected $error;

    public function __construct(ValidationError $error)
    {
        $this->error = $error;
    }

    /**
     * Get property name
     *
     * @return string
     */
    public function getProperty()
    {
        if ($this->error->keyword() === 'required') {
            return $this->error->keywordArgs()['missing'];
        }

        if ($this->error->keyword() === "additionalProperties") {
            return $this->error->keyword();
        }

        return $this->error->dataPointer()[0];
    }

    /**
     * Get capitalized property name
     *
     * @return string
     */
    public function getCapitalizedProperty()
    {
        return ucfirst($this->getProperty());
    }

    /**
     * Check if there is defined message for this error
     *
     * @return bool
     */
    protected function hasDefinedMessage()
    {
        return property_exists($this->error->schema(), '$messages') &&
                property_exists($this->error->schema()->{'$messages'}, $this->error->keyword());
    }

    /**
     * Get error defined message
     *
     * @return string
     */
    protected function getDefinedMessage()
    {
        return $this->error->schema()->{'$messages'}->{$this->error->keyword()};
    }

    /**
     * Get error message
     *
     * @return mixed
     */
    public function getMessage()
    {
        if ($this->hasDefinedMessage()) {
            return $this->getDefinedMessage();
        }
        return $this->toString();
    }

    /**
     * Convert error object to string
     *
     * @return string
     */
    protected function toString()
    {
        $methodName = "get" . ucfirst(trim($this->error->keyword(), '$')) . "Error";

        if (method_exists($this, $methodName)) {
            return $this->{$methodName}();
        }
        return "{$this->getCapitalizedProperty()} is invalid";
    }
    
    protected function getRequiredError()
    {
        return "{$this->getCapitalizedProperty()} is required";
    }

    protected function getAdditionalPropertiesError()
    {
        $properties = implode(
            ' and ',
            array_map(function ($error) {
                return $error->dataPointer()[0];
            }, $this->error->subErrors())
        );
        return "Not allowed properties {$properties}";
    }

    protected function getMaxLengthError()
    {
        return "{$this->getCapitalizedProperty()} can have a maximum length of {$this->error->keywordArgs()['max']}";
    }

    protected function getMinLengthError()
    {
        return "{$this->getCapitalizedProperty()} can have a minimum length of {$this->error->keywordArgs()['min']}";
    }

    protected function getFiltersError()
    {
        return "{$this->getCapitalizedProperty()} does not match filter {$this->error->keywordArgs()['filter']}";
    }

    protected function getPatternError()
    {
        return "{$this->getCapitalizedProperty()} should match pattern {$this->error->keywordArgs()['pattern']}";
    }

    protected function getTypeError()
    {
        $expected = $this->error->keywordArgs()['expected'];
        if (is_array($expected)) {
            $expected = implode(' or ', $expected);
        }
        return "{$this->getCapitalizedProperty()} should be {$expected}";
    }

    protected function getFormatError()
    {
        return "{$this->getCapitalizedProperty()} should be valid {$this->error->keywordArgs()['format']}";
    }

    protected function getMaximumError()
    {
        return "{$this->getCapitalizedProperty()} should be greater or equal {$this->error->keywordArgs()['max']}";
    }
    
    protected function getMinimumError()
    {
        return "{$this->getCapitalizedProperty()} should be lower or equal {$this->error->keywordArgs()['min']}";
    }

    protected function getExclusiveMinimumError()
    {
        return "{$this->getCapitalizedProperty()} should be lower then {$this->error->keywordArgs()['min']}";
    }

    protected function getExclusiveMaximumError()
    {
        return "{$this->getCapitalizedProperty()} should be greater then {$this->error->keywordArgs()['max']}";
    }
}

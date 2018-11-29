<?php

namespace Modules\Core\Helpers\JsonSchema;

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
        if ($this->error->keyword() === "additionalProperties") {
            return $this->error->keyword();
        }

        $property = $this->error->dataPointer() ?? [];

        if ($this->error->keyword() === 'required') {
            $property[] = $this->error->keywordArgs()['missing'];
        }

        return  implode('.', $property);
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
        return sprintf(
            $this->error->schema()->{'$messages'}->{$this->error->keyword()},
            $this->error->data()
        );
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
        return "{$this->getProperty()} is invalid";
    }

    protected function getRequiredError()
    {
        return "{$this->getProperty()} is required";
    }

    protected function getAdditionalPropertiesError()
    {
        $properties = implode(
            ' and ',
            array_map(function ($error) {
                return implode('.', $error->dataPointer());
            }, $this->error->subErrors())
        );
        return "Not allowed properties {$properties}";
    }

    protected function getMaxLengthError()
    {
        return "{$this->getProperty()} can have a maximum length of {$this->error->keywordArgs()['max']}";
    }

    protected function getMinLengthError()
    {
        return "{$this->getProperty()} can have a minimum length of {$this->error->keywordArgs()['min']}";
    }

    protected function getFiltersError()
    {
        return "{$this->getProperty()} does not match filter {$this->error->keywordArgs()['filter']}";
    }

    protected function getPatternError()
    {
        return "{$this->getProperty()} should match pattern {$this->error->keywordArgs()['pattern']}";
    }

    protected function getTypeError()
    {
        $expected = $this->error->keywordArgs()['expected'];
        if (is_array($expected)) {
            $expected = implode(' or ', $expected);
        }
        return "{$this->getProperty()} should be {$expected}";
    }

    protected function getFormatError()
    {
        return "{$this->getProperty()} should be valid {$this->error->keywordArgs()['format']}";
    }

    protected function getMaximumError()
    {
        return "{$this->getProperty()} should be greater or equal {$this->error->keywordArgs()['max']}";
    }

    protected function getMinimumError()
    {
        return "{$this->getProperty()} should be lower or equal {$this->error->keywordArgs()['min']}";
    }

    protected function getExclusiveMinimumError()
    {
        return "{$this->getProperty()} should be lower then {$this->error->keywordArgs()['min']}";
    }

    protected function getExclusiveMaximumError()
    {
        return "{$this->getProperty()} should be greater then {$this->error->keywordArgs()['max']}";
    }

    protected function getAllOfError()
    {
        $messages = [];

        foreach ($this->error->subErrors() as $error) {
            $error = new self($error);
            $messages[$error->getProperty()] = $error->getMessage();
        }

        return $messages;
    }
}

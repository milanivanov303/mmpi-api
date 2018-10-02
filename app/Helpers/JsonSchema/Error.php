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
     * Create instance
     *
     * @param ValidationError $error
     * @return \self
     */
    public static function create(ValidationError $error)
    {
        return new self($error);
    }

    /**
     * Get property name
     *
     * @return string
     */
    protected function getProperty()
    {
        return $this->error->dataPointer()[0];
    }

    /**
     * Check if there is defined message for this error
     *
     * @return bool
     */
    protected function hasDefinedMessage()
    {
        return property_exists($this->error->schema(), 'messages') &&
                property_exists($this->error->schema()->messages, $this->error->keyword());
    }

    /**
     * Get error defined message
     *
     * @return string
     */
    protected function getDefinedMessage()
    {
        return $this->error->schema()->messages->{$this->error->keyword()};
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
        var_dump($this->error);
        return "{$this->getProperty()} is invalid";
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
        return "{$this->getProperty()} should be {$this->error->keywordArgs()['expected']}";
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
}

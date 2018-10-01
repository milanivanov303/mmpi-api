<?php

namespace App\Helpers\JsonSchema\Errors;

use Opis\JsonSchema\ValidationError;

class Error
{
    /**
     * Opis validation error
     *
     * @var ValidationError
     */
    protected $error;

    /**
     * Property name
     * 
     * @var string
     */
    protected $property;

    /**
     * Error message
     * 
     * @var mixed
     */
    protected $message;

    public function __construct(ValidationError $error) {
        $this->error    = $error;
        $this->property = $error->dataPointer()[0];

        if ($this->hasDefinedMessage()) {
            $this->message = $this->getDefinedMessage();
        } else {
            $this->message = $this->toString();
        }
    }

    protected function hasDefinedMessage()
    {
        return property_exists($this->error->schema(), 'messages') &&
                property_exists($this->error->schema()->messages, $this->error->keyword());
    }

    protected function getDefinedMessage()
    {
        return $this->error->schema()->messages->{$this->error->keyword()};
    }

    public function getMessage()
    {
        return $this->message;
    }
}

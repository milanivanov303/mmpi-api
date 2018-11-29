<?php

namespace Core\Exceptions;

class FileException extends \Exception
{

    public function __construct($message = '', $code = 0, $previous = null)
    {
        if (empty($message)) {
            $message = 'File not found';
        }

        parent::__construct($message, $code, $previous);
    }
}

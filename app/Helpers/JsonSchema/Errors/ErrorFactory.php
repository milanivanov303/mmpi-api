<?php

namespace App\Helpers\JsonSchema\Errors;

use Opis\JsonSchema\ValidationError;

class ErrorFactory
{

    public static function create (ValidationError $error)
    {
        switch ($error->keyword()) {
            case '$filters':
                return new FiltersError($error);
            break;
            case 'type':
                return new TypeError($error);
            break;
            case 'pattern':
                return new PatternError($error);
            break;
            default:
                return new Error($error);
            break;
        }
    }

}

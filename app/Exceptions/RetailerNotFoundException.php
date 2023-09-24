<?php

namespace App\Exceptions;

use Exception;

class RetailerNotFoundException extends Exception
{
    public function __construct($message = "Retailer not found.", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

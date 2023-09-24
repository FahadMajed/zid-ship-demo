<?php

namespace App\Exceptions;

use Exception;

class NoAvailableCourierException extends Exception
{
    public function __construct($message = "No available courier.", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

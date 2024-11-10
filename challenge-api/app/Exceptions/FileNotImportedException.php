<?php

namespace App\Exceptions;

use Exception;

class FileNotImportedException extends Exception
{
    public function __construct(string $message = "The file has not been fully imported yet.", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

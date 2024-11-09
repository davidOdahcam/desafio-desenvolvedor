<?php

namespace App\Exceptions;

use Exception;

class FileUploadException extends Exception
{
    public function __construct(string $message = "Error uploading the file.", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

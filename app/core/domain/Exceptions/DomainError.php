<?php

namespace App\core\domain\Exceptions;

use RuntimeException;

class DomainError extends RuntimeException
{
    public function __construct(string $message = "")
    {
        parent::__construct($message);
    }
}

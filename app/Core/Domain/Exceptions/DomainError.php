<?php

namespace App\Core\Domain\Exceptions;

use RuntimeException;

class DomainError extends RuntimeException
{
    public function __construct(string $message = "")
    {
        parent::__construct($message);
    }
}

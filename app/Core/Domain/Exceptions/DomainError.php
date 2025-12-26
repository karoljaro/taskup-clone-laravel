<?php

namespace App\Core\Domain\Exceptions;

use RuntimeException;
abstract class DomainError extends RuntimeException
{
    public function __construct(string $message = "")
    {
        parent::__construct($message);
    }

    abstract public function group(): DomainExceptionGroup;
}

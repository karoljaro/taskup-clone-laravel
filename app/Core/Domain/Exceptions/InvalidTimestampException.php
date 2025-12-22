<?php

namespace App\Core\Domain\Exceptions;

class InvalidTimestampException extends DomainError
{
    public function __construct(string $message = "The provided timestamp is invalid.")
    {
        parent::__construct($message);
    }
}

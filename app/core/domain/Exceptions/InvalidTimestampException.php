<?php

namespace App\core\domain\Exceptions;

class InvalidTimestampException extends DomainError
{
    public function __construct(string $message = "The provided timestamp is invalid.")
    {
        parent::__construct($message);
    }
}

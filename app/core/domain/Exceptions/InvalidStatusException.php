<?php

namespace App\core\domain\Exceptions;

final class InvalidStatusException extends DomainError
{
    public function __construct(string $message = "The provided status is invalid.")
    {
        parent::__construct($message);
    }
}


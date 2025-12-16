<?php

namespace App\core\domain\Exceptions;

final class InvalidIdException extends DomainError
{
    public function __construct(string $message = "The provided ID is invalid.")
    {
        parent::__construct($message);
    }
}

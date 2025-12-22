<?php

namespace App\Core\domain\Exceptions;

final class InvalidDescriptionException extends DomainError
{
    public function __construct(string $message = "The provided description is invalid.")
    {
        parent::__construct($message);
    }
}


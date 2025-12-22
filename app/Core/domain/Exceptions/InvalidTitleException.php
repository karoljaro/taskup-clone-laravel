<?php

namespace App\Core\domain\Exceptions;

final class InvalidTitleException extends DomainError
{
    public function __construct(string $message = "The provided title is invalid.")
    {
        parent::__construct($message);
    }
}


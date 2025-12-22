<?php

namespace App\Core\Domain\Exceptions;

final class InvalidTitleException extends DomainError
{
    public function __construct(string $message = "The provided title is invalid.")
    {
        parent::__construct($message);
    }
}


<?php

namespace App\Core\Domain\Exceptions;

final class InvalidUsernameException extends DomainError
{
    public function __construct(string $message = "The provided username is invalid.")
    {
        parent::__construct($message);
    }

    public function group(): DomainExceptionGroup
    {
        return DomainExceptionGroup::INVALID_INPUT;
    }
}


<?php

namespace App\Core\Domain\Exceptions;

final class InvalidPasswordException extends DomainError
{
    public function __construct(string $message = "The provided password is invalid.")
    {
        parent::__construct($message);
    }

    public function group(): DomainExceptionGroup
    {
        return DomainExceptionGroup::INVALID_INPUT;
    }
}


<?php

namespace App\Core\Domain\Exceptions;

final class InvalidEmailException extends DomainError
{
    public function __construct(string $message = "The provided email is invalid.")
    {
        parent::__construct($message);
    }

    public function group(): DomainExceptionGroup
    {
        return DomainExceptionGroup::INVALID_INPUT;
    }
}

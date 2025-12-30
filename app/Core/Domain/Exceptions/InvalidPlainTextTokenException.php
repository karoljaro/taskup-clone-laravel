<?php

namespace App\Core\Domain\Exceptions;

final class InvalidPlainTextTokenException extends DomainError
{
    public function __construct(string $message = "The provided plain text token is invalid.")
    {
        parent::__construct($message);
    }

    public function group(): DomainExceptionGroup
    {
        return DomainExceptionGroup::INVALID_INPUT;
    }
}



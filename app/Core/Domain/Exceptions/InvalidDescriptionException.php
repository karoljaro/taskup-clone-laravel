<?php

namespace App\Core\Domain\Exceptions;

final class InvalidDescriptionException extends DomainError
{
    public function __construct(string $message = "The provided description is invalid.")
    {
        parent::__construct($message);
    }

    public function group(): DomainExceptionGroup
    {
        return DomainExceptionGroup::INVALID_INPUT;
    }
}


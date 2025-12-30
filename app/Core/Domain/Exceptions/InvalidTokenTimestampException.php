<?php

namespace App\Core\Domain\Exceptions;

final class InvalidTokenTimestampException extends DomainError
{
    public function __construct(string $message = "The provided token timestamp is invalid.")
    {
        parent::__construct($message);
    }

    public function group(): DomainExceptionGroup
    {
        return DomainExceptionGroup::INVALID_INPUT;
    }
}


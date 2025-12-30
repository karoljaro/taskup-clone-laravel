<?php

namespace App\Core\Domain\Exceptions;

final class TokenNotFoundException extends DomainError
{
    public function __construct(string $message = "The requested token was not found.")
    {
        parent::__construct($message);
    }

    public function group(): DomainExceptionGroup
    {
        return DomainExceptionGroup::NOT_FOUND;
    }
}


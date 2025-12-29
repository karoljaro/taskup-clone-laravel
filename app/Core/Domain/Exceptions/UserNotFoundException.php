<?php

namespace App\Core\Domain\Exceptions;

final class UserNotFoundException extends DomainError
{
    public function __construct(string $message = "User not found.")
    {
        parent::__construct($message);
    }

    public function group(): DomainExceptionGroup
    {
        return DomainExceptionGroup::NOT_FOUND;
    }
}


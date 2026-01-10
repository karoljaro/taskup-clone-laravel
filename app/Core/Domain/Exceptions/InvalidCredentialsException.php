<?php

namespace App\Core\Domain\Exceptions;

/**
 * InvalidCredentialsException - thrown when email or password is incorrect during login.
 */
final class InvalidCredentialsException extends DomainError
{
    public function __construct(string $message = "Invalid email or password.")
    {
        parent::__construct($message);
    }

    public function group(): DomainExceptionGroup
    {
        return DomainExceptionGroup::INVALID_INPUT;
    }
}


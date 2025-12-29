<?php

namespace App\Core\Domain\Validation;

use App\Core\Domain\Entities\User;
use App\Core\Domain\Exceptions\DomainError;
use App\Core\Domain\Exceptions\InvalidEmailException;
use App\Core\Domain\Exceptions\InvalidIdException;
use App\Core\Domain\Exceptions\InvalidTimestampException;
use App\Core\Domain\Exceptions\InvalidUsernameException;

class UserInvariantValidation
{
    /**
     * Validates input props for user creation
     *
     * @throws DomainError When validation fails
     */
    public static function validateCreateProps(
        string $id,
        string $username,
        string $email,
        string $plainPassword
    ): void
    {
        SharedBusinessValidation::validateId($id);
        UserBusinessValidation::validateUsername($username);
        UserBusinessValidation::validateEmail($email);
        UserBusinessValidation::validatePassword($plainPassword);
    }

    /**
     * Validates input props for user update (non-null values must be valid)
     *
     * @throws DomainError When validation fails
     */
    public static function validateUpdateProps(
        ?string $username = null,
        ?string $email = null,
        ?string $password = null
    ): void
    {
        if ($username !== null) {
            UserBusinessValidation::validateUsername($username);
        }

        if ($email !== null) {
            UserBusinessValidation::validateEmail($email);
        }

        if ($password !== null) {
            UserBusinessValidation::validatePassword($password);
        }
    }

    /**
     * Validates user entity invariants after creation
     *
     * @throws DomainError When invariants are violated
     */
    public static function validateCreatedUser(User $user): void
    {
        // Verify user has required properties
        if ($user->getId() === null) {
            throw new InvalidIdException("User ID cannot be null");
        }

        if (empty($user->getUsername())) {
            throw new InvalidUsernameException("User username cannot be empty");
        }

        if (empty($user->getEmail())) {
            throw new InvalidEmailException("User email cannot be empty");
        }

        if ($user->getCreatedAt() === null) {
            throw new InvalidTimestampException("User createdAt cannot be null");
        }
    }
}

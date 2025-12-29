<?php

namespace App\Core\Domain\Validation;

use App\Core\Domain\Exceptions\InvalidEmailException;
use App\Core\Domain\Exceptions\InvalidPasswordException;
use App\Core\Domain\Exceptions\InvalidUsernameException;

class UserBusinessValidation
{
    private const int USERNAME_MIN_LENGTH = 3;
    private const int USERNAME_MAX_LENGTH = 50;
    private const int EMAIL_MAX_LENGTH = 255;
    private const int PASSWORD_MIN_LENGTH = 8;

    /**
     * Validates username format and length constraints
     *
     * @throws InvalidUsernameException When username is invalid
     */
    public static function validateUsername(string $username): void
    {
        $username = trim($username);

        if (empty($username)) {
            throw new InvalidUsernameException("Username cannot be empty.");
        }

        if (strlen($username) < self::USERNAME_MIN_LENGTH) {
            throw new InvalidUsernameException(
                "Username must be at least " . self::USERNAME_MIN_LENGTH . " characters long."
            );
        }

        if (strlen($username) > self::USERNAME_MAX_LENGTH) {
            throw new InvalidUsernameException(
                "Username cannot be longer than " . self::USERNAME_MAX_LENGTH . " characters."
            );
        }

        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            throw new InvalidUsernameException(
                "Username can only contain letters, numbers, underscores, and hyphens."
            );
        }
    }

    /**
     * Validates email format and length constraints
     *
     * @throws InvalidEmailException When email is invalid
     */
    public static function validateEmail(string $email): void
    {
        $email = trim($email);

        if (empty($email)) {
            throw new InvalidEmailException("Email cannot be empty.");
        }

        if (strlen($email) > self::EMAIL_MAX_LENGTH) {
            throw new InvalidEmailException(
                "Email cannot be longer than " . self::EMAIL_MAX_LENGTH . " characters."
            );
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException("Invalid email format: " . $email);
        }
    }

    /**
     * Validates plain password strength requirements
     *
     * @throws InvalidPasswordException When password is too weak
     */
    public static function validatePassword(string $plainPassword): void
    {
        if (strlen($plainPassword) < self::PASSWORD_MIN_LENGTH) {
            throw new InvalidPasswordException(
                "Password must be at least " . self::PASSWORD_MIN_LENGTH . " characters long."
            );
        }
    }
}

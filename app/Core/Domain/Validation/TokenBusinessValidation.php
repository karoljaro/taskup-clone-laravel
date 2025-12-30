<?php

namespace App\Core\Domain\Validation;

use App\Core\Domain\Exceptions\InvalidPlainTextTokenException;
use App\Core\Domain\Exceptions\InvalidTokenTimestampException;
use DateTimeImmutable;

class TokenBusinessValidation
{
    private const int MIN_TOKEN_LENGTH = 32;
    private const int MAX_TOKEN_LENGTH = 500;

    /**
     * Validates plain text token format and length
     */
    public static function validatePlainTextToken(string $token): void
    {
        $token = trim($token);

        if ($token === '') {
            throw new InvalidPlainTextTokenException("Token cannot be empty.");
        }

        $length = strlen($token);
        if ($length < self::MIN_TOKEN_LENGTH) {
            throw new InvalidPlainTextTokenException(
                "Token must be at least " . self::MIN_TOKEN_LENGTH . " characters long."
            );
        }

        if ($length > self::MAX_TOKEN_LENGTH) {
            throw new InvalidPlainTextTokenException(
                "Token cannot exceed " . self::MAX_TOKEN_LENGTH . " characters."
            );
        }

        // Token should contain only alphanumeric, hyphens and underscores
        if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $token)) {
            throw new InvalidPlainTextTokenException(
                "Token can only contain alphanumeric characters, hyphens and underscores."
            );
        }
    }

    /**
     * Validates token expiration timestamp
     */
    public static function validateExpiresAt(?DateTimeImmutable $expiresAt): void
    {
        if ($expiresAt === null) {
            return;
        }

        $now = new DateTimeImmutable();
        if ($expiresAt < $now) {
            throw new InvalidTokenTimestampException(
                "Token expiration time cannot be in the past."
            );
        }
    }
}


<?php

namespace App\Core\Domain\Validation;

use App\Core\Domain\Entities\Token;
use App\Core\Domain\VO\TokenId;
use App\Core\Domain\VO\UserId;
use DateTimeImmutable;

class TokenInvariantValidation
{
    /**
     * Validates properties for token creation
     */
    public static function validateCreateProps(
        string $id,
        UserId $userId,
        string $plainTextToken,
        ?DateTimeImmutable $expiresAt = null
    ): void
    {
        SharedBusinessValidation::validateId($id);
        SharedBusinessValidation::validateId($userId->value());
        TokenBusinessValidation::validatePlainTextToken($plainTextToken);
        TokenBusinessValidation::validateExpiresAt($expiresAt);
    }

    /**
     * Validates properties for token reconstruction from persistence
     */
    public static function validateReconstructProps(
        TokenId $id,
        UserId $userId,
        string $plainTextToken,
        ?DateTimeImmutable $expiresAt,
        bool $isRevoked,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $lastUsedAt
    ): void
    {
        TokenBusinessValidation::validatePlainTextToken($plainTextToken);
        TokenBusinessValidation::validateExpiresAt($expiresAt);
        SharedBusinessValidation::validateId($id);
        SharedBusinessValidation::validateId($userId);
        SharedBusinessValidation::validateTimeStamp($createdAt);
        SharedBusinessValidation::validateTimeStamp($lastUsedAt);
        SharedBusinessValidation::validateUpdatedAt($createdAt, $lastUsedAt);
    }

    /**
     * Validates reconstructed token entity
     */
    public static function validateReconstructedToken(Token $token): void
    {
        SharedBusinessValidation::validateId($token->getId()->value());
        SharedBusinessValidation::validateTimeStamp($token->getCreatedAt());
        SharedBusinessValidation::validateTimeStamp($token->getLastUsedAt());
        SharedBusinessValidation::validateUpdatedAt($token->getCreatedAt(), $token->getLastUsedAt());
    }
}


<?php

namespace App\Persistence\Mappers;

use App\Core\Domain\Entities\Token;
use App\Core\Domain\VO\TokenId;
use App\Core\Domain\VO\UserId;
use Laravel\Sanctum\PersonalAccessToken;

class TokenMapper
{
    /**
     * Map Sanctum token to domain Token entity.
     * Note: plainTextToken is empty string as Sanctum stores only hash in database.
     * Real token is only available immediately after creation.
     */
    public static function toDomain(PersonalAccessToken $sanctumToken): Token
    {
        return Token::reconstruct(
            id: TokenId::create((string)$sanctumToken->id),
            userId: new UserId($sanctumToken->tokenable_id),
            plainTextToken: '',
            expiresAt: $sanctumToken->expires_at
                ? new \DateTimeImmutable($sanctumToken->expires_at)
                : null,
            isRevoked: (bool)$sanctumToken->revoked,
            createdAt: new \DateTimeImmutable($sanctumToken->created_at),
            lastUsedAt: $sanctumToken->last_used_at
                ? new \DateTimeImmutable($sanctumToken->last_used_at)
                : new \DateTimeImmutable($sanctumToken->created_at)
        );
    }
}

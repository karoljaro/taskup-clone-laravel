<?php

namespace App\Persistence\Repositories;

use App\Core\Domain\Entities\Token;
use App\Core\Domain\Exceptions\TokenNotFoundException;
use App\Core\Domain\Repositories\TokenRepository;
use App\Core\Domain\VO\TokenId;
use App\Core\Domain\VO\UserId;
use App\Persistence\Mappers\TokenMapper;
use Laravel\Sanctum\PersonalAccessToken;

final class EloquentTokenRepository implements TokenRepository
{
    /**
     * Save or update a token (updates revoked and last_used_at status).
     *
     * @throws TokenNotFoundException If token does not exist
     */
    public function save(Token $token): void
    {
        $sanctumToken = PersonalAccessToken::find($token->getId()->value());

        if ($sanctumToken === null) {
            throw new TokenNotFoundException($token->getId());
        }

        // Use forceFill to bypass guarded attributes
        $sanctumToken->forceFill([
            'revoked' => $token->isRevoked(),
            'last_used_at' => $token->getLastUsedAt(),
        ])->save();
    }

    /**
     * Find token by ID or throw exception.
     */
    public function findById(TokenId $id): Token
    {
        try {
            $sanctumToken = PersonalAccessToken::findOrFail($id->value());
            return TokenMapper::toDomain($sanctumToken);
        } catch(\Throwable) {
            throw new TokenNotFoundException($id);
        }
    }

    /**
     * Get token by plain text value.
     * Note: Sanctum hashes tokens, so this searches by hash.
     */
    public function getByPlainTextToken(string $plainTextToken): Token
    {
        try {
            $hash = hash('sha256', $plainTextToken);
            $sanctumToken = PersonalAccessToken::where('token', $hash)->firstOrFail();
            return TokenMapper::toDomain($sanctumToken);
        } catch(\Throwable) {
            throw new TokenNotFoundException('token');
        }
    }

    /**
     * Get all tokens for a user.
     */
    public function getByUserId(UserId $userId): array
    {
        $sanctumTokens = PersonalAccessToken::where('tokenable_id', $userId->value())
            ->where('tokenable_type', 'App\\Persistence\\Eloquent\\UserEloquentModel')
            ->get();

        return $sanctumTokens->map(fn($token) => TokenMapper::toDomain($token))->toArray();
    }

    /**
     * Delete token by ID.
     */
    public function deleteById(TokenId $id): void
    {
        try {
            PersonalAccessToken::where('id', $id->value())->deleteOrFail();
        } catch(\Throwable) {
            throw new TokenNotFoundException($id);
        }
    }
}

<?php

namespace App\Persistence\Services;

use App\Core\Application\Ports\TokenGenerator;
use App\Core\Domain\VO\UserId;
use App\Persistence\Eloquent\UserEloquentModel;
use DateTimeImmutable;

final readonly class SanctumTokenGenerator implements TokenGenerator
{
    /**
     * Generate a new personal access token for a user.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function generate(UserId $userId, DateTimeImmutable $expiresAt): string
    {
        $eloquentUser = UserEloquentModel::findOrFail($userId->value());

        $token = $eloquentUser->createToken(
            name: 'api-token',
            abilities: ['*'],
            expiresAt: $expiresAt
        );

        return $token->plainTextToken;
    }
}

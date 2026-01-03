<?php

namespace App\Core\Application\Commands;

use App\Core\Domain\Repositories\TokenRepository;
use App\Core\Domain\VO\UserId;

/**
 * Revoke all tokens for a specific user.
 */
final readonly class RevokeAllUserTokensCommand
{
    public function __construct(
        private TokenRepository $tokenRepo,
    ) {}

    /**
     * Execute the command to revoke all user's tokens.
     *
     * @param UserId $userId The user ID whose tokens to revoke
     */
    public function execute(UserId $userId): void {
        $tokens = $this->tokenRepo->getByUserId($userId);

        // TODO: Later convert it to unit of work pattern to optimize multiple saves
        foreach ($tokens as $token) {
            $token->revoke();
            $this->tokenRepo->save($token);
        }
    }
}

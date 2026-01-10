<?php

namespace App\Core\Application\Commands;

use App\Core\Application\Ports\UnitOfWork;
use App\Core\Domain\VO\UserId;
use Throwable;

/**
 * Revoke all tokens for a specific user.
 * Uses UnitOfWork to manage transaction with automatic rollback on failure.
 * All tokens are revoked atomically - either all succeed or all rollback.
 */
final readonly class RevokeAllUserTokensCommand
{
    public function __construct(
        private UnitOfWork $uow,
    ) {}

    /**
     * Execute the command to revoke all user's tokens.
     * Rolls back on any failure.
     *
     * @param UserId $userId The user ID whose tokens to revoke
     * @return void
     * @throws Throwable If revoke fails
     */
    public function execute(UserId $userId): void {
        try {
            $this->uow->begin();

            $tokens = $this->uow->tokens()->getByUserId($userId);

            foreach ($tokens as $token) {
                $token->revoke();
                $this->uow->tokens()->save($token);
            }

            $this->uow->commit();
        } catch (Throwable $e) {
            $this->uow->rollback();
            throw $e;
        }
    }
}

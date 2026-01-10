<?php

namespace App\Core\Application\Commands;

use App\Core\Application\Ports\UnitOfWork;
use App\Core\Domain\Exceptions\TokenNotFoundException;
use App\Core\Domain\VO\TokenId;
use Throwable;

/**
 * Revoke a specific token by its ID.
 * Uses UnitOfWork to manage transaction with automatic rollback on failure.
 */
final readonly class RevokeTokenCommand
{
    public function __construct(
        private UnitOfWork $uow,
    ) {}

    /**
     * Execute the command to revoke a token.
     * Rolls back on any failure.
     *
     * @param TokenId $tokenId The token ID to revoke
     * @return void
     * @throws TokenNotFoundException If token not found
     * @throws Throwable If revoke fails
     */
    public function execute(TokenId $tokenId): void {
        try {
            $this->uow->begin();

            $token = $this->uow->tokens()->findById($tokenId);

            $token->revoke();

            $this->uow->tokens()->save($token);

            $this->uow->commit();
        } catch (Throwable $e) {
            $this->uow->rollback();
            throw $e;
        }
    }
}

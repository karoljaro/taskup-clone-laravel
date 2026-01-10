<?php

namespace App\Core\Application\Commands;

use App\Core\Application\Ports\UnitOfWork;
use App\Core\Domain\Exceptions\TokenNotFoundException;
use App\Core\Domain\VO\TokenId;
use Throwable;

/**
 * Update the last used timestamp for a token.
 * Uses UnitOfWork to manage transaction with automatic rollback on failure.
 */
final readonly class UpdateTokenLastUsedCommand
{
    public function __construct(
        private UnitOfWork $uow,
    ) {}

    /**
     * Execute the command to update token's last used time.
     * Rolls back on any failure.
     *
     * @param TokenId $tokenId The token ID to update
     * @return void
     * @throws TokenNotFoundException If token not found
     * @throws Throwable If update fails
     */
    public function execute(TokenId $tokenId): void {
        try {
            $this->uow->begin();

            $token = $this->uow->tokens()->findById($tokenId);

            $token->updateLastUsedAt();

            $this->uow->tokens()->save($token);

            $this->uow->commit();
        } catch (Throwable $e) {
            $this->uow->rollback();
            throw $e;
        }
    }
}

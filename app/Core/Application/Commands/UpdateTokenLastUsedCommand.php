<?php

namespace App\Core\Application\Commands;

use App\Core\Domain\Exceptions\TokenNotFoundException;
use App\Core\Domain\Repositories\TokenRepository;
use App\Core\Domain\VO\TokenId;

/**
 * Update the last used timestamp for a token.
 */
final readonly class UpdateTokenLastUsedCommand
{
    public function __construct(
        private TokenRepository $tokenRepo,
    ) {}

    /**
     * Execute the command to update token's last used time.
     *
     * @param TokenId $tokenId The token ID to update
     * @throws TokenNotFoundException If token not found
     */
    public function execute(TokenId $tokenId): void {
        $token = $this->tokenRepo->findById($tokenId);

        $token->updateLastUsedAt();

        $this->tokenRepo->save($token);
    }
}

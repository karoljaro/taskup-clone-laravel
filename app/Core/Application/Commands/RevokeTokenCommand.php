<?php

namespace App\Core\Application\Commands;

use App\Core\Domain\Exceptions\TokenNotFoundException;
use App\Core\Domain\Repositories\TokenRepository;
use App\Core\Domain\VO\TokenId;

/**
 * Revoke a specific token by its ID.
 */
final readonly class RevokeTokenCommand
{
    public function __construct(
        private TokenRepository $tokenRepo,
    ) {}

    /**
     * Execute the command to revoke a token.
     *
     * @param TokenId $tokenId The token ID to revoke
     * @throws TokenNotFoundException If token not found
     */
    public function execute(TokenId $tokenId): void {
        $token = $this->tokenRepo->findById($tokenId);

        $token->revoke();

        $this->tokenRepo->save($token);
    }
}

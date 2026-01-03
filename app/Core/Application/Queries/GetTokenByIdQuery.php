<?php

namespace App\Core\Application\Queries;

use App\Core\Domain\Entities\Token;
use App\Core\Domain\Exceptions\TokenNotFoundException;
use App\Core\Domain\Repositories\TokenRepository;
use App\Core\Domain\VO\TokenId;

/**
 * Retrieve a token by its ID.
 */
final readonly class GetTokenByIdQuery
{
    public function __construct(
        private TokenRepository $tokenRepo
    ) {}

    /**
     * Execute the query to get a token by ID.
     *
     * @param TokenId $tokenId The token ID to retrieve
     * @return Token The token entity
     * @throws TokenNotFoundException If token not found
     */
    public function execute(TokenId $tokenId): Token {
        return $this->tokenRepo->findById($tokenId);
    }
}

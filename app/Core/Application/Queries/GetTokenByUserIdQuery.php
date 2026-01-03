<?php

namespace App\Core\Application\Queries;

use App\Core\Domain\Entities\Token;
use App\Core\Domain\Repositories\TokenRepository;
use App\Core\Domain\VO\UserId;

/**
 * Retrieve all tokens for a specific user.
 */
final readonly class GetTokenByUserIdQuery
{
    public function __construct(
        private TokenRepository $tokenRepo
    ) {}

    /**
     * Execute the query to get all tokens for a user.
     *
     * @param UserId $userId The user ID
     * @return list<Token> Array of user's tokens
     */
    public function execute(UserId $userId): array {
        return $this->tokenRepo->getByUserId($userId);
    }
}

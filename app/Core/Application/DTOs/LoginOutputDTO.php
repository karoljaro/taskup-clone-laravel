<?php

namespace App\Core\Application\DTOs;

use App\Core\Domain\Entities\Token;
use App\Core\Domain\Entities\User;

readonly class LoginOutputDTO
{
    /**
     * @param User $user The authenticated user entity
     * @param Token $token The access token created for this login session
     * @param string $plainTextToken The plain text token (only transmitted once during login)
     */
    public function __construct(
        public User $user,
        public Token $token,
        public string $plainTextToken
    ) {}
}


<?php

namespace App\Core\Application\DTOs;

use App\Core\Domain\Entities\Token;
use App\Core\Domain\Entities\User;

readonly class RegisterOutputDTO
{
    /**
     * @param User $user The newly created user entity
     * @param Token $token The access token created for the user
     * @param string $plainTextToken The plain text token (only transmitted once during registration)
     */
    public function __construct(
        public User $user,
        public Token $token,
        public string $plainTextToken
    ) {}
}


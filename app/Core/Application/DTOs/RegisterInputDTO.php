<?php

namespace App\Core\Application\DTOs;

readonly class RegisterInputDTO
{
    /**
     * @param string $username The username for the new account
     * @param string $email The email address for the new account
     * @param string $plainPassword The plain text password (will be hashed during user creation)
     */
    public function __construct(
        public string $username,
        public string $email,
        public string $plainPassword
    ) {}
}


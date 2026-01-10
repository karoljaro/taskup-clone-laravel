<?php

namespace App\Core\Application\DTOs;

use DateTimeImmutable;

readonly class LoginInputDTO
{
    /**
     * @param string $email The user's email address
     * @param string $plainPassword The plain text password
     * @param bool $rememberMe Whether to extend the token expiration (30 days vs 24 hours)
     */
    public function __construct(
        public string $email,
        public string $plainPassword,
        public bool $rememberMe = false
    ) {}
}


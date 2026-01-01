<?php

namespace App\Core\Application\DTOs;

readonly class CreateUserInputDTO
{
    public function __construct(
        public string $username,
        public string $email,
        public string $plainPassword
    )
    {}
}

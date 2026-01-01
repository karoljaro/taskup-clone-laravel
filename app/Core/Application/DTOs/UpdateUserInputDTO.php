<?php

namespace App\Core\Application\DTOs;

readonly class UpdateUserInputDTO
{
    public function __construct(
        public ?string $username = null,
        public ?string $email = null,
        public ?string $plainPassword = null
    )
    {}
}

<?php

namespace App\Core\Application\DTOs;

use App\Core\Domain\VO\UserId;
use DateTimeImmutable;

readonly class CreateTokenInputDTO
{
    /**
     * @param UserId $userId
     * @param string $plainTextToken
     * @param DateTimeImmutable|null $expiresAt
     */
    public function __construct(
        public UserId $userId,
        public string $plainTextToken,
        public ?DateTimeImmutable $expiresAt = null
    ) {}
}

<?php

namespace App\Core\Domain\Entities;

use App\Core\Domain\Validation\TokenInvariantValidation;
use App\Core\Domain\VO\TokenId;
use App\Core\Domain\VO\UserId;
use DateTimeImmutable;

final class Token
{
    private bool $isRevoked = false;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $lastUsedAt;
    private function __construct(
        private readonly TokenId $id,
        private readonly UserId $userId,
        private readonly string $plainTextToken,
        private readonly ?DateTimeImmutable $expiresAt = null,
    ) {}

//  ==========================[ FACTORY ] ==========================

    public static function create(
        string $id,
        UserId $userId,
        string $plainTextToken,
        ?DateTimeImmutable $expiresAt = null
    ): self
    {
        TokenInvariantValidation::validateCreateProps($id, $userId, $plainTextToken, $expiresAt);

        $token = new self(
            TokenId::create($id),
            $userId,
            $plainTextToken,
            $expiresAt
        );

        $token->createdAt = new DateTimeImmutable();
        $token->lastUsedAt = $token->createdAt;

        return $token;
    }

    public static function reconstruct(
        TokenId $id,
        UserId $userId,
        string $plainTextToken,
        ?DateTimeImmutable $expiresAt,
        bool $isRevoked,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $lastUsedAt
    ): self
    {
        TokenInvariantValidation::validateReconstructProps(
            $id,
            $userId,
            $plainTextToken,
            $expiresAt,
            $isRevoked,
            $createdAt,
            $lastUsedAt
        );

        $token = new self(
            $id,
            $userId,
            $plainTextToken,
            $expiresAt
        );
        $token->isRevoked = $isRevoked;
        $token->createdAt = $createdAt;
        $token->lastUsedAt = $lastUsedAt;

        return $token;
    }

//   ==========================[ BEHAVIORS ]==========================

    public function isValid(): bool
    {
        if ($this->isRevoked) {
            return false;
        }

        if ($this->isExpired()) {
            return false;
        }

        return true;
    }

    public function isExpired(): bool
    {
        if ($this->expiresAt === null) {
            return false;
        }

        $now = new DateTimeImmutable();
        return $now > $this->expiresAt;
    }

    public function revoke(): void
    {
        $this->isRevoked = true;
    }

    public function updateLastUsedAt(): void
    {
        $this->lastUsedAt = new DateTimeImmutable();
    }

    public function matches(string $plainTextToken): bool
    {
        return hash_equals($this->plainTextToken, $plainTextToken);
    }

//  ==========================[ GETTERS ]==========================

    public function getId(): TokenId
    {
        return $this->id;
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getPlainTextToken(): string
    {
        return $this->plainTextToken;
    }

    public function getExpiresAt(): ?DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function isRevoked(): bool
    {
        return $this->isRevoked;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getLastUsedAt(): DateTimeImmutable
    {
        return $this->lastUsedAt;
    }

    public function __toString(): string
    {
        return $this->plainTextToken;
    }
}

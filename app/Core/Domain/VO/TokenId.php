<?php

namespace App\Core\Domain\VO;

use App\Core\Domain\Validation\SharedBusinessValidation;

final readonly class TokenId
{
    private function __construct(
        private string $value
    ) {}

    public static function create(string $id): self
    {
        self::validate($id);
        return new self($id);
    }

    public function fromDatabase(string $id): self
    {
        return new self($id);
    }

    private static function validate(string $id): void
    {
        SharedBusinessValidation::validateId($id);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(TokenId $other): bool
    {
        return $this->value === $other->value();
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

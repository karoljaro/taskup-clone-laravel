<?php

namespace App\Core\Domain\VO;

use App\Core\Domain\Exceptions\InvalidEmailException;
use App\Core\Domain\Validation\UserBusinessValidation;

final readonly class Email
{
    private function __construct(
        private string $value
    ) {}

    public static function create(string $email): self
    {
        self::validate($email);
        return new self(trim($email));
    }

    public static function fromDatabase(string $email): self
    {
        return new self($email);
    }

    private static function validate(string $email): void
    {
        UserBusinessValidation::validateEmail($email);
    }

    public function normalized(): string
    {
        return strtolower(trim($this->value));
    }

    public function equals(Email $other): bool
    {
        return $this->normalized() === $other->normalized();
    }

    public function value(): string
    {
        return $this->value;
    }
}

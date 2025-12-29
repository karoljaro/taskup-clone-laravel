<?php

namespace App\Core\Domain\VO;

final readonly class HashedPassword
{
    private function __construct(
        private string $hash
    ) {}

    public static function fromPlain(string $plainPassword): self {
        return new self(password_hash($plainPassword, PASSWORD_BCRYPT));
    }

    public static function fromHash(string $hash): self {
        return new self($hash);
    }

    public function verify(string $plainPassword): bool {
        return password_verify($plainPassword, $this->hash);
    }

    public function equals(HashedPassword $other): bool {
        return $this->hash === $other->hash;
    }

    public function value(): string {
        return $this->hash;
    }
}

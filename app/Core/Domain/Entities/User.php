<?php

namespace App\Core\Domain\Entities;

use App\Core\Domain\Validation\UserInvariantValidation;
use App\Core\Domain\VO\Email;
use App\Core\Domain\VO\HashedPassword;
use App\Core\Domain\VO\UserId;
use DateTimeImmutable;

final class User
{
    private bool $emailVerified = false;
    private ?DateTimeImmutable $emailVerifiedAt = null;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    private function __construct(
        private readonly UserId $id,
        private string $username,
        private Email $email,
        private HashedPassword $password
    ) {
        $now = new DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

//  ==========================[ FACTORY ] ==========================
    public static function create(string $id, string $username, string $email, string $plainPassword): self
    {
        UserInvariantValidation::validateCreateProps(
            id: $id,
            username: $username,
            email: $email,
            plainPassword: $plainPassword
        );

        $user = new self(
            new UserId($id),
            $username,
            Email::create($email),
            HashedPassword::fromPlain($plainPassword)
        );

        UserInvariantValidation::validateCreatedUser($user);
        return $user;
    }

    public function update(
        ?string $username = null,
        ?string $email = null,
        ?string $plainPassword = null
    ): void
    {
        $newUsername = $username ?? $this->username;
        $newEmail = $email ? Email::create($email) : $this->email;
        $newPassword = $plainPassword ? HashedPassword::fromPlain($plainPassword) : $this->password;

        UserInvariantValidation::validateUpdateProps(
            username: $newUsername,
            email: $newEmail->value(),
            password: $newPassword->value()
        );

        $changeDetected = false;

        if ($newUsername !== $this->username) {
            $this->username = $newUsername;
            $changeDetected = true;
        }

        if (!$newEmail->equals($this->email)) {
            $this->email = $newEmail;
            $this->emailVerified = false;
            $this->emailVerifiedAt = null;
            $changeDetected = true;
        }

        if (!$newPassword->equals($this->password)) {
            $this->password = $newPassword;
            $changeDetected = true;
        }

        if ($changeDetected) {
            $this->updatedAt = new DateTimeImmutable();
        }
    }

    public static function reconstruct(
        UserId $id,
        string $username,
        string $email,
        string $hashedPassword,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
        bool $emailVerified = false,
        ?DateTimeImmutable $verifiedAt = null,
    ): self
    {
        $user = new self(
            id: $id,
            username: $username,
            email: Email::create($email),
            password: HashedPassword::fromHash($hashedPassword)
        );

        $user->emailVerified = $emailVerified;
        $user->emailVerifiedAt = $verifiedAt;
        $user->createdAt = $createdAt;
        $user->updatedAt = $updatedAt;

        return $user;
    }

//  ==========================[ BEHAVIORS ]==========================

    public function verifyPassword(string $plainPassword): bool
    {
        return $this->password->verify($plainPassword);
    }

    public function verifyEmail(): void
    {
        $this->emailVerified = true;
        $this->emailVerifiedAt = new DateTimeImmutable();
    }

//  ==========================[ GETTERS ]==========================

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): HashedPassword
    {
        return $this->password;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function isEmailVerified(): bool
    {
        return $this->emailVerified;
    }

    public function getEmailVerifiedAt(): ?DateTimeImmutable
    {
        return $this->emailVerifiedAt;
    }
}

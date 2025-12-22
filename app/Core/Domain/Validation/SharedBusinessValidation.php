<?php

namespace App\Core\Domain\Validation;

use App\Core\Domain\Exceptions\InvalidIdException;
use App\Core\Domain\Exceptions\InvalidTimestampException;
use DateTimeImmutable;

class SharedBusinessValidation
{
    public static function validateId(string $id): void
    {
        if (empty($id)) {
            throw new InvalidIdException("ID cannot be empty.");
        }

        if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $id)) {
            throw new InvalidIdException("Invalid UUID format");
        }
    }

    public static function validateTimeStamp(DateTimeImmutable $timestamp): void
    {
        $now = new DateTimeImmutable();

        if ($timestamp > $now) {
            throw new InvalidTimestampException("Timestamp cannot be in the future.");
        }
    }

    public static function validateUpdatedAt(DateTimeImmutable $createdAt, DateTimeImmutable $updatedAt): void
    {
        if ($updatedAt < $createdAt) {
            throw new InvalidTimestampException("UpdatedAt cannot be earlier than CreatedAt.");
        }
    }
}

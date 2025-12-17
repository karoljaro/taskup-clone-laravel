<?php

namespace App\core\domain\Validation;

use App\core\domain\Exceptions\InvalidIdException;
use App\core\domain\Exceptions\InvalidTimestampException;
use DateTimeImmutable;

class SharedBusinessValidation
{
    public static function validateId(string $id): void
    {
        if (empty($id)) {
            throw new InvalidIdException("ID cannot be empty.");
        }

        if (!preg_match('/^[a-f0-9]{32}$/', $id)) {
            throw new InvalidIdException("ID must be a valid 32-character hexadecimal string.");
        }
    }

    public static function validateTimeStamp(DateTimeImmutable $timestamp): void
    {
        if ($timestamp <= 0) {
            throw new InvalidTimestampException("Timestamp must be a positive integer.");
        }

        if ($timestamp > time()) {
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

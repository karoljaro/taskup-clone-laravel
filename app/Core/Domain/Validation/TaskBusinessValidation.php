<?php

namespace App\Core\Domain\Validation;

use App\Core\Domain\Enums\TaskStatus;
use App\Core\Domain\Exceptions\InvalidDescriptionException;
use App\Core\Domain\Exceptions\InvalidTitleException;

class TaskBusinessValidation
{
    public static function validateTitle(string $title): void
    {
        $title = trim($title);

        if ($title === '') {
            throw new InvalidTitleException("Title cannot be empty.");
        }

        if (mb_strlen($title) < 3) {
            throw new InvalidTitleException("Title must be at least 3 characters long.");
        }

        if (mb_strlen($title) > 255) {
            throw new InvalidTitleException("Title cannot exceed 255 characters.");
        }

        if ($title !== strip_tags($title)) {
            throw new InvalidTitleException("Title cannot contain HTML tags.");
        }
    }

    public static function validateDescription(string $description): void
    {
        if (strlen($description) > 2000) {
            throw new InvalidDescriptionException("Description cannot exceed 2000 characters.");
        }
    }

    public static function validateStatus(TaskStatus $status): void
    {
        // Since TaskStatus is an enum, this check is somewhat redundant,
    }
}
